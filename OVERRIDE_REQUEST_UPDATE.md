# Update Override Request System - Check In/Out Problems

## 📋 Ringkasan Perubahan

Sistem override request telah diperbarui dari yang sebelumnya digunakan untuk "Dinas Luar" menjadi untuk menangani masalah **Check In Terlambat** dan **Check Out Terlalu Cepat**.

### Perubahan Konsep:

**SEBELUMNYA:**
- Override untuk jenis absensi "Dinas Luar" atau "Lembur" (tidak ada check in/out)
- Request dibuat SEBELUM absensi
- Parameter: `jenis_absensi`, `timestamp`, `latitude`, `longitude`, `reason`

**SEKARANG:**
- Override untuk check in yang terlambat ATAU check out yang terlalu cepat
- Request dibuat SETELAH absensi (sudah ada data check in/out)
- Parameter: `id_absensi`, `override_type`, `reason`
- Dinas Luar tidak perlu override karena sudah punya status khusus

---

## 🔄 API Endpoint Changes

### Endpoint: `POST /api/v1/attendance/override-request`

#### Request Parameters (UPDATED)

```json
{
  "id_absensi": 123,
  "override_type": "late_check_in",
  "reason": "Terlambat karena pergi ke lokasi project dulu, lalu balik ke kantor untuk absen"
}
```

| Parameter | Type | Required | Values | Description |
|-----------|------|----------|--------|-------------|
| `id_absensi` | integer | ✅ Yes | - | ID record absensi yang sudah ada |
| `override_type` | string | ✅ Yes | `late_check_in`, `early_check_out` | Jenis masalah absensi |
| `reason` | string | ✅ Yes | min: 10, max: 500 | Alasan/penjelasan dari karyawan |

#### Override Types

| Type | Kapan Digunakan | Contoh Alasan |
|------|----------------|---------------|
| `late_check_in` | Check in terlambat dari jam kerja normal | "Terlambat karena pergi ke lokasi project lalu balik ke kantor dan mengabsen" |
| `early_check_out` | Check out terlalu cepat (sebelum jam keluar normal) | "Check out cepat karena ingin pergi ke lokasi project" |

---

## 📱 Mobile App Implementation Guide

### 1. Deteksi Kapan Override Diperlukan

Override request diperlukan ketika:

```kotlin
fun needsOverrideRequest(attendance: AttendanceRecord): Boolean {
    val workHours = attendance.jamKerja ?: return false
    
    // Check for late check in
    if (attendance.jamMasuk != null) {
        val normalCheckIn = parseTime(workHours.jamMasukNormal)
        val actualCheckIn = parseTime(attendance.jamMasuk)
        val tolerance = workHours.toleransiKeterlambatan ?: 0
        
        if (actualCheckIn.isAfter(normalCheckIn.plusMinutes(tolerance))) {
            return true // Late check in
        }
    }
    
    // Check for early check out
    if (attendance.jamKeluar != null) {
        val normalCheckOut = parseTime(workHours.jamKeluarNormal)
        val actualCheckOut = parseTime(attendance.jamKeluar)
        val tolerance = workHours.toleransiPulangCepat ?: 0
        
        if (actualCheckOut.isBefore(normalCheckOut.minusMinutes(tolerance))) {
            return true // Early check out
        }
    }
    
    return false
}
```

### 2. Tampilkan UI Override Request

Setelah check in/out berhasil, cek apakah perlu override:

```kotlin
fun onCheckInSuccess(attendanceId: Int, response: CheckInResponse) {
    // Simpan attendance ID
    saveAttendanceId(attendanceId)
    
    // Cek apakah terlambat
    if (response.isLate) {
        showOverrideRequestDialog(
            attendanceId = attendanceId,
            overrideType = "late_check_in",
            title = "Terlambat Check In",
            message = "Anda check in pada ${response.checkInTime}, lebih lambat dari jam kerja ${response.normalTime}. Mohon berikan alasan keterlambatan."
        )
    } else {
        showSuccess("Check in berhasil!")
    }
}

fun onCheckOutSuccess(attendanceId: Int, response: CheckOutResponse) {
    if (response.isEarly) {
        showOverrideRequestDialog(
            attendanceId = attendanceId,
            overrideType = "early_check_out",
            title = "Check Out Lebih Awal",
            message = "Anda check out pada ${response.checkOutTime}, lebih cepat dari jam keluar ${response.normalTime}. Mohon berikan alasan."
        )
    } else {
        showSuccess("Check out berhasil!")
    }
}
```

### 3. Kirim Override Request

```kotlin
data class OverrideRequest(
    @SerializedName("id_absensi")
    val idAbsensi: Int,
    
    @SerializedName("override_type")
    val overrideType: String, // "late_check_in" or "early_check_out"
    
    @SerializedName("reason")
    val reason: String
)

suspend fun sendOverrideRequest(
    idAbsensi: Int,
    overrideType: String,
    reason: String
): Result<OverrideResponse> {
    return withContext(Dispatchers.IO) {
        try {
            val request = OverrideRequest(
                idAbsensi = idAbsensi,
                overrideType = overrideType,
                reason = reason
            )
            
            val response = apiService.requestOverride(request)
            
            if (response.isSuccessful && response.body()?.success == true) {
                Result.success(response.body()!!)
            } else {
                Result.failure(Exception(response.body()?.message ?: "Gagal mengirim override request"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }
}
```

### 4. Contoh UI Dialog

```kotlin
@Composable
fun OverrideRequestDialog(
    attendanceId: Int,
    overrideType: String,
    title: String,
    message: String,
    onDismiss: () -> Unit,
    onSubmit: (String) -> Unit
) {
    var reason by remember { mutableStateOf("") }
    var isSubmitting by remember { mutableStateOf(false) }
    
    AlertDialog(
        onDismissRequest = onDismiss,
        title = { Text(title) },
        text = {
            Column {
                Text(message)
                Spacer(modifier = Modifier.height(16.dp))
                OutlinedTextField(
                    value = reason,
                    onValueChange = { reason = it },
                    label = { Text("Alasan (min. 10 karakter)") },
                    placeholder = { 
                        Text(
                            if (overrideType == "late_check_in") 
                                "Contoh: Terlambat karena pergi ke lokasi project lalu balik ke kantor"
                            else 
                                "Contoh: Check out cepat karena ingin pergi ke lokasi project"
                        ) 
                    },
                    minLines = 3,
                    maxLines = 5,
                    modifier = Modifier.fillMaxWidth()
                )
                if (reason.length < 10) {
                    Text(
                        text = "${reason.length}/10 karakter minimum",
                        style = MaterialTheme.typography.caption,
                        color = Color.Red
                    )
                }
            }
        },
        confirmButton = {
            Button(
                onClick = {
                    if (reason.length >= 10) {
                        isSubmitting = true
                        onSubmit(reason)
                    }
                },
                enabled = reason.length >= 10 && !isSubmitting
            ) {
                Text(if (isSubmitting) "Mengirim..." else "Kirim Request")
            }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) {
                Text("Nanti Saja")
            }
        }
    )
}
```

---

## 📊 Response Format

### Success Response (201 Created)

```json
{
  "success": true,
  "message": "Permintaan override telah dikirim. Menunggu persetujuan Manager SDM.",
  "data": {
    "absensi_id": 123,
    "status": "pending",
    "override_type": "late_check_in",
    "message": "Permintaan override berhasil dikirim ke Manager SDM"
  }
}
```

### Error Responses

#### 400 - Already Has Pending Override
```json
{
  "success": false,
  "message": "Anda sudah memiliki permintaan override yang sedang diproses untuk tanggal ini."
}
```

#### 400 - Already Approved
```json
{
  "success": false,
  "message": "Permintaan override untuk tanggal ini sudah disetujui sebelumnya."
}
```

#### 403 - Unauthorized Access
```json
{
  "success": false,
  "message": "Anda tidak memiliki akses ke data absensi ini."
}
```

#### 422 - Validation Error
```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "reason": ["The reason field must be at least 10 characters."],
    "override_type": ["The selected override type is invalid."]
  }
}
```

---

## 🔍 Flow Diagram

```
┌─────────────────┐
│ Karyawan Check  │
│ In / Check Out  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Cek Status:     │
│ Terlambat?      │
│ Pulang Cepat?   │
└────────┬────────┘
         │
    ┌────┴────┐
    │         │
    ▼         ▼
┌─────┐   ┌─────────────────┐
│ OK  │   │ Perlu Override  │
└─────┘   └────────┬────────┘
                   │
                   ▼
         ┌─────────────────┐
         │ Tampilkan Dialog│
         │ Override Request│
         └────────┬────────┘
                  │
                  ▼
         ┌─────────────────┐
         │ Karyawan Isi    │
         │ Alasan          │
         └────────┬────────┘
                  │
                  ▼
         ┌─────────────────┐
         │ POST /override  │
         │ -request        │
         └────────┬────────┘
                  │
                  ▼
         ┌─────────────────┐
         │ Status: PENDING │
         │ Tunggu Approval │
         └────────┬────────┘
                  │
    ┌─────────────┴──────────────┐
    │                            │
    ▼                            ▼
┌─────────┐                 ┌──────────┐
    │APPROVED │                 │REJECTED  │
│Status:  │                 │Status:   │
│Tetap    │                 │Tetap     │
│Sesuai   │                 │Sesuai    │
│Asli*    │                 │Asli      │
└─────────┘                 └──────────┘

*Status akan tetap seperti semula (terlambat, pulang cepat, dll)
 kecuali admin mengubahnya secara eksplisit saat approval.
```

---

## 🎯 Admin Approval Features

### Saat Menyetujui Override Request, Admin Dapat:

1. **Menyetujui Tanpa Perubahan**
   - Status tetap sesuai kondisi asli (terlambat, pulang cepat, dll)
   - Jam masuk/keluar tetap sesuai yang tercatat

2. **Menyetujui Dengan Koreksi Waktu**
   - Admin bisa mengubah jam masuk jika diperlukan
   - Admin bisa mengubah jam keluar jika diperlukan
   - Contoh: Karyawan check in jam 08:30, tapi seharusnya 08:15 karena sudah di kantor

3. **Menyetujui Dengan Ubah Status**
   - Admin bisa mengubah status dari "terlambat" menjadi "hadir" jika ada alasan valid
   - Admin bisa mengubah status dari "pulang cepat" menjadi "hadir" jika disetujui
   - Status tersedia: hadir, terlambat, pulang cepat, tidak konsisten, izin, sakit, cuti, dinas luar

4. **Menyetujui Dengan Koreksi Penuh**
   - Admin bisa mengubah waktu DAN status sekaligus
   - Memberikan fleksibilitas penuh untuk admin

### Perilaku Default Saat Approval:

**PENTING:** Status tidak akan otomatis berubah menjadi "hadir" saat disetujui.

- ❌ **SEBELUMNYA:** Approval otomatis mengubah status menjadi "hadir"
- ✅ **SEKARANG:** Status tetap seperti semula kecuali admin mengubahnya

**Contoh Kasus:**

| Kondisi Awal | Admin Action | Hasil Akhir |
|--------------|--------------|-------------|
| Check in 08:30 (terlambat) | Approve tanpa ubah | Status: **terlambat**, Jam: 08:30 |
| Check in 08:30 (terlambat) | Approve + ubah status "hadir" | Status: **hadir**, Jam: 08:30 |
| Check in 08:30 (terlambat) | Approve + ubah jam 08:00 | Status: **terlambat**, Jam: 08:00 |
| Check in 08:30 (terlambat) | Approve + ubah jam 08:00 + ubah status "hadir" | Status: **hadir**, Jam: 08:00 |
| Check out 16:30 (pulang cepat) | Approve tanpa ubah | Status: **pulang cepat**, Jam: 16:30 |
| Check out 16:30 (pulang cepat) | Approve + ubah status "hadir" | Status: **hadir**, Jam: 16:30 |

**Keuntungan Sistem Baru:**

1. ✅ **Akurat**: Status mencerminkan kondisi sebenarnya
2. ✅ **Fleksibel**: Admin bisa koreksi jika memang diperlukan
3. ✅ **Transparan**: Data absensi lebih jujur untuk laporan
4. ✅ **Efisien**: Satu kali action untuk approve + koreksi

---

## 💡 Best Practices### 1. **Simpan ID Absensi Setelah Check In/Out**
```kotlin
// Setelah check in/out berhasil, simpan ID
fun onCheckInSuccess(response: CheckInResponse) {
    PreferenceManager.saveTodayAttendanceId(response.attendanceId)
    
    if (response.isLate) {
        // Langsung bisa request override karena ID sudah ada
        showOverrideDialog(response.attendanceId, "late_check_in")
    }
}
```

### 2. **Berikan Contoh Alasan yang Jelas**
```kotlin
val exampleReasons = when (overrideType) {
    "late_check_in" -> listOf(
        "Terlambat karena pergi ke lokasi project lalu balik ke kantor untuk absen",
        "Terlambat karena mengantar anak ke sekolah",
        "Terlambat karena macet di jalan"
    )
    "early_check_out" -> listOf(
        "Check out cepat karena ada meeting di lokasi project",
        "Check out cepat karena harus ke kantor cabang",
        "Check out cepat karena keperluan darurat keluarga"
    )
    else -> emptyList()
}
```

### 3. **Tampilkan Status Override di History**
```kotlin
fun displayAttendanceStatus(attendance: AttendanceRecord): String {
    return when {
        attendance.overrideRequest && attendance.overrideStatus == "pending" -> 
            "⏳ Menunggu Approval Manager"
        
        attendance.overrideRequest && attendance.overrideStatus == "approved" -> 
            "✅ Override Disetujui - ${attendance.status}"
        
        attendance.overrideRequest && attendance.overrideStatus == "rejected" -> 
            "❌ Override Ditolak - ${attendance.status}"
        
        else -> attendance.status
    }
}
```

### 4. **Handle Retry jika Gagal**
```kotlin
suspend fun sendOverrideWithRetry(
    idAbsensi: Int,
    overrideType: String,
    reason: String,
    maxRetries: Int = 3
): Result<OverrideResponse> {
    repeat(maxRetries) { attempt ->
        val result = sendOverrideRequest(idAbsensi, overrideType, reason)
        if (result.isSuccess) return result
        
        if (attempt < maxRetries - 1) {
            delay(1000L * (attempt + 1)) // Exponential backoff
        }
    }
    return Result.failure(Exception("Gagal setelah $maxRetries percobaan"))
}
```

---

## ⚠️ Important Notes

### Untuk Dinas Luar:
- **TIDAK PERLU** override request
- Sudah ada status khusus "Dinas Luar" di sistem
- Tetap gunakan endpoint check in/out biasa dengan parameter `attendance_type = "dinas_luar"`

### Validasi:
- **Alasan minimum 10 karakter**, maksimum 500 karakter
- **ID Absensi harus valid** dan milik user yang login
- **Override type** harus `late_check_in` atau `early_check_out`
- **Tidak bisa request ulang** jika sudah ada yang pending

### Error Handling:
- Selalu cek response status code
- Tampilkan error message yang user-friendly
- Simpan log untuk debugging

---

## 🧪 Testing Checklist

- [ ] Check in normal (tidak perlu override)
- [ ] Check in terlambat → Dialog override muncul
- [ ] Isi alasan < 10 karakter → Button disabled
- [ ] Isi alasan ≥ 10 karakter → Bisa kirim
- [ ] Override request berhasil terkirim
- [ ] Check out normal (tidak perlu override)
- [ ] Check out terlalu cepat → Dialog override muncul
- [ ] Kirim override request untuk early check out
- [ ] Coba kirim override kedua kali → Dapat error
- [ ] Lihat status di history → Tampil "Menunggu Approval"
- [ ] Setelah approved di web → Status berubah di mobile
- [ ] Setelah rejected di web → Tetap tampil status bermasalah

---

## 📞 Contact

Jika ada pertanyaan atau issue, hubungi tim backend developer.

**Update Date:** October 31, 2025
**Version:** 2.0
