# 📇 Google Contacts Manager (PHP Package)

Package sederhana untuk manipulasi kontak Google menggunakan People API. Mendukung:

- 🔐 Login Google (OAuth2) sekali
- 📖 Baca (read) daftar kontak
- ➕ Tambah kontak
- 📝 Update kontak
- ❌ Hapus kontak
- 🔁 Refresh token otomatis (tanpa login ulang)

---

## 📦 Instalasi

Tambahkan ke proyek kamu via Composer:

```bash
composer require addin/google-contacts
```

🔧 Setup Awal

1. Aktifkan Google People API
    - Buka Google Cloud Console
    - Buat atau pilih project
    - Aktifkan People API
    - Buat kredensial OAuth 2.0 Client ID (application type: Desktop app)
    - Unduh credentials.json
2. Pertama Kali Login & Simpan Token
Jalankan perintah berikut:

```bash
php -S localhost:8080 example/authorize-server.php
```

buka di browser http://localhost:8080 

atau bisa juga membuat authorize script sendiri dengan mencontoh file /example/authorize-server

- Script ini akan:
- Menampilkan URL untuk login ke Google
- Meminta kode verifikasi
- Menyimpan token akses ke token.json

🚀 Contoh Pemakaian
Inisialisasi

```php
use Addin\GoogleContactsManager;

require 'vendor/autoload.php';

// Inisialisasi dengan file token
$contacts = new GoogleContactsManager('credentials.json','token.json');
```

📖 Baca Kontak

```php
$list = $contacts->listContacts(10);
foreach ($list as $person) {
    echo $person->getNames()[0]->getDisplayName() . PHP_EOL;
}
```

➕ Tambah Kontak

```php
$contacts->addContact('John Doe', 'john@example.com');
```

📝 Update Kontak

```php
$contacts->updateContact($resourceName, 'Nama Baru', 'emailbaru@example.com');
```

❌ Hapus Kontak

```php
$contacts->deleteContact($resourceName);
```

⚠️ Catatan

- Token akan otomatis direfresh jika access token expired
- Jangan upload credentials.json dan tokens/*.json ke GitHub (gunakan .gitignore)
- Jika token dicabut oleh user atau Google, harus login ulang (php auth-google.php)
