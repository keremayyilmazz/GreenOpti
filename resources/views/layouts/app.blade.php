<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Scripts -->
        @livewireScripts
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        
        <!-- Custom Scripts -->
        <script>
            // Global AJAX Setup for CSRF
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Global Error Handler
            $(document).ajaxError(function(event, jqXHR, settings, thrownError) {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: 'Bir hata oluştu. Lütfen tekrar deneyin.',
                });
            });

            // Initialize Select2
            $(document).ready(function() {
                $('.select2').select2({
                    theme: 'classic',
                    placeholder: 'Seçiniz...',
                    allowClear: true
                });
            });

            // Global Success Message
            function showSuccess(message) {
                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı!',
                    text: message,
                    timer: 2000,
                    showConfirmButton: false
                });
            }

            // Global Error Message
            function showError(message) {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: message
                });
            }

            // Global Confirmation Dialog
            function confirmAction(title, text, callback) {
                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Evet',
                    cancelButtonText: 'Hayır'
                }).then((result) => {
                    if (result.isConfirmed) {
                        callback();
                    }
                });
            }

            // Format Number
            function formatNumber(number) {
                return new Intl.NumberFormat('tr-TR').format(number);
            }

            // Format Date
            function formatDate(date) {
                return new Date(date).toLocaleString('tr-TR');
            }

            // Format Currency
            function formatCurrency(amount) {
                return new Intl.NumberFormat('tr-TR', {
                    style: 'currency',
                    currency: 'TRY'
                }).format(amount);
            }
        </script>

        <!-- Stack Scripts -->
        @stack('scripts')
    </body>
</html>