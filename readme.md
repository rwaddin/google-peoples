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
Buka Google Cloud Console

Buat atau pilih project

Aktifkan People API

Buat kredensial OAuth 2.0 Client ID (application type: Desktop app)

Unduh credentials.json

2. Pertama Kali Login & Simpan Token
Jalankan perintah berikut:

```bash
php auth-google.php
```

- Script ini akan:
- Menampilkan URL untuk login ke Google
- Meminta kode verifikasi
- Menyimpan token akses ke tokens/[email].json

🚀 Contoh Pemakaian
Inisialisasi
```php
use GoogleContacts\GoogleContactsManager;

require 'vendor/autoload.php';

// Inisialisasi dengan file token
$contacts = new GoogleContactsManager(
    'credentials.json',
    'tokens/contact@addin.web.id.json'
);
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

Struktur Token Multi-Akun (Opsional)
Kamu bisa menyimpan banyak akun Google:

```pgsql
tokens/
├── support@yulo.id.json
├── marketing@yulo.id.json
└── admin@yulo.id.json
```

⚠️ Catatan
- Token akan otomatis direfresh jika access token expired
- Jangan upload credentials.json dan tokens/*.json ke GitHub (gunakan .gitignore)
- Jika token dicabut oleh user atau Google, harus login ulang (php auth-google.php)
