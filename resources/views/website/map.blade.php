<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Adaptive Environmental Monitoring Networks for East Africa</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/logo.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    @include('website.links')
</head>

<body>

    <!-- ======= Top Bar ======= -->
    @include('website.top_bar')

    <!-- ======= Header ======= -->
    @include('website.header')
    <!-- End Header -->

    <main id="main">

        <!-- ======= Breadcrumb / Page Title ======= -->
        <section class="breadcrumbs py-3 bg-light text-center">
            <div class="container">
                <h2>Apiary Locations</h2>
                <p class="text-muted">All registered apiary sites in our network</p>
            </div>
        </section>

        <!-- ======= Map Section ======= -->
        <section class="map-section py-4">
            <div class="container">
                <div id="map" style="width: 100%; height: 500px;"></div>
            </div>
        </section>

    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    @include('website.footer')
    <!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    @include('website.scripts')

    <!-- Load Google Maps JavaScript API -->
    <script async
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps_api_key') }}&callback=initMap">
    </script>

    <script>
        function initMap() {
            // Center of the map
            const center = { lat: 0.3326, lng: 32.5686 };

            // Map initialization
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 9,
                center: center
            });

            // Apiary locations
            const apiaries = [
                { lat: 0.3136, lng: 32.5811, name: "Apiary 1" },
                { lat: 0.381111, lng: 32.552852, name: "Mr. Kaddu's Apiary", color: "green" },
                { lat: 0.7071, lng: 30.6500, name: "Apiary 3" },
                { lat: 0.3314, lng: 32.5706, name: "Brand Coffee Farm", color: "green" },
                { lat: 0.7782, lng: 33.0020, name: "Apiary 5" }
            ];

            const iconBase = "http://maps.google.com/mapfiles/ms/icons/";

            apiaries.forEach(apiary => {
                new google.maps.Marker({
                    position: { lat: apiary.lat, lng: apiary.lng },
                    map: map,
                    title: apiary.name,
                    icon: iconBase + (apiary.color === "green" ? "green-dot.png" : "red-dot.png")
                });
            });
        }
    </script>

</body>

</html>
