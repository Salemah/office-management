<html>
<head>
    <title>Simple Map</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <style>
        /*
 * Always set the map height explicitly to define the size of the div element
 * that contains the map.
 */
        #map {
            height: 100%;
        }

        /*
         * Optional: Makes the sample page fill the window.
         */
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
<button onclick="getLocation()">Try It</button>
<p id="demo"></p>
<div id="map"></div>
<script src="https://code.jquery.com/jquery-3.6.1.js" integrity="sha256-3zlB5s2uwoUzrXK3BT7AX3FyvojsraNFxCc2vC/7pNI=" crossorigin="anonymous"></script>
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBj0urNG9HYtiisPPwvOa1xgXF8cjsvJlY&callback=initMap&v=weekly" async
    defer
></script>

<script>
    var x = document.getElementById("demo");

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            x.innerHTML = "Geolocation is not supported by this browser.";
        }
    }
    var lat = 0;
    var long = 0;

    function showPosition(position) {
        lat = position.coords.latitude;
        long = position.coords.longitude;
        x.innerHTML = "Latitude: " + position.coords.latitude +
            "<br>Longitude: " + position.coords.longitude;

        console.log(lat, long)
        const myLatLng = { lat: lat, lng: long };
        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 15,
            center: myLatLng,
        });

        new google.maps.Marker({
            position: myLatLng,
            map,
            title: "Hello Rajkot!",
        });

    }

    let url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=23.8716485,90.3858549&sensor=true&key=AIzaSyBj0urNG9HYtiisPPwvOa1xgXF8cjsvJlY';

    var location ;

    $.getJSON(url, function(data) {
        console.log(data.results);
    });
</script>

<script src="https://maps.googleapis.com/maps/api/geocode/json?latlng=23.8716485,90.3858549&sensor=true&key=AIzaSyBj0urNG9HYtiisPPwvOa1xgXF8cjsvJlY" async defer></script>
</body>
</html>
