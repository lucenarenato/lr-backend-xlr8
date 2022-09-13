<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use NumberFormatter;
use Exception;
use App\Models\Hotel;

class SearchController extends Controller
{

     /**
     * Calculate the distance between two coordinates
     *
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float
     */
    private static function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2)
    {

        $pi80 = M_PI / 180;
        $lat1 *= $pi80;
        $lon1 *= $pi80;
        $lat2 *= $pi80;
        $lon2 *= $pi80;

        $r = 6372.797; // mean radius of Earth in km
        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;
        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $km = $r * $c;

        //echo '<br/>'.$km;
        return $km;
    }

    /**
     * Get a list of hotels
     *
     * @param float $latitude
     * @param float $longitude
     * @param string $orderby
     * @return array
     */
    public function getNearbyHotels(Request $request)
    {
        // Get data from get request
        $url1 = file_get_contents(env('SOURCE1', 'https://xlr8-interview-files.s3.eu-west-2.amazonaws.com/source_1.json'));
        $url2 = file_get_contents(env('SOURCE2', 'https://xlr8-interview-files.s3.eu-west-2.amazonaws.com/source_2.json'));

        $response1 = self::getUrls($url1, $request);
        // dd($response1);
        $response2 = self::getUrls($url2, $request);
        $hotelListFormated = array_merge($response1, $response2);
        return $hotelListFormated;
    }

    /**
     * Get a list of hotels
     * Get orderby “pricepernight” || "proximity"
     * @param float $latitude
     * @param float $longitude
     * @param string $orderby
     * @return array
     */
    public function getUrls($response, $request, $orderby = "proximity")
    {
        //dd($orderby);
        $hotelList = [];
        $response = json_decode($response);

        //dd($response);
        // Fill an array of Hotel Objects with the data retrieved from xlr8 hotels api
        foreach ($response->message as $index => $item) {
            // Check if all field of an item is not null
            if (!($item[0] && $item[1] && $item[2] && $item[3])) {
                continue;
            }
            $hotel = new Hotel($item[0], floatval($item[1]), floatval($item[2]), $item[3]);
            $hotel->setDistance(
                self::calculateDistance($request->latitude, $request->longitude, $hotel->getLatitude(), $hotel->getLongitude())
            );
            array_push($hotelList, $hotel);
        }


        // Check if orderby is "pricepernight" and return the data according "orderBy" choice.
        if ($orderby == "pricepernight") {
            self::orderByPrice($hotelList);
        } else {
            self::orderByDistance($hotelList);
        }

        $hotelListFormated = self::getFormatedHotelList($hotelList);
        return $hotelListFormated;
    }

    private static function getFormatedHotelList(array $hotels)
    {
        $hotelListFormated = [];
        foreach ($hotels as $hotel) {
            array_push($hotelListFormated, "Hotel {$hotel->getName()}, {$hotel->getFormatedDistance()} KM, {$hotel->getPrice()} EUR");
        }
        return $hotelListFormated;
    }

    /**
     * Order Hotel list by most nearby hotels
     *
     * @param array $hotelList
     * @return void
     */
    private static function orderByDistance(array &$hotelList)
    {
        usort($hotelList, function ($hotel1, $hotel2) {
            if ($hotel1->getDistance() === $hotel2->getDistance()) {
                return 0;
            }
            return $hotel1->getDistance() > $hotel2->getDistance() ? 1 : -1;
        });
    }

    /**
     * Order Hotel list by price
     *
     * @param array $hotelList
     * @return void
     */
    private static function orderByPrice(array &$hotelList)
    {
        usort($hotelList, function ($hotel1, $hotel2) {
            if ($hotel1->getPrice() === $hotel2->getPrice()) {
                return 0;
            }
            return $hotel1->getPrice() > $hotel2->getPrice() ? 1 : -1;
        });
    }



    protected function selectedEndpoint()
    {
        $url1 = file_get_contents(env('SOURCE1', 'https://xlr8-interview-files.s3.eu-west-2.amazonaws.com/source_1.json'));
        $url2 = file_get_contents(env('SOURCE2', 'https://xlr8-interview-files.s3.eu-west-2.amazonaws.com/source_2.json'));
        $array = array();
        $array[] = json_decode($url1);
        $array[] = json_decode($url2);

        $selected_endpoint = json_encode($array);
        return $selected_endpoint;
    }
}
