# TODO: Implementasi Halaman Kelola Lokasi dengan Geo-Fence

## Langkah-langkah:
- [ ] Buat migration untuk menambah kolom latitude, longitude, radius ke tabel lokasi_kerja
- [ ] Update model LokasiKerja dengan fillable fields
- [ ] Tambah method di AdminController untuk indexLokasi, storeLokasi, updateLokasi, destroyLokasi
- [ ] Tambah route untuk kelola-lokasi di routes/web.php
- [ ] Buat view kelola-lokasi.blade.php dengan tabel, modal add/edit/delete, dan field untuk nama, lat, lng, radius, serta tombol GPS
- [ ] Tambah link di sidebar.blade.php untuk kelola lokasi
- [ ] Jalankan migration
- [ ] Test halaman kelola lokasi
