# 🚀 UPDATE SISTEM ABSENSI - Quick Start Guide

## 📋 Ringkasan Update

Update ini menambahkan fitur-fitur baru pada sistem absensi:

1. ✅ **Dinas Luar Auto Checkout** - Otomatis terisi jam pulang
2. ✅ **Default "Hari Ini"** - Data absensi & laporan default tampil hari ini
3. ✅ **Lokasi Nama Saja** - Tampil nama lokasi, bukan peta
4. ✅ **Area Kerja Karyawan** - Geofence per karyawan
5. ✅ **Bebas Lokasi Lembur/Dinas** - Tidak perlu validasi lokasi
6. ✅ **Sabtu/Minggu Libur** - Otomatis tandai weekend
7. ✅ **Jadwal Libur Nasional** - Kelola hari libur & cuti bersama
8. ✅ **Grafik Performa** - Dashboard dengan pie chart absensi

---

## 🔧 Instalasi

### Step 1: Backup Database
```bash
# Backup database terlebih dahulu (PENTING!)
mysqldump -u root -p nama_database > backup_$(date +%Y%m%d).sql
```

### Step 2: Run Migration

php artisan migrate
```

Output yang diharapkan:
```
INFO  Running migrations.

2025_10_30_000001_add_area_kerja_to_karyawan_table .................... 10ms DONE
2025_10_30_000002_create_jadwal_pengecualian_table .................... 8ms DONE
```

### Step 3: Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Step 4: (Opsional) Rebuild Assets
```bash
npm run build
```

---

## 📖 Panduan Penggunaan

### 1️⃣ Kelola Area Kerja Karyawan

1. Login sebagai Admin
2. Menu: **Kelola Karyawan**
3. Klik **Tambah Karyawan** atau **Edit** karyawan existing
4. Pilih **Area Kerja (Lokasi Absen)** dari dropdown
5. Simpan

**Catatan**: Area kerja akan menjadi batasan geofence untuk absensi normal. Dinas luar & lembur bebas lokasi.

---

### 2️⃣ Kelola Jadwal Libur

1. Login sebagai Admin
2. Menu: **Jadwal Libur** (sidebar)
3. Klik **Tambah Hari Libur**
4. Isi form:
   - **Tanggal**: Pilih tanggal libur
   - **Nama Hari Libur**: Contoh: "Hari Kemerdekaan RI"
   - **Jenis**: Libur Nasional / Cuti Bersama / Lainnya
   - **Keterangan**: (Opsional) Detail tambahan
5. Simpan

**Hasil**: Tanggal tersebut otomatis ditandai "Libur" di laporan absensi.

---

### 3️⃣ Lihat Grafik Performa Absensi

1. Login sebagai Admin
2. Menu: **Dashboard**
3. Scroll ke bawah ke section **Performa Absensi Bulan Ini**
4. Lihat grafik pie chart:
   - 🟢 **Hijau** = Tepat Waktu
   - 🟡 **Kuning** = Terlambat  
   - 🔴 **Merah** = Tidak Hadir

**Perhitungan**: Hanya menghitung hari kerja (exclude weekend & libur).

---

### 4️⃣ Absensi Dinas Luar (Mobile App)

1. Karyawan buka mobile app
2. Pilih **Dinas Luar** saat check-in
3. Check-in dari lokasi mana saja (bebas)
4. **TIDAK PERLU CHECK-OUT** - Sistem otomatis isi jam pulang sesuai jadwal

**Status**: Otomatis tercatat sebagai "Dinas Luar"

---

### 5️⃣ Lihat Data Absensi

1. Login sebagai Admin
2. Menu: **Data Absensi**
3. **Default tampil: Hari Ini** (bisa filter tanggal lain)
4. Data urut dari **paling baru**
5. Lokasi tampil **nama saja** (bukan koordinat)

---

### 6️⃣ Lihat Laporan Absensi

1. Login sebagai Admin
2. Menu: **Laporan**
3. **Default tampil: Hari Ini** (bisa pilih Harian/Mingguan/Bulanan)
4. Sabtu/Minggu otomatis status **"Libur - Akhir Pekan"**
5. Hari libur nasional status **"Libur - {Nama Libur}"**
6. Data urut dari **paling baru**

---

## 🔍 Troubleshooting

### ❌ Error: "Migration already exists"
```bash
# Cek status migration
php artisan migrate:status

# Jika sudah jalan, skip
```

### ❌ Error: "Column not found"
```bash
# Clear cache dulu
php artisan config:clear
php artisan cache:clear

# Lalu refresh halaman
```

### ❌ Grafik tidak muncul
```bash
# Pastikan Chart.js loaded
# Cek console browser (F12) untuk error JavaScript
```

### ❌ Area kerja tidak muncul di form
```bash
# Clear view cache
php artisan view:clear

# Refresh browser (Ctrl+F5)
```

---

## 📊 Testing Checklist

Setelah instalasi, test fitur-fitur berikut:

- [ ] ✅ Migration berhasil (2 migration)
- [ ] ✅ Menu "Jadwal Libur" muncul di sidebar
- [ ] ✅ Form karyawan ada dropdown "Area Kerja"
- [ ] ✅ Dashboard tampil grafik pie chart performa
- [ ] ✅ Data absensi default hari ini, urut terbaru
- [ ] ✅ Laporan default hari ini, Sabtu/Minggu libur
- [ ] ✅ Bisa tambah hari libur nasional
- [ ] ✅ Hari libur muncul di laporan
- [ ] ✅ Check-in dinas luar tanpa validasi lokasi
- [ ] ✅ Dinas luar otomatis jam keluar terisi

---

## 🆘 Butuh Bantuan?

Jika ada masalah atau error:

1. **Cek Log Error**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Rollback Migration** (jika diperlukan):
   ```bash
   php artisan migrate:rollback --step=2
   ```

3. **Lihat Detail** di: `CHANGELOG_UPDATE_30_OCT_2025.md`

---

## 📝 File Changes Summary

**New Files (3)**:
- `database/migrations/2025_10_30_000001_add_area_kerja_to_karyawan_table.php`
- `database/migrations/2025_10_30_000002_create_jadwal_pengecualian_table.php`
- `app/Models/JadwalPengecualian.php`
- `resources/views/kelola-jadwal-pengecualian.blade.php`

**Modified Files (5)**:
- `app/Models/Karyawan.php`
- `app/Http/Controllers/AdminController.php`
- `app/Http/Controllers/EmployeeController.php`
- `resources/views/dashboard.blade.php`
- `resources/views/sidebar.blade.php`
- `routes/web.php`

---

## 🎯 Next Steps

1. ✅ **Isi Area Kerja Karyawan**: Edit semua karyawan, pilih area kerja
2. ✅ **Input Hari Libur 2025**: Tambahkan libur nasional & cuti bersama
3. ✅ **Sosialisasi**: Info ke karyawan tentang fitur dinas luar
4. ✅ **Monitor**: Lihat grafik performa di dashboard

---

**Update berhasil! 🎉**

Sistem absensi sekarang lebih fleksibel dan informatif.
