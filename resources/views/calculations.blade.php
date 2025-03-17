<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rota Hesaplama</title>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
 
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
</head>
<body>

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
        .carbon-info {
            background-color: #e8f5e9;
            padding: 10px;
            border-radius: 5px;
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
                <!-- Hesaplama Formu -->
                <div class="calculation-form">
                    <h4><i class="fas fa-calculator"></i> Rota Hesaplama</h4>
                    <form id="routeForm" class="mt-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="source_factory_id">Başlangıç Noktası</label>
                                    <select class="form-control" id="source_factory_id" required>
                                        <option value="">Seçiniz...</option>
                                        @foreach($factories as $factory)
                                            <option value="{{ $factory->id }}">{{ $factory->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="destination_factory_id">Varış Noktası</label>
                                    <select class="form-control" id="destination_factory_id" required>
                                        <option value="">Seçiniz...</option>
                                        @foreach($factories as $factory)
                                            <option value="{{ $factory->id }}">{{ $factory->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="vehicle_type">Taşıma Tipi</label>
                                    <select class="form-control" id="vehicle_type" required>
                                        <option value="land">Kara Yolu</option>
                                        <option value="sea">Deniz Yolu</option>
                                        <option value="rail">Demir Yolu</option>
                                        <option value="air">Hava Yolu</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-route"></i> Rotayı Hesapla
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Harita -->
                <div class="row">
    <div class="col-md-12">
        <!-- Harita Container -->
        <div id="mapContainer" class="mt-4">
            <div id="map"></div>
        </div>
    </div>
</div>

<style>
    #mapContainer {
        position: relative;
        width: 100%;
        height: 600px;
    }
    #map {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 10px;
    }
</style>

                <!-- Sonuç Kutusu -->
                <div class="result-box">
                    <h4><i class="fas fa-info-circle"></i> Rota Bilgileri</h4>
                    <div class="route-info">
                        <p><strong>Mesafe:</strong> <span id="distance">-</span></p>
                        <p><strong>Tahmini Süre:</strong> <span id="duration">-</span></p>
                        <p><strong>CO2 Emisyonu:</strong> <span id="carbon">-</span></p>
                        <div id="carbon-warning" style="display: none;" class="carbon-info">
                            <p class="mb-2">
                                <i class="fas fa-exclamation-triangle"></i> 
                                Bu rota için daha çevre dostu alternatifler mevcut:
                            </p>
                            <div id="alternatives-list"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Factories verisi -->
    <!-- Factories verisi -->
<script>
    window.factories = @json($factories);
</script>

<!-- Harita div'i -->
<div id="map" style="position: relative; z-index: 0;"></div>


	<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</body>
</html>

