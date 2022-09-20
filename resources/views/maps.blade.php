<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exemplo de mapa Leaflet</title>

     <!-- Fonts -->
     {{-- <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet"> --}}
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
  <!--Bootstrap Icons-->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css">
    <!-- Link de importação do CSS do mapa Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
        integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
        crossorigin="" />

        <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
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
    <!--Navagation Bar-->
  <nav class="navbar navbar-custom" id="fullNavbar">
    <div class="container-fluid">
      <!--Navbar Brand-->
      <div class="navbar-brand mb-0 h1" id="header-image-container">
        <img src="https://laravel.com/img/logotype.min.svg?width=800&height=400&cropmode=none" alt="skyline" id="header-skyline">
    </div>
      <!--Opening Icon/Button-->
      <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fullSidebar" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </nav>

  <!-- Side Bar -->
  <div class="collapse navbar-collapse flex-column p-3 navbar-custom float-end" id="fullSidebar" style="width: 280px;">
    <!--Options of Sidebar-->
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item">
        <a href="/" class="nav-link">Home</a>
      </li>
      <li>
        <a href="#" class="nav-link">Sources</a>
      </li>
      <li>
        <a href="#" class="nav-link">About</a>
      </li>
      <li>
        <a href="#" class="nav-link">Contact</a>
      </li>
    </ul>
  </div>

    <h1 class='text-center header mb-2 pb-1'>Bem vindo a <strong>busca de hospedagens</strong> </h1>
    {{ csrf_field() }}
    <table class="table" style="border: 1px solid; margin-top: 5px; width: 50%">
        <tr style="padding: 5px;">
            <td>
                <label for="floatingInput">Latitude</label>
                <input class="form-control" id="lat" type="text" value="38.7071">
            </td>
            <td>
                <label for="floatingInput">Longitude</label>
                <input class="form-control" id="long" type="text" value="-9.13549">
            </td>
            <td>
                <label for="floatingInput">KM</label>
                <input class="form-control" id="km" type="text" value="100">
            </td>
            <td><br>
                <button class="btn btn-primary" id="btnbusca" onclick="startSpinner()">Buscar</button>
            </td>
            <td><br>
                <img src="img/spinner.gif" id="spinner" style="width: 45px; height: 45px; visibility: hidden;">
            </td>
        </tr>
    </table>

    <!-- aqui você define a div e dá um nome id pra ela. Aí esse nome id você referencia nas chamadas de criação do mapa logo abaixo no script -->
    <div id="meuMapa" style="min-height: 800px;height: 600px;"></div>

    </div>
    <script>
        const latitude = document.getElementById('lat');
        const longitude = document.getElementById('long');
        const km = document.getElementById('km');
        const spinner = document.getElementById('spinner');
        var delayInMilliseconds = 1000;
        var latLongAtual = [latitude.value, longitude.value]
        var zoomDoMapa = 10
        /*no set view vc coloca a latitude, longitude e depois o zoom*/
        var mymap = L.map('meuMapa').setView(latLongAtual, zoomDoMapa);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(mymap);

        var tipo = latitude.value.toString();
        //console.log("tipo retorna: " + typeof(tipo));
        function startSpinner(){
            spinner.style.visibility = 'visible';

            setTimeout(function() {
                buscar();
            }, delayInMilliseconds);
        }
        function buscar() {
                //spinner.style.visibility = 'visible';
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
                            spinner.style.visibility = 'hidden';
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
                //     ["Walfran (Famoso)", -16.664708603789567, -49.25700959473245],
                // ];

                var antenaIcon = L.icon({
                    iconUrl: 'img/bed.png',
                    iconSize: [24, 24], // size of the icon
                    iconAnchor: [2, 54], // point of the icon which will correspond to marker's location
                    popupAnchor: [15, -50] // point from which the popup should open relative to the iconAnchor
                });

                /*Esta linha é para adicionar os marcadores no mapa.*/
                for (var i = 0; i < estacoes.length; i++) {
                    marker = new L.marker([estacoes[i][1], estacoes[i][2]], {icon: antenaIcon})
                        .bindPopup(estacoes[i][0]) /*adiciona o popup com o respectivo valor*/
                        .addTo(mymap);
                }

            }
    </script>
    <!--Bootstrap JS-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
</body>

</html>
