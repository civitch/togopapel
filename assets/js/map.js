import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

export default class Map {
    static init () {
        let map = document.querySelector('#adresse-map-annonce');
        if (map === null) {
            return
        }
        let icon = L.icon({
            iconUrl: '/images/map/marker-icon.png',
        });
        let center = [map.dataset.lat, map.dataset.lng];
        map = L.map('adresse-map-annonce' ).setView(center, 13);
        L.tileLayer(`https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png`, {
            minZoom: 1,
            maxZoom: 13,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        L.marker(center, {icon: icon}).addTo(map)
    }

}


