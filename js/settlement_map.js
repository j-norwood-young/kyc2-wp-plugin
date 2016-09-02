var mymap = L.map('mapid').setView([midLat, midLng], 13);
mymap.scrollWheelZoom.disable();
L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
    attribution: 'Our own text here',
    maxZoom: 18,
    id: 'mapbox.light',
    accessToken: 'pk.eyJ1IjoiamFzb25ueSIsImEiOiJzNTBLZUpVIn0.lVgz_XShOLK06PVQFbH9Vw'
}).addTo(mymap);

var polygon = L.polyline(shape).addTo(mymap);
polygon.setStyle({ color: "#444", fillColor: "#4d916a", fill: true, fillOpacity: 0.6 });

mymap.fitBounds(polygon.getBounds());