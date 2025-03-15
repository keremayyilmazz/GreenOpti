<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Fabrikalar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Fabrika Ekleme ve Hesaplama Butonları -->
                    <div class="mb-4 flex space-x-4">
                        <button id="addFactoryBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Fabrika Ekle
                        </button>
                        
                        <a href="{{ route('calculations') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Hesaplama Yap
                        </a>
                    </div>

                    <!-- Harita -->
                    <div id="map" style="height: 500px;" class="mb-4 rounded-lg shadow-lg"></div>

                    <!-- Fabrika Listesi -->
                    <div class="mt-6">
                        <h3 class="text-lg font-bold mb-4">Fabrika Listesi</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white dark:bg-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 border-b dark:border-gray-600 text-left">Fabrika Adı</th>
                                        <th class="px-6 py-3 border-b dark:border-gray-600 text-left">Konum</th>
                                        <th class="px-6 py-3 border-b dark:border-gray-600 text-left">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody id="factoryList">
                                    <!-- Fabrikalar JavaScript ile buraya eklenecek -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fabrika Ekleme Modal -->
    <div id="factoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 text-center">Fabrika Ekle</h3>
                <form id="factoryForm" class="space-y-4">
                    <div>
                        <label for="factoryName" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fabrika Adı</label>
                        <input type="text" 
                               id="factoryName" 
                               name="factoryName" 
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                               placeholder="Fabrika adını girin"
                               required>
                    </div>
                    <input type="hidden" id="lat">
                    <input type="hidden" id="lng">
                    <div class="flex justify-end space-x-3 mt-4">
                        <button type="button" 
                                id="closeModal" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200">
                            İptal
                        </button>
                        <button type="submit" 
                                id="saveFactory" 
                                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                            Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Leaflet CSS ve JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

    <script>
        // Harita başlatma
        let map = L.map('map').setView([41.0082, 28.9784], 8);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        let markers = [];
        let selectedLocation = null;

        // Mevcut fabrikaları yükle
        function loadFactories() {
            fetch('/factories')
                .then(response => response.json())
                .then(factories => {
                    // Tabloyu temizle
                    document.getElementById('factoryList').innerHTML = '';
                    // Markerları temizle
                    markers.forEach(marker => map.removeLayer(marker));
                    markers = [];

                    factories.forEach(factory => {
                        // Tabloya ekle
                        let row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="px-6 py-4 border-b dark:border-gray-600">${factory.name}</td>
                            <td class="px-6 py-4 border-b dark:border-gray-600">${factory.latitude}, ${factory.longitude}</td>
                            <td class="px-6 py-4 border-b dark:border-gray-600">
                                <button onclick="deleteFactory(${factory.id})" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">Sil</button>
                            </td>
                        `;
                        document.getElementById('factoryList').appendChild(row);

                        // Haritaya marker ekle
                        let marker = L.marker([factory.latitude, factory.longitude])
                            .bindPopup(factory.name)
                            .addTo(map);
                        markers.push(marker);
                    });
                });
        }

        // Sayfa yüklendiğinde fabrikaları yükle
        loadFactories();

        // Form submit olayını dinle
        document.getElementById('factoryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            let name = document.getElementById('factoryName').value;
            let lat = document.getElementById('lat').value;
            let lng = document.getElementById('lng').value;

            if (!name || !lat || !lng) {
                alert('Lütfen fabrika adı girin ve haritadan konum seçin.');
                return;
            }

            fetch('/factories', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: name,
                    latitude: parseFloat(lat),
                    longitude: parseFloat(lng)
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('factoryModal').classList.add('hidden');
                document.getElementById('factoryForm').reset();
                loadFactories();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Bir hata oluştu: ' + (error.message || 'Bilinmeyen hata'));
            });
        });

        // Haritaya tıklama olayı
        map.on('click', function(e) {
            selectedLocation = e.latlng;
            
            // Eski marker'ı temizle
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];

            // Yeni marker ekle
            let marker = L.marker(e.latlng).addTo(map);
            markers.push(marker);

            // Modal'ı aç ve koordinatları kaydet
            document.getElementById('factoryModal').classList.remove('hidden');
            document.getElementById('lat').value = e.latlng.lat;
            document.getElementById('lng').value = e.latlng.lng;
            
            // Input'a focus
            document.getElementById('factoryName').focus();
        });

        // Modal kapatma
        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('factoryModal').classList.add('hidden');
            document.getElementById('factoryForm').reset();
        });

        // Sayfa dışına tıklayınca modal'ı kapat
        window.addEventListener('click', function(e) {
            let modal = document.getElementById('factoryModal');
            if (e.target === modal) {
                modal.classList.add('hidden');
                document.getElementById('factoryForm').reset();
            }
        });

        // Fabrika silme fonksiyonu
        function deleteFactory(id) {
            if (confirm('Bu fabrikayı silmek istediğinizden emin misiniz?')) {
                fetch(`/factories/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    loadFactories();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Silme işlemi sırasında bir hata oluştu: ' + (error.message || 'Bilinmeyen hata'));
                });
            }
        }
    </script>
    @endpush
</x-app-layout>