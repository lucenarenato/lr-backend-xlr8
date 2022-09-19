<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exemplo de mapa Leaflet</title>

    <!-- Link de importação do CSS do mapa Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
        integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
        crossorigin="" />

    <!-- Link de importação do script javascript do mapa Leaflet (ELE TEM QUE SER APÓS A IMPORTAÇÃO DO CSS) -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
        integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
        crossorigin=""></script>

    <script type="text/javascript" src="https://code.jquery.com/jquery-2.2.3.min.js"></script>

    <!-- aqui é o height do mapa. Vc seta o tamnho que vc quiser.    -->
    <style>
        #meuMapa {
            height: 900px;
        }
    </style>

</head>

<body>
    {{ csrf_field() }}
    <input id="lat" type="text" value="38.7071">
    <input id="long" type="text" value="-9.13549">
    <input id="km" type="text" value="100">
    <button id="btnbusca" onclick="buscar()">Buscar</button>
    <!-- aqui você define a div e dá um nome id pra ela. Aí esse nome id você referencia nas chamadas de criação do mapa logo abaixo no script -->
    <div id="meuMapa"></div>


    <script>

function buscar() {

        const latitude = document.getElementById('lat');
        const longitude = document.getElementById('long');
        const km = document.getElementById('km');
        var latLongAtual = [latitude.value, longitude.value]
        var zoomDoMapa = 10
        var tipo = latitude.value.toString();
        //console.log("tipo retorna: " + typeof(tipo));

        var retorno;
        $.ajax(
            {
                type: 'POST',
                url: 'api/view',
                dataType: 'json',
                data: {
                    latitude: latitude.value.toString(),
                    longitude: longitude.value.toString(),
                    _token: "{{ csrf_token() }}",
                },
                crossDomain: true,
                async: false,
                success: function (data) {
                    retorno = data;
                }
            });
        //console.log(retorno);
        var estacoes = new Array();

        for (var i = 0; i < retorno.length; i++) {
            //console.log(km.value);
            if(parseFloat(retorno[i].KM) < km.value) {
                lugar = [
                    retorno[i].Hotel,
                    retorno[i].Latitude,
                    retorno[i].Longitude
                ]
                estacoes.push(lugar);
            }
        }

        // console.log(estacoes);

        // var estacoes = [
        //     ["Santana", -16.684373773224998, -49.288728002421365],
        //     ["Walfran (Famoso)", -16.664708603789567, -49.25700959473245],
        //     ["Fernando (Corujão)", -16.71878791111704, -49.32033860251477],
        //     ["? (Pancadão)", -16.65808528837226, -49.25291650169644],
        //     ["Silvano (Caçador)", -16.70837962894523, -49.30573328238385],
        //     ["? (Papa xingú)", -16.65941022502678, -49.25604828294081],
        //     ["Amarildo (Feiticeiro)", -16.620279392084527, -49.219269443170255],
        //     ["? (Dragão branco)", -16.568114620202806, -49.29552085476284],
        //     ["Camilo (Bola de fogo)", -16.692658304484684, -49.21469325700581],
        //     ["Diogo (Homem das sombras)", -16.738940359636153, -49.258498951182325],
        //     ["Yury (Belgato)", -16.763510088350504, -49.29112032644621],
        //     ["Krauser (08)", -16.590630525473593, -49.32293751704396],
        //     ["Tony (Coyote)", -16.786366796065032, -49.27790046329399],
        //     ["Aimoré (?)", -16.69234892313377, -49.30965766149846],
        //     ["Célio (?)", -16.65377502064373, -49.314135325485005],
        // ];

        var antenaIcon = L.icon({
            iconUrl: 'img/bed.png',
            iconSize: [24, 24], // size of the icon
            iconAnchor: [2, 54], // point of the icon which will correspond to marker's location
            popupAnchor: [15, -50] // point from which the popup should open relative to the iconAnchor
        });

        /*no set view vc coloca a latitude, longitude e depois o zoom*/
        var mymap = L.map('meuMapa').setView(latLongAtual, zoomDoMapa);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(mymap);


        /*Esta linha é para adicionar os marcadores no mapa.*/
        for (var i = 0; i < estacoes.length; i++) {
            marker = new L.marker([estacoes[i][1], estacoes[i][2]], {icon: antenaIcon})
                .bindPopup(estacoes[i][0]) /*adiciona o popup com o respectivo valor*/
                .addTo(mymap);
        }

    }
    </script>

</body>

</html>
