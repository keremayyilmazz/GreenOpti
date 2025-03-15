@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <style>
        #map { 
            height: 400px; 
            width: 100%; 
            margin-bottom: 20px; 
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .factory-form {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #eee;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Yeni Fabrika Ekle</span>
                    <a href="{{ route('factories.index') }}" class="btn btn-sm btn-secondary">
                        Fabrikalar Listesi
                    </a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div id="map"></div>
                    
                    <form id="factoryForm" class="factory-form">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Fabrika Adı</label>
                            <input type="text" class="form-control" id="name" required>
                            <div class="form-text">Fabrikanın tam adını giriniz.</div>
                        </div>
                        <input type="hidden" id="latitude">
                        <input type="hidden" id="longitude">
                        <input type="hidden" id="address">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Kaydet
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script>
let map;
let marker;

function initMap() {
    // Türkiye'nin merkezi koordinatları
    const turkeyCenter = [39.9334, 32.8597];
    map = L.map('map').setView(turkeyCenter, 6);

    // OpenStreetMap layer ekle
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Arama kontrolü ekle
    const geocoder = L.Control.geocoder({
        defaultMarkGeocode: false
    })
    .on('markgeocode', function(e) {
        const latlng = e.geocode.center;
        placeMarker(latlng);
        map.setView(latlng, 15);
    })
    .addTo(map);

    // Harita tıklama olayını dinle
    map.on('click', function(e) {
        placeMarker(e.latlng);
    });
}

function placeMarker(latlng) {
    if (marker) {
        map.removeLayer(marker);
    }

    marker = L.marker(latlng, {draggable: true}).addTo(map);
    
    // Form alanlarını güncelle
    document.getElementById('latitude').value = latlng.lat;
    document.getElementById('longitude').value = latlng.lng;

    // Reverse geocoding ile adresi al
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latlng.lat}&lon=${latlng.lng}&accept-language=tr`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('address').value = data.display_name;
        })
        .catch(error => {
            console.error('Adres alma hatası:', error);
            document.getElementById('address').value = `${latlng.lat}, ${latlng.lng}`;
        });

    // Marker sürüklendiğinde koordinatları güncelle
    marker.on('dragend', function(e) {
        const pos = e.target.getLatLng();
        document.getElementById('latitude').value = pos.lat;
        document.getElementById('longitude').value = pos.lng;

        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${pos.lat}&lon=${pos.lng}&accept-language=tr`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('address').value = data.display_name;
            })
            .catch(error => {
                console.error('Adres alma hatası:', error);
                document.getElementById('address').value = `${pos.lat}, ${pos.lng}`;
            });
    });
}

// Form gönderme işlemi
document.getElementById('factoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!marker) {
        alert('Lütfen haritadan bir konum seçin!');
        return;
    }

    const data = {
        name: document.getElementById('name').value,
        latitude: document.getElementById('latitude').value,
        longitude: document.getElementById('longitude').value,
        address: document.getElementById('address').value,
        _token: document.querySelector('input[name="_token"]').value
    };

    fetch('/factories', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Fabrika başarıyla eklendi!');
            window.location.href = '/factories';
        } else {
            alert('Bir hata oluştu: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu! Lütfen tüm alanları kontrol edin.');
    });
});

// Haritayı başlat
document.addEventListener('DOMContentLoaded', initMap);
</script>
@endsection