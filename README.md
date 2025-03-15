<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# GreenOpti - Akıllı Ulaşım Optimizasyonu

## Proje Hakkında
Bu proje, farklı ulaşım modları (kara, hava, deniz ve tren) arasında en optimum rotayı belirleyerek karbon emisyonunu azaltmayı, zaman ve maliyet optimizasyonunu sağlamayı amaçlayan bir web uygulamasıdır.

## 🚧 Geliştirme Aşaması
Proje şu anda aktif geliştirme aşamasındadır. Aşağıdaki özellikler yakında eklenecektir:

### Planlan Özellikler
- [ ] Çoklu Ulaşım Modu Analizi
  - Kara yolu optimizasyonu
  - Hava yolu optimizasyonu
  - Deniz yolu optimizasyonu
  - Tren yolu optimizasyonu

- [ ] Optimizasyon Kriterleri
  - Karbon emisyonu hesaplama
  - Zaman optimizasyonu
  - Maliyet analizi
  - Kombine rota önerileri

- [ ] Raporlama Sistemi
  - Emisyon raporları
  - Maliyet karşılaştırma
  - Zaman analizi
  - Optimizasyon önerileri

### Yakında Eklenecek
- Gerçek zamanlı rota takibi
- Yapay zeka destekli rota optimizasyonu
- Detaylı karbon ayak izi analizi
- İnteraktif harita entegrasyonu

## Teknik Altyapı
- Laravel 10.x
- PHP 8.1+
- MySQL
- JavaScript
- Harita API'leri
- AI Modelleri (Geliştirme aşamasında)


### Adım Adım Kurulum

1. **Repository'yi klonlayın:**
```bash
git clone https://github.com/keremayyilmazz/GreenOpti.git
cd GreenOpti
```

2. **Composer bağımlılıklarını yükleyin:**
```bash
composer install
```

3. **NPM bağımlılıklarını yükleyin:**
```bash
npm install
```

4. **Ortam değişkenlerini ayarlayın:**
```bash
# .env.example dosyasını kopyalayın
cp .env.example .env

# Uygulama anahtarını oluşturun
php artisan key:generate
```

5. **Veritabanını ayarlayın:**
- MySQL'de yeni bir veritabanı oluşturun
- .env dosyasında veritabanı bilgilerinizi düzenleyin:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fabrika_db
DB_USERNAME=root
DB_PASSWORD=
```

6. **Veritabanı tablolarını oluşturun:**
```bash
# Tabloları oluşturun
php artisan migrate

# (Opsiyonel) Örnek verileri yükleyin
php artisan db:seed
```

7. **Uygulamayı çalıştırın:**
```bash
# Laravel sunucusunu başlatın
php artisan serve

# Yeni bir terminal açın ve asset'leri derleyin
npm run dev
```

8. **Tarayıcıda açın:**
- [http://localhost:8000](http://localhost:8000)

### Varsayılan Giriş Bilgileri
```
Admin Kullanıcısı:
Email: admin@example.com
Şifre: password
```


## Proje Yapısı



```
project/
├── app/
│   ├── Http/
│   │   └── Controllers/    # Controller sınıfları
│   └── Models/            # Veritabanı modelleri
├── config/               # Yapılandırma dosyaları
├── database/
│   ├── migrations/      # Veritabanı migration'ları
│   └── seeders/        # Veritabanı seed'leri
├── public/             # Genel erişime açık dosyalar
├── resources/
│   ├── css/           # Stil dosyaları
│   ├── js/           # JavaScript dosyaları
│   └── views/        # Blade template'leri
├── routes/           # Rota tanımlamaları
└── tests/           # Test dosyaları
```
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