<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rota Hesaplama</title>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        #map {
            height: 600px;
            width: 100%;
            border-radius: 10px;
        }
        .calculation-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .result-box {
            background-color: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .navbar {
            margin-bottom: 20px;
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .route-info {
            font-size: 1.1rem;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Lojistik Yönetim Sistemi</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('profile.edit') }}">Profil</a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link">Çıkış Yap</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @include('calculation-form')

                <div id="map"></div>

                <div class="result-box">
                    <h4><i class="fas fa-info-circle"></i> Rota Bilgileri</h4>
                    <div class="route-info">
                        <p><strong>Mesafe:</strong> <span id="distance">-</span></p>
                        <p><strong>Tahmini Süre:</strong> <span id="duration">-</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Fabrika verilerini JavaScript'e aktar
        var factories = @json($factories);

        // Harita başlatma
        var map = L.map('map').setView([39.9334, 32.8597], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Marker ikonlarını tanımla
        var startIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        var endIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        var factoryIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        // Tüm markerları tutacak bir obje
        var markers = {};

        // Fabrikaları haritaya ekle
        factories.forEach(function(factory) {
            markers[factory.id] = L.marker([factory.latitude, factory.longitude], {
                icon: factoryIcon
            }).addTo(map).bindPopup(factory.name);
        });

        // Form submit olayını dinle
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('routeForm').addEventListener('submit', function(e) {
                e.preventDefault();
                calculateRoute();
            });
        });

        function getRouteColor(vehicleType) {
            switch(vehicleType) {
                case 'land': return '#FF5722';  // Turuncu
                case 'sea': return '#2196F3';   // Mavi
                case 'air': return '#9C27B0';   // Mor
                case 'rail': return '#4CAF50';  // Yeşil
                default: return '#000000';      // Siyah
            }
        }

        function calculateRoute() {
            console.clear(); // Önceki logları temizle
            console.log('Hesaplama başladı...');
            
            // Tüm markerları mavi yap
            Object.values(markers).forEach(marker => {
                marker.setIcon(factoryIcon);
            });
            console.log('Tüm markerlar maviye çevrildi');

            var sourceFactory = factories.find(f => f.id === parseInt(document.getElementById('source_factory_id').value));
            var destinationFactory = factories.find(f => f.id === parseInt(document.getElementById('destination_factory_id').value));
            var vehicleType = document.getElementById('vehicle_type').value;
            
            console.log('Seçilen fabrikalar:', {
                kaynak: sourceFactory?.name,
                hedef: destinationFactory?.name,
                tasimaTipi: vehicleType
            });

            if (!sourceFactory || !destinationFactory) {
                alert('Lütfen başlangıç ve hedef fabrikalarını seçin');
                return;
            }

            // Seçilen fabrikaların markerlarını güncelle
            try {
                markers[sourceFactory.id].setIcon(startIcon);
                console.log('Başlangıç noktası yeşile çevrildi:', sourceFactory.name);
                
                markers[destinationFactory.id].setIcon(endIcon);
                console.log('Hedef noktası kırmızıya çevrildi:', destinationFactory.name);

                // Popup içeriklerini güncelle
                markers[sourceFactory.id].setPopupContent('<b>Başlangıç:</b> ' + sourceFactory.name);
                markers[destinationFactory.id].setPopupContent('<b>Hedef:</b> ' + destinationFactory.name);
            } catch (error) {
                console.error('Marker güncellenirken hata:', error);
            }

            // AJAX çağrısı
            fetch('/calculate-route', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    source_factory_id: sourceFactory.id,
                    destination_factory_id: destinationFactory.id,
                    vehicle_type: vehicleType
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'Bir hata oluştu');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (data.geometry) {
                        if (window.routeLayer) {
                            map.removeLayer(window.routeLayer);
                        }
                        window.routeLayer = L.geoJSON(data.geometry, {
                            style: function(feature) {
                                return {
                                    color: getRouteColor(vehicleType),
                                    weight: 3,
                                    opacity: 0.8
                                };
                            }
                        }).addTo(map);

                        map.fitBounds(window.routeLayer.getBounds(), {
                            padding: [50, 50]
                        });
                    }

                    document.getElementById('distance').textContent = data.distance.toFixed(2) + ' km';
                    document.getElementById('duration').textContent = data.duration.toFixed(2) + ' saat';
                } else {
                    alert(data.message || 'Rota hesaplanırken bir hata oluştu');
                }
            })
            .catch(error => {
                console.error('Hata:', error);
                alert(error.message || 'Rota hesaplanırken bir hata oluştu');
            });
        }
    </script>
</body>
</html>