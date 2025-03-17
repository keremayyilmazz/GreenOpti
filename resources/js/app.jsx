import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', function() {
    initializeMap();
});

// Global değişkenler
let map = null;
let markers = {};
let routeLayer = null;
let isMapInitialized = false;

// Karbon emisyon faktörleri (kg CO2/ton-km)
const CARBON_FACTORS = {
    'land': 0.096,  // TIR
    'sea': 0.021,   // Gemi
    'rail': 0.028,  // Tren
    'air': 0.602    // Uçak
};

// Varsayılan yük miktarı (ton)
const DEFAULT_CARGO = 20;

// Marker ikonlarını tanımla
const startIcon = L.icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

const endIcon = L.icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

const factoryIcon = L.icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});
// Harita başlatma fonksiyonu
// Harita başlatma fonksiyonu
// Harita başlatma fonksiyonu
function initializeMap() {
    if (isMapInitialized) return;

    const mapElement = document.getElementById('map');
    if (!mapElement) return;

    try {
        map = L.map('map').setView([39.9334, 32.8597], 6);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        if (window.factories) {
            window.factories.forEach(function(factory) {
                markers[factory.id] = L.marker([factory.latitude, factory.longitude])
                    .addTo(map)
                    .bindPopup(factory.name);
            });
        }

        isMapInitialized = true;
    } catch (error) {
        console.error('Harita başlatılırken hata:', error);
    }
}

// Event listener'ları güncelle
document.addEventListener('DOMContentLoaded', function() {
    let attempts = 0;
    const maxAttempts = 5;
    
    function tryInitMap() {
        if (attempts >= maxAttempts) {
            console.error('Harita başlatılamadı: Maksimum deneme sayısına ulaşıldı');
            return;
        }
        
        if (!initializeMap()) {
            attempts++;
            setTimeout(tryInitMap, 500);
        }
    }
    
    tryInitMap();

    // Form submit olayını dinle
    const routeForm = document.getElementById('routeForm');
    if (routeForm) {
        routeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (isMapInitialized && map) {
                calculateRoute();
            } else {
                alert('Harita henüz yüklenemedi. Lütfen sayfayı yenileyin.');
            }
        });
    }
});

// calculateRoute fonksiyonunda kontrolleri güncelle
function calculateRoute() {
    if (!isMapInitialized || !map) {
        console.error('Harita henüz yüklenmedi!');
        alert('Harita henüz yüklenemedi. Lütfen sayfayı yenileyin.');
        return;
    }
function calculateRoute() {
    if (!isMapInitialized || !map) {
        console.error('Harita henüz yüklenmedi!');
        alert('Harita henüz yüklenemedi. Lütfen sayfayı yenileyin.');
        return;
    }



// Window load event'i ekle
window.addEventListener('load', function() {
    if (!isMapInitialized || !map) {
        setTimeout(initializeMap, 500);
    }
});

// Fabrika yönetimi için fonksiyonlar
function initFactoryManagement() {
    loadFactories();
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
    const marker = L.marker(latlng, { icon: factoryIcon }).addTo(map);
    const markerId = Date.now();
    markers[markerId] = marker;

    const popupContent = `
        <div>
            <input type="text" placeholder="Fabrika adı" value="${name}" class="factory-name">
            <button onclick="window.saveFactory(this)">Kaydet</button>
            <button onclick="window.removeMarker('${markerId}')">Sil</button>
        </div>
    `;

    marker.bindPopup(popupContent);
    marker.openPopup();
}

function saveFactory(button) {
    const popup = button.closest('.leaflet-popup-content');
    const name = popup.querySelector('.factory-name').value;
    const marker = markers[Object.keys(markers)[Object.keys(markers).length - 1]];
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

function removeMarker(id) {
    if (markers[id]) {
        map.removeLayer(markers[id]);
        delete markers[id];
    }
}

function getRouteColor(vehicleType) {
    switch(vehicleType) {
        case 'land': return '#FF5722';  // Turuncu
        case 'sea': return '#2196F3';   // Mavi
        case 'air': return '#9C27B0';   // Mor
        case 'rail': return '#4CAF50';  // Yeşil
        default: return '#000000';      // Siyah
    }
}

function getVehicleTypeName(type) {
    const names = {
        'land': 'Kara Yolu',
        'sea': 'Deniz Yolu',
        'rail': 'Demir Yolu',
        'air': 'Hava Yolu'
    };
    return names[type] || type;
}

function calculateCarbonEmission(distance, vehicleType) {
    return CARBON_FACTORS[vehicleType] * distance * DEFAULT_CARGO;
}

function getEnvironmentalImpact(emission) {
    const trees = Math.ceil(emission / 23);
    return `Bu emisyonu dengelemek için ${trees} ağaç dikilmesi gerekir.`;
}

function suggestAlternatives(currentType, distance, emission) {
    let suggestions = [];
    for (let [type, factor] of Object.entries(CARBON_FACTORS)) {
        if (type !== currentType) {
            const altEmission = factor * distance * DEFAULT_CARGO;
            const saving = emission - altEmission;
            if (saving > 0) {
                suggestions.push({ type, saving });
            }
        }
    }
    return suggestions.sort((a, b) => b.saving - a.saving);
}

function updateRouteInfo(data, vehicleType) {
    try {
        const distanceElement = document.getElementById('distance');
        const durationElement = document.getElementById('duration');
        const carbonElement = document.getElementById('carbon');
        const warningElement = document.getElementById('carbon-warning');
        const alternativesElement = document.getElementById('alternatives-list');

        if (distanceElement) {
            distanceElement.textContent = data.distance.toFixed(2) + ' km';
        }

        if (durationElement) {
            durationElement.textContent = data.duration.toFixed(2) + ' saat';
        }

        if (carbonElement) {
            const emission = calculateCarbonEmission(data.distance, vehicleType);
            carbonElement.innerHTML = `
                ${emission.toFixed(2)} kg CO2
                <br>
                <small class="text-muted">${getEnvironmentalImpact(emission)}</small>
            `;

            // Alternatif önerileri göster
            if (vehicleType === 'air' || emission > 1000) {
                const alternatives = suggestAlternatives(vehicleType, data.distance, emission);
                if (alternatives.length > 0 && warningElement && alternativesElement) {
                    warningElement.style.display = 'block';
                    alternativesElement.innerHTML = `
                        <ul class="mb-0">
                            ${alternatives.map(alt => `
                                <li>${getVehicleTypeName(alt.type)}: 
                                    ${alt.saving.toFixed(2)} kg daha az CO2 emisyonu
                                </li>
                            `).join('')}
                        </ul>
                    `;
                }
            } else if (warningElement) {
                warningElement.style.display = 'none';
            }
        }
    } catch (error) {
        console.error('Rota bilgileri güncellenirken hata:', error);
    }
}

function calculateRoute() {
    if (!isMapInitialized || !map) {
        console.error('Harita henüz yüklenmedi!');
        setTimeout(initializeMap, 100); // Haritayı tekrar başlatmayı dene
        return;
    }













    console.log('Hesaplama başladı...');
    
    // Form değerlerini al
    const sourceId = parseInt(document.getElementById('source_factory_id').value);
    const destId = parseInt(document.getElementById('destination_factory_id').value);
    const vehicleType = document.getElementById('vehicle_type').value;

    if (!sourceId || !destId) {
        alert('Lütfen başlangıç ve hedef fabrikalarını seçin');
        return;
    }

    // Markerları güncelle
    Object.values(markers).forEach(marker => marker.setIcon(factoryIcon));
    
    if (markers[sourceId]) {
        markers[sourceId].setIcon(startIcon);
        markers[sourceId].bindPopup('<b>Başlangıç Noktası</b>').openPopup();
    }
    
    if (markers[destId]) {
        markers[destId].setIcon(endIcon);
        markers[destId].bindPopup('<b>Varış Noktası</b>').openPopup();
    }

    // Mevcut rotayı temizle
    if (routeLayer && map) {
        map.removeLayer(routeLayer);
        routeLayer = null;
    }

    // AJAX çağrısı
    fetch('/calculate-route', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            source_factory_id: sourceId,
            destination_factory_id: destId,
            vehicle_type: vehicleType
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Sunucu hatası: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success && map) {
            // Yeni rotayı çiz
            routeLayer = L.geoJSON(data.geometry, {
                style: {
                    color: getRouteColor(vehicleType),
                    weight: 3,
                    opacity: 0.8
                }
            }).addTo(map);

            // Haritayı rotaya odakla
            map.fitBounds(routeLayer.getBounds(), { padding: [50, 50] });

            // Rota bilgilerini güncelle
            updateRouteInfo(data, vehicleType);
        } else {
            throw new Error(data.message || 'Rota hesaplanırken bir hata oluştu');
        }
    })
    .catch(error => {
        console.error('Hata:', error);
        alert(error.message || 'Rota hesaplanırken bir hata oluştu');
    });
}

// Global olarak fonksiyonları dışa aktar
window.initializeMap = initializeMap;

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- App JS -->
@vite(['resources/js/app.js'])