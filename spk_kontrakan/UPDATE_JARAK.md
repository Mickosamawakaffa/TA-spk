# Update Jarak Kontrakan dari Kampus POLIJE

File ini menjelaskan cara mengupdate jarak kontrakan yang sudah ada di database agar menggunakan jarak dari kampus POLIJE.

## Koordinat Kampus POLIJE
- **Latitude**: -8.15981
- **Longitude**: 113.72312

## Cara 1: Menggunakan Artisan Command (Recommended)

Jalankan command berikut di terminal:

```bash
php artisan update:jarak-kampus
```

Command ini akan:
- Mengambil semua kontrakan yang memiliki koordinat
- Menghitung jarak dari kampus POLIJE menggunakan Haversine formula
- Update field `jarak` di database (dalam meter)
- Menampilkan progress dan hasil update

## Cara 2: Menggunakan Seeder

Jalankan seeder berikut:

```bash
php artisan db:seed --class=UpdateJarakSeeder
```

## Hasil

Setelah dijalankan, semua kontrakan akan memiliki jarak yang dihitung dari kampus POLIJE:
- Jarak disimpan dalam **meter**
- Hanya data yang memiliki koordinat (latitude & longitude) yang akan diupdate
- Data tanpa koordinat akan di-skip

## Catatan

- Pastikan semua kontrakan sudah memiliki koordinat (latitude & longitude)
- Jarak dihitung menggunakan formula Haversine (akurat untuk jarak pendek-menengah)
- Update ini hanya mengubah data yang sudah ada, tidak menambah data baru




