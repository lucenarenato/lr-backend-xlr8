<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use NumberFormatter;
use Exception;
use App\Models\Hotel;

class TesteController extends Controller
{
    /**
     * @param string $label
     * @param string|null $value
     * @return void
     * //
     */
    private static function verifyIsNull(string $label, string $value = null)
    {
        if (is_null($value) || empty($label))
            throw new Exception(sprintf(self::ERROR_IS_REQUIRED, $label));
    }

    /**
     * @param float $amount
     * @param string $locale
     * @param string $currency
     * @param bool $showSymbol
     * @return string
     * //
     */
    private static function currencyConvert(float $amount, string $locale = 'pt', string $currency = 'EUR', bool $showSymbol = false): string
    {
        $fmt = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        if (!$showSymbol)
            $fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');

        $fmt_amount = $fmt->formatCurrency($amount, $currency);
        if (intl_is_failure($fmt->getErrorCode())) {
            throw new Exception(self::ERROR_FORMATTER);
        }
        return $fmt_amount . (!$showSymbol ? " {$currency}" : null);
    }

    /**
     * @return void
     * //
     */
    private static function responseList(array $data, string $separator = " &bull; ")
    {
        $formatedData = [];
        foreach ($data as $item) {
            $formatedData[] = sprintf("%s, %s, %s", $item['hotel'], $item['km'] . " KM", self::currencyConvert($item['price']));
        }
        echo $separator . implode($separator, $formatedData);
    }

    /**
     * @return void
     */
    private static function response($data)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }
}
