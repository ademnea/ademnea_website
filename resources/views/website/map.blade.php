<section class="map-section my-1">
    <div class="container">
        <h2 class="text-center">All Apiary Locations</h2>

        <div id="map" style="width: 100%; height: 500px; margin-top: 20px;"></div>
    </div>
</section>

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
            { lat: 0.381111, lng: 32.552852, name: "Mr. Kaddu's Apiary", color: "green" }, // green
            { lat: 0.7071, lng: 30.6500, name: "Apiary 3" },
            { lat: 0.3314, lng: 32.5706, name: "Brand Coffee Farm", color: "green" }, // green
            { lat: 0.7782, lng: 33.0020, name: "Apiary 5" }
        ];

        // Marker icons URLs for colors (Google Maps default icons)
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
