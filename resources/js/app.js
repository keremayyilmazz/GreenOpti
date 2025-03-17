import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

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
function initializeMap() {
    if (isMapInitialized && map) {
        console.log('Harita zaten başlatılmış');
        return;
    }

    const mapElement = document.getElementById('map');
    if (!mapElement) {
        console.error('Harita elementi bulunamadı');
        return;
    }

    try {
        map = L.map('map', {
            dragging: true,
            scrollWheelZoom: true,
            doubleClickZoom: true,
            zoomControl: true
        }).setView([39.9334, 32.8597], 6);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        const path = window.location.pathname;
        
        if (path.includes('factories')) {
            initFactoryManagement();
        } else if (path.includes('calculations')) {
            if (window.factories) {
                window.factories.forEach(function(factory) {
                    markers[factory.id] = L.marker([factory.latitude, factory.longitude], {
                        icon: factoryIcon
                    }).addTo(map).bindPopup(factory.name);
                });
            }
        }

        isMapInitialized = true;
        console.log('Harita başarıyla başlatıldı');
    } catch (error) {
        console.error('Harita başlatılırken hata:', error);
        isMapInitialized = false;
        map = null;
    }
}

// Fabrika yönetimi fonksiyonları
function initFactoryManagement() {
    loadFactories();
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
    });
}

function loadFactories() {
    fetch('/factories/list')
        .then(response => response.json())
        .then(data => {
            data.forEach(factory => {
                markers[factory.id] = L.marker([factory.latitude, factory.longitude], {
                    icon: factoryIcon
                }).addTo(map).bindPopup(factory.name);
            });
        });
}

function saveFactory() {
    const name = document.getElementById('name').value;
    const latitude = document.getElementById('latitude').value;
    const longitude = document.getElementById('longitude').value;

    fetch('/factories', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ name, latitude, longitude })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            markers[data.factory.id] = L.marker([latitude, longitude], {
                icon: factoryIcon
            }).addTo(map).bindPopup(name);
            alert('Fabrika başarıyla eklendi');
        }
    })
    .catch(error => {
        console.error('Hata:', error);
        alert('Fabrika eklenirken bir hata oluştu');
    });
}

function removeMarker(id) {
    if (markers[id]) {
        map.removeLayer(markers[id]);
        delete markers[id];
    }
}

// Rota hesaplama fonksiyonları
function calculateRoute() {
    if (!isMapInitialized || !map) {
        console.error('Harita henüz yüklenmedi!');
        alert('Harita henüz yüklenemedi. Lütfen sayfayı yenileyin.');
        return;
    }

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
    .then(response => response.json())
    .then(data => {
        if (data.success && map) {
            routeLayer = L.geoJSON(data.geometry, {
                style: {
                    color: getRouteColor(vehicleType),
                    weight: 3,
                    opacity: 0.8
                }
            }).addTo(map);

            map.fitBounds(routeLayer.getBounds(), { padding: [50, 50] });
            updateRouteInfo(data, vehicleType);
        } else {
            // Özel hata mesajlarını göster
            const errorMessage = data.message || 'Rota hesaplanırken bir hata oluştu';
            
            // Bootstrap alert ile daha şık bir hata mesajı göster
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-warning alert-dismissible fade show';
            alertDiv.role = 'alert';
            alertDiv.innerHTML = `
                <strong>Uyarı!</strong> ${errorMessage}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <hr>
                <p class="mb-0">Lütfen farklı bir taşıma türü seçin veya rotanızı değiştirin.</p>
            `;
            
            // Alert'i sayfaya ekle
            const container = document.querySelector('.container');
            container.insertBefore(alertDiv, container.firstChild);

            // 5 saniye sonra alert'i otomatik kaldır
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    })
    .catch(error => {
        console.error('Hata:', error);
        alert('Rota hesaplanırken bir hata oluştu. Lütfen tekrar deneyin.');
    });
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

function calculateCarbonEmission(distance, vehicleType) {
    return distance * CARBON_FACTORS[vehicleType] * DEFAULT_CARGO;
}

function getEnvironmentalImpact(emission) {
    if (emission < 100) return 'Düşük çevresel etki';
    if (emission < 500) return 'Orta düzeyde çevresel etki';
    return 'Yüksek çevresel etki';
}

function suggestAlternatives(currentType, distance, currentEmission) {
    return Object.entries(CARBON_FACTORS)
        .filter(([type, factor]) => type !== currentType)
        .map(([type, factor]) => {
            const altEmission = distance * factor * DEFAULT_CARGO;
            return {
                type: type,
                saving: currentEmission - altEmission
            };
        })
        .filter(alt => alt.saving > 0)
        .sort((a, b) => b.saving - a.saving);
}

function getRouteColor(vehicleType) {
    const colors = {
        'land': '#FF0000',  // Kırmızı
        'sea': '#0000FF',   // Mavi
        'rail': '#008000',  // Yeşil
        'air': '#800080'    // Mor
    };
    return colors[vehicleType] || '#000000';
}

function getVehicleTypeName(type) {
    const names = {
        'land': 'Kara Taşımacılığı',
        'sea': 'Deniz Taşımacılığı',
        'rail': 'Demiryolu Taşımacılığı',
        'air': 'Hava Taşımacılığı'
    };
    return names[type] || type;
}

// Event listener'ı ekle
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initializeMap, 100);
});

// Global olarak fonksiyonları dışa aktar
window.saveFactory = saveFactory;
window.removeMarker = removeMarker;
window.calculateRoute = calculateRoute;
window.getRouteColor = getRouteColor;
window.getVehicleTypeName = getVehicleTypeName;