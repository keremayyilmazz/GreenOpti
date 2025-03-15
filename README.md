<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Fabrika Yönetim Sistemi

## Proje Hakkında
Bu proje, fabrikaların üretim süreçlerini, stok yönetimini ve iş akışlarını yönetmek için geliştirilmiş bir web uygulamasıdır. Laravel framework'ü kullanılarak geliştirilmiştir.

## Özellikler
- Üretim takibi ve planlama
- Stok yönetimi ve takibi
- Sipariş yönetimi
- Detaylı raporlama sistemi
- Kullanıcı yetkilendirme sistemi
- Gerçek zamanlı bildirimler

## Sistem Gereksinimleri
- PHP >= 8.1
- MySQL veya PostgreSQL
- Composer
- Node.js ve NPM

## Kurulum
1. Repository'yi clone'layın:



## Proje Yapısı

project/
├── app/
│ ├── Http/
│ │ └── Controllers/ # Controller sınıfları
│ └── Models/ # Veritabanı modelleri
├── config/ # Yapılandırma dosyaları
├── database/
│ ├── migrations/ # Veritabanı migration'ları
│ └── seeders/ # Veritabanı seed'leri
├── public/ # Genel erişime açık dosyalar
├── resources/
│ ├── css/ # Stil dosyaları
│ ├── js/ # JavaScript dosyaları
│ └── views/ # Blade template'leri
├── routes/ # Rota tanımlamaları
└── tests/ # Test dosyaları

## Özellikler ve Ekran Görüntüleri

### Admin Paneli
- Kullanıcı Yönetimi
  - Kullanıcı ekleme/düzenleme/silme
  - Rol ve yetki yönetimi
  - Kullanıcı aktivitelerini izleme

- Stok Takibi
  - Ürün stok durumu
  - Stok giriş/çıkış işlemleri
  - Kritik stok bildirimleri

- Sipariş Yönetimi
  - Yeni sipariş oluşturma
  - Sipariş durumu güncelleme
  - Sipariş geçmişi

### Kullanıcı Paneli
- Sipariş İşlemleri
  - Yeni sipariş oluşturma
  - Sipariş takibi
  - Sipariş geçmişi görüntüleme

- Stok Görüntüleme
  - Mevcut stok durumu
  - Ürün detayları
  - Fiyat bilgileri

- Raporlar
  - Günlük/haftalık/aylık raporlar
  - Sipariş istatistikleri
  - Stok hareket raporları

## API Dokümantasyonu

### Kullanılabilir Endpoint'ler

\`\`\`
# Ürün İşlemleri
GET /api/products           - Tüm ürünleri listele
GET /api/products/{id}      - Ürün detayı
POST /api/products          - Yeni ürün ekle
PUT /api/products/{id}      - Ürün güncelle
DELETE /api/products/{id}   - Ürün sil

# Sipariş İşlemleri
GET /api/orders            - Tüm siparişleri listele
POST /api/orders           - Yeni sipariş oluştur
PUT /api/orders/{id}       - Sipariş durumu güncelle

# Raporlama
GET /api/reports/daily     - Günlük rapor
GET /api/reports/monthly   - Aylık rapor
GET /api/reports/stock     - Stok raporu
\`\`\`

## Teknik Detaylar

### Kullanılan Teknolojiler
- Laravel 10.x
- PHP 8.1+
- MySQL 8.0
- Tailwind CSS
- Alpine.js
- Laravel Livewire

### Güvenlik Özellikleri
- Laravel Sanctum ile API Authentication
- CSRF koruması
- XSS koruması
- SQL Injection koruması
- Rate Limiting