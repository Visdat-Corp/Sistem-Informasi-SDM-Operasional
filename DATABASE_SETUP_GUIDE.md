# 🗄️ PANDUAN SETUP DATABASE - Lembur & Dinas Luar

## 📋 CHECKLIST KESIAPAN DATABASE

### ✅ Yang Sudah Siap:
1. **Model Absensi** - Sudah include semua field (`is_lembur`, `status`, dll)
2. **Migration File** - Sudah ada migrasi untuk update enum status
3. **API Controller** - Sudah siap menerima dan memproses data

### ⚠️ Yang Perlu Dicek:

**Apakah database sudah di-update dengan enum status terbaru?**

---

## 🔍 CEK STATUS DATABASE

### Metode 1: Menggunakan Laravel Tinker
```bash
php artisan tinker
```
```php
Schema::getColumnType('absensi', 'status');
// atau
DB::select("SHOW COLUMNS FROM absensi LIKE 'status'");
```

### Metode 2: Menggunakan phpMyAdmin / MySQL Client
```sql
SHOW COLUMNS FROM `absensi` LIKE 'status';
```

**Hasil yang BENAR harus menunjukkan:**
```
enum('hadir','terlambat','pulang cepat','tidak konsisten','tidak hadir','izin','sakit','cuti','dinas luar','lembur')
```

**Jika hasilnya SALAH (hanya menunjukkan):**
```
enum('hadir','terlambat','lembur')
```
**Maka database BELUM SIAP dan perlu di-update!**

---

## 🛠️ CARA UPDATE DATABASE

### Opsi A: Menggunakan Laravel Migration (RECOMMENDED)

**Jalankan perintah ini di terminal:**
```bash
php artisan migrate
```

Migration yang akan dijalankan:
- `2025_10_03_113252_update_status_enum_in_absensi_table.php`

### Opsi B: Menggunakan SQL Manual

**1. Buka file:**
```
database/update_absensi_status_enum.sql
```

**2. Jalankan SQL di phpMyAdmin atau MySQL client:**
```sql
ALTER TABLE `absensi` 
MODIFY COLUMN `status` ENUM(
    'hadir', 
    'terlambat', 
    'pulang cepat', 
    'tidak konsisten', 
    'tidak hadir', 
    'izin', 
    'sakit', 
    'cuti', 
    'dinas luar', 
    'lembur'
) DEFAULT NULL;
```

**3. Verifikasi hasil:**
```sql
SHOW COLUMNS FROM `absensi` LIKE 'status';
```

---

## ✅ VERIFIKASI SETELAH UPDATE

### 1. Cek Struktur Tabel
```sql
DESCRIBE absensi;
```

**Pastikan field berikut ada:**
- `status` - enum dengan semua nilai termasuk 'dinas luar'
- `is_lembur` - tinyint(1) DEFAULT 0
- `lokasi_absen_masuk` - varchar(255)
- `lokasi_absen_keluar` - varchar(255)
- `foto_masuk` - varchar(255)
- `foto_keluar` - varchar(255)

### 2. Test Insert Data
```sql
-- Test insert absensi dinas luar
INSERT INTO `absensi` (
    `id_karyawan`, 
    `tanggal_absen`, 
    `jam_masuk`, 
    `status`, 
    `lokasi_absen_masuk`
) VALUES (
    1, 
    '2025-10-30', 
    '08:00:00', 
    'dinas luar', 
    '0.0,0.0'
);

-- Jika berhasil, berarti database sudah siap!
-- Hapus data test:
DELETE FROM `absensi` WHERE `status` = 'dinas luar' AND `tanggal_absen` = '2025-10-30';
```

---

## 🚀 TESTING API

Setelah database ready, test API dengan curl atau Postman:

### Test Check-in Dinas Luar
```bash
curl -X POST http://your-api-url/api/v1/attendance/check-in \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: multipart/form-data" \
  -F "latitude=0.0" \
  -F "longitude=0.0" \
  -F "attendance_type=dinas_luar" \
  -F "foto=@photo.jpg"
```

### Expected Response
```json
{
  "success": true,
  "message": "Check-in successful",
  "data": {
    "attendance_id": 123,
    "check_in_time": "08:00:00",
    "date": "2025-10-30",
    "location": "Dinas Luar (Bebas Lokasi)",
    "coordinates": "0.0,0.0"
  }
}
```

---

## ⚠️ TROUBLESHOOTING

### Error: "Data truncated for column 'status'"
**Penyebab:** Enum status belum di-update
**Solusi:** Jalankan migration atau SQL update di atas

### Error: "Column 'is_lembur' not found"
**Penyebab:** Tabel belum memiliki kolom is_lembur
**Solusi:** Jalankan migration: `php artisan migrate`

### Error: "Unknown column 'attendance_type'"
**Penyebab:** Ini bukan error! attendance_type adalah parameter request, bukan kolom database
**Solusi:** Tidak perlu action, ini normal

---

## 📝 CATATAN PENTING

1. **Backup Database** sebelum menjalankan ALTER TABLE!
   ```bash
   mysqldump -u username -p database_name > backup_before_update.sql
   ```

2. **Jangan** langsung jalankan di production tanpa testing di development dulu

3. Data existing di tabel `absensi` **TIDAK** akan hilang saat update enum

4. Jika ada data dengan status lama yang tidak valid, Laravel akan tetap bisa membaca tapi tidak bisa update

---

## 🎯 KESIMPULAN

**Database SIAP menerima data Lembur dan Dinas Luar jika:**
- ✅ Migration sudah dijalankan (`php artisan migrate`)
- ✅ ATAU SQL update manual sudah dieksekusi
- ✅ Enum status include 'dinas luar'
- ✅ Field is_lembur sudah ada

**Cara Cepat Cek Kesiapan:**
```bash
php artisan tinker
>>> DB::select("SHOW COLUMNS FROM absensi LIKE 'status'")[0]->Type
```

Jika outputnya include `'dinas luar'`, **database SUDAH SIAP!** ✅
