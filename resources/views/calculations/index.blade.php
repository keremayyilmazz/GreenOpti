<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Fabrika Hesaplamaları') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($factories->isEmpty())
                        <p class="text-red-500">Henüz hiç fabrika eklenmemiş.</p>
                    @else
                        <form id="calculationForm" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Fabrikalar ({{ $factories->count() }} adet)
                                </label>
                                @foreach($factories as $factory)
                                <div class="mb-2">
                                    <label class="inline-flex items-center">
                                        <input 
                                            type="checkbox" 
                                            name="factories[]" 
                                            value="{{ $factory->id }}" 
                                            class="form-checkbox"
                                            onclick="console.log('Checkbox clicked:', this.value, this.checked)"
                                        >
                                        <span class="ml-2">
                                            {{ $factory->name }} 
                                            ({{ number_format($factory->latitude, 6) }}, {{ number_format($factory->longitude, 6) }})
                                        </span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Hesapla
                            </button>
                        </form>

                        <!-- Sonuçlar -->
                        <div id="results" class="mt-6 hidden">
                            <h3 class="text-lg font-semibold mb-4">Sonuçlar</h3>
                            <div id="resultsContent"></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(!$factories->isEmpty())
    <script>
        document.getElementById('calculationForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Seçili fabrikaları al
            const selectedFactories = Array.from(document.querySelectorAll('input[name="factories[]"]:checked')).map(cb => cb.value);
            
            // Debug
            console.log('Seçili fabrikalar:', selectedFactories);

            if (selectedFactories.length === 0) {
                alert('Lütfen en az bir fabrika seçin');
                return;
            }

            try {
                // CSRF token'ı al
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // FormData oluştur
                const formData = new FormData();
                selectedFactories.forEach(id => {
                    formData.append('factories[]', id);
                });

                const response = await fetch('/calculations', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();
                console.log('Response:', data);
                
                const resultsDiv = document.getElementById('results');
                const resultsContent = document.getElementById('resultsContent');
                
                if (response.ok) {
                    resultsContent.innerHTML = `
                        <p class="mb-4">Merkez Nokta: ${data.center.latitude.toFixed(6)}, ${data.center.longitude.toFixed(6)}</p>
                        <ul class="list-disc pl-5">
                            ${data.distances.map(d => `
                                <li class="mb-2">
                                    ${d.factory_name}: ${d.distance} km
                                </li>
                            `).join('')}
                        </ul>
                    `;
                    resultsDiv.classList.remove('hidden');
                } else {
                    resultsContent.innerHTML = `<p class="text-red-500">${data.message}</p>`;
                    resultsDiv.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Hata:', error);
                alert('Bir hata oluştu: ' + error.message);
            }
        });
    </script>
    @endif
</x-app-layout>