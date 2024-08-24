document.addEventListener('DOMContentLoaded', () => {
    mapboxgl.accessToken = 'pk.eyJ1Ijoibmlrb2xhaTEyMjIiLCJhIjoiY2x6d3pva281MGx6ODJrczJhaTJ4M2RmYyJ9.0sJ2ZGR2xpEza2j370y3rQ';

    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v12',
        center: [122.8313, 10.5338],
        zoom: 12
    });

    map.on('click', (e) => {
        const coordinates = e.lngLat;
        
        if (window.currentMarker) {
            window.currentMarker.remove();
        }

        window.currentMarker = new mapboxgl.Marker()
            .setLngLat([coordinates.lng, coordinates.lat])
            .addTo(map);

        // Display coordinates in the console or you can add it to the UI
        console.log('Longitude:', coordinates.lng, 'Latitude:', coordinates.lat);

        // Optionally, display the coordinates in an alert or on the webpage
        alert(`Longitude: ${coordinates.lng}, Latitude: ${coordinates.lat}`);
    });
});