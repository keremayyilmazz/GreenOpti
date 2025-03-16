import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Harita başlatma
let map;
let markers = [];

document.addEventListener('DOMContentLoaded', function() {
    // Harita varsa başlat
    if(document.getElementById('map')) {
        initMap();
    }
});

function initMap() {
    // Türkiye'nin merkezi koordinatları
    map = L.map('map').setView([39.9334, 32.8597], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Mevcut fabrikaları haritaya ekle
    loadFactories();

    // Haritaya tıklama olayı
    map.on('click', function(e) {
        addMarker(e.latlng);
    });
}

function loadFactories() {
    fetch('/factories')
        .then(response => response.json())
        .then(factories => {
            factories.forEach(factory => {
                addMarker({
                    lat: factory.latitude,
                    lng: factory.longitude
                }, factory.name);
            });
        });
}

function addMarker(latlng, name = '') {
    const marker = L.marker(latlng).addTo(map);
    markers.push(marker);

    // Popup içeriği
    const popupContent = `
        <div>
            <input type="text" placeholder="Fabrika adı" value="${name}" class="factory-name">
            <button onclick="saveFactory(this)">Kaydet</button>
            <button onclick="removeMarker(${markers.length - 1})">Sil</button>
        </div>
    `;

    marker.bindPopup(popupContent);
    marker.openPopup();
}

function saveFactory(button) {
    const popup = button.closest('.leaflet-popup-content');
    const name = popup.querySelector('.factory-name').value;
    const marker = markers[markers.length - 1];
    const latlng = marker.getLatLng();

    fetch('/factories', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            name: name,
            latitude: latlng.lat,
            longitude: latlng.lng
        })
    })
    .then(response => response.json())
    .then(data => {
        marker.closePopup();
    });
}

function removeMarker(index) {
    if (markers[index]) {
        map.removeLayer(markers[index]);
        markers.splice(index, 1);
    }
}
