<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nakliye Hesaplama') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form id="calculationForm" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="source_factory_id" class="block text-sm font-medium text-gray-700">Çıkış Fabrikası</label>
                                <select id="source_factory_id" name="source_factory_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Fabrika Seçin</option>
                                    @foreach($factories as $factory)
                                        <option value="{{ $factory->id }}">{{ $factory->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="destination_factory_id" class="block text-sm font-medium text-gray-700">Varış Fabrikası</label>
                                <select id="destination_factory_id" name="destination_factory_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Fabrika Seçin</option>
                                    @foreach($factories as $factory)
                                        <option value="{{ $factory->id }}">{{ $factory->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700">Taşınacak Miktar (ton)</label>
                                <input type="number" step="0.01" min="0.01" id="weight" name="weight" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Hesapla
                            </button>
                        </div>
                    </form>

                    <!-- Sonuç Alanı -->
                    <div id="result" class="mt-8 hidden">
                        <div class="rounded-md bg-green-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-green-800" id="resultText"></h3>
                                    <div class="mt-2 text-sm text-green-700">
                                        <p id="distanceText"></p>
                                        <p id="weightText"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Geçmiş Hesaplamalar -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900">Geçmiş Hesaplamalar</h3>
                        <div class="mt-4 flex flex-col">
                            <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                                        <table class="min-w-full divide-y divide-gray-300">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Çıkış Fabrikası</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Varış Fabrikası</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Miktar</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Mesafe</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Tutar</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Tarih</th>
                                                </tr>
                                            </thead>
                                            <tbody id="calculationHistory" class="divide-y divide-gray-200 bg-white">
                                                <!-- JavaScript ile doldurulacak -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CSRF Token Meta -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        // CSRF Token'ı al
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Form gönderimi
        document.getElementById('calculationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const sourceFactoryId = document.getElementById('source_factory_id').value;
            const destFactoryId = document.getElementById('destination_factory_id').value;
            const weight = document.getElementById('weight').value;

            // Form validasyonu
            if (!sourceFactoryId || !destFactoryId || !weight) {
                alert('Lütfen tüm alanları doldurun.');
                return;
            }

            // Aynı fabrika kontrolü
            if (sourceFactoryId === destFactoryId) {
                alert('Çıkış ve varış fabrikaları aynı olamaz!');
                return;
            }

            const formData = {
                source_factory_id: sourceFactoryId,
                destination_factory_id: destFactoryId,
                weight: parseFloat(weight)
            };

            // API isteği
            fetch('/calculations', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Sunucu hatası oluştu');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    throw new Error(data.message || 'İşlem başarısız');
                }

                // Sonuçları göster
                const resultDiv = document.getElementById('result');
                const resultText = document.getElementById('resultText');
                const distanceText = document.getElementById('distanceText');
                const weightText = document.getElementById('weightText');
                
                resultDiv.classList.remove('hidden');
                resultText.textContent = `Toplam Nakliye Bedeli: ${Number(data.amount).toLocaleString('tr-TR')} TL`;
                distanceText.textContent = `Toplam Mesafe: ${Number(data.distance).toLocaleString('tr-TR')} km`;
                weightText.textContent = `Taşınacak Miktar: ${Number(formData.weight).toLocaleString('tr-TR')} ton`;
                
                // Geçmiş hesaplamaları güncelle
                loadCalculationHistory();
                
                // Formu temizle
                this.reset();
            })
            .catch(error => {
                console.error('Hesaplama hatası:', error);
                alert('Hesaplama yapılırken bir hata oluştu: ' + error.message);
            });
        });

        // Geçmiş hesaplamaları yükle
        function loadCalculationHistory() {
            fetch('/calculations/list')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Geçmiş hesaplamalar yüklenemedi');
                    }
                    return response.json();
                })
                .then(calculations => {
                    const tbody = document.getElementById('calculationHistory');
                    tbody.innerHTML = '';
                    
                    calculations.forEach(calc => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap">${calc.source_factory_name}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${calc.destination_factory_name}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${Number(calc.weight).toLocaleString('tr-TR')} ton</td>
                            <td class="px-6 py-4 whitespace-nowrap">${Number(calc.distance).toLocaleString('tr-TR')} km</td>
                            <td class="px-6 py-4 whitespace-nowrap">${Number(calc.amount).toLocaleString('tr-TR')} TL</td>
                            <td class="px-6 py-4 whitespace-nowrap">${new Date(calc.created_at).toLocaleString('tr-TR')}</td>
                        `;
                        tbody.appendChild(row);
                    });
                })
                .catch(error => {
                    console.error('Geçmiş yükleme hatası:', error);
                    alert('Geçmiş hesaplamalar yüklenirken bir hata oluştu');
                });
        }

        // Sayfa yüklendiğinde
        document.addEventListener('DOMContentLoaded', function() {
            loadCalculationHistory();
        });

        // Fabrika seçimi değiştiğinde kontrol
        document.getElementById('source_factory_id').addEventListener('change', checkFactories);
        document.getElementById('destination_factory_id').addEventListener('change', checkFactories);

        function checkFactories() {
            const sourceId = document.getElementById('source_factory_id').value;
            const destId = document.getElementById('destination_factory_id').value;
            
            if (sourceId && destId && sourceId === destId) {
                alert('Çıkış ve varış fabrikaları aynı olamaz!');
                document.getElementById('destination_factory_id').value = '';
            }
        }
    </script>
</x-app-layout>