@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let map;
let markers = [];
let polyline;

function initMap() {
    // Türkiye'nin merkezi koordinatları
    const turkeyCenter = [39.9334, 32.8597];

    map = L.map('map').setView(turkeyCenter, 6);

    // OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Fabrika seçimi değiştiğinde haritayı güncelle
    ['from_factory_id', 'to_factory_id'].forEach(id => {
        document.getElementById(id).addEventListener('change', updateMap);
    });
}

function updateMap() {
    // Önceki işaretçileri ve çizgiyi temizle
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];
    if (polyline) map.removeLayer(polyline);

    const fromSelect = document.getElementById('from_factory_id');
    const toSelect = document.getElementById('to_factory_id');

    if (fromSelect.value && toSelect.value) {
        const fromOption = fromSelect.options[fromSelect.selectedIndex];
        const toOption = toSelect.options[toSelect.selectedIndex];

        const fromLocation = [
            parseFloat(fromOption.dataset.lat),
            parseFloat(fromOption.dataset.lng)
        ];

        const toLocation = [
            parseFloat(toOption.dataset.lat),
            parseFloat(toOption.dataset.lng)
        ];

        // Başlangıç noktası işaretçisi
        markers.push(L.marker(fromLocation, {
            title: fromOption.text
        }).addTo(map));

        // Varış noktası işaretçisi
        markers.push(L.marker(toLocation, {
            title: toOption.text
        }).addTo(map));

        // İki nokta arasına çizgi çek
        polyline = L.polyline([fromLocation, toLocation], {
            color: 'red',
            weight: 3,
            opacity: 0.7
        }).addTo(map);

        // Haritayı iki noktayı gösterecek şekilde ayarla
        const bounds = L.latLngBounds([fromLocation, toLocation]);
        map.fitBounds(bounds, { padding: [50, 50] });
    }
}

// Sayfa yüklendiğinde haritayı başlat
document.addEventListener('DOMContentLoaded', initMap);
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
.card {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
.form-select {
    padding: 0.75rem 1rem;
}
.btn-primary {
    padding: 0.75rem 1.5rem;
}
.alert {
    margin-bottom: 2rem;
}
#map {
    height: 400px;
    width: 100%;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}
</style>
@endpush