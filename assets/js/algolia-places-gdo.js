console.log("9595");
import Places from 'places.js';
import L from "leaflet";
import 'leaflet/dist/leaflet.css';
import 'leaflet/dist/leaflet';
const inputAddress = document.querySelector('#annonce_adresse');

if (inputAddress !== null) {
    let place = Places({
        container: inputAddress,
        language: 'fr',
        countries: ['TG'],
    }).configure({
        type: 'address',
        //aroundLatLngViaIP: false
    });
    place.on('change', e => {
        document.querySelector('#annonce_lat').value = e.suggestion.latlng.lat;
        document.querySelector('#annonce_lng').value = e.suggestion.latlng.lng;
        handleOnChange(e);
    });
    place.on('suggestions', handleOnSuggestions);
    place.on('cursorchanged', handleOnCursorchanged);
    place.on('clear', handleOnClear);

    var map = L.map('map-adresse-annonce', {
        scrollWheelZoom: false,
        zoomControl: false
    });

    var osmLayer = new L.TileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            minZoom: 1,
            maxZoom: 13,
            zoomControl: true,
            attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
        }
    );

    var markers = [];

    map.setView(new L.LatLng(0, 0), 1);
    map.addLayer(osmLayer);
    function handleOnSuggestions(e) {
        markers.forEach(removeMarker);
        markers = [];

        if (e.suggestions.length === 0) {
            map.setView(new L.LatLng(0, 0), 1);
            return;
        }

        e.suggestions.forEach(addMarker);
        findBestZoom();
    }

    function handleOnChange(e) {
        markers
            .forEach(function(marker, markerIndex) {
                if (markerIndex === e.suggestionIndex) {
                    markers = [marker];
                    marker.setOpacity(1);
                    findBestZoom();
                } else {
                    removeMarker(marker);
                }
            });
    }

    function handleOnClear() {
        map.setView(new L.LatLng(0, 0), 1);
        markers.forEach(removeMarker);
    }

    function handleOnCursorchanged(e) {
        markers
            .forEach(function(marker, markerIndex) {
                if (markerIndex === e.suggestionIndex) {
                    marker.setOpacity(1);
                    marker.setZIndexOffset(1000);
                } else {
                    marker.setZIndexOffset(0);
                    marker.setOpacity(0.5);
                }
            });
    }

    function addMarker(suggestion) {
        let icon = L.icon({
            iconUrl: '/images/map/marker-icon.png',
        });
        var marker = L.marker(suggestion.latlng, {icon: icon, opacity: .4});
        marker.addTo(map);
        markers.push(marker);
    }

    function removeMarker(marker) {
        map.removeLayer(marker);
    }

    function findBestZoom() {
        var featureGroup = L.featureGroup(markers);
        map.fitBounds(featureGroup.getBounds().pad(0.5), {animate: false});
    }

}
