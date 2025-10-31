# 🚨 PANDUAN OVERRIDE REQUEST - MOBILE APP

## ❗ PENTING - URL yang Benar

**✅ GUNAKAN URL INI:**
```
POST /api/v1/attendance/override-request
```

**❌ JANGAN GUNAKAN:**
```
POST /api/v1/attendance/request-override
```

## 📋 Required Parameters (WAJIB)

### 1. tanggal_absen (REQUIRED)
- **Type:** String
- **Format:** YYYY-MM-DD
- **Example:** "2025-10-31"
- **Note:** HARUS ada, tidak boleh null atau empty

### 2. reason (REQUIRED)
- **Type:** String
- **Min Length:** 10 karakter
- **Max Length:** 500 karakter
- **Example:** "Dinas luar ke client Jakarta untuk presentasi project"
- **Note:** Alasan harus jelas dan minimal 10 karakter

## 📋 Optional Parameters

### 3. attendance_type (OPTIONAL)
- **Type:** String
- **Values:** "normal", "lembur", "dinas_luar"
- **Default:** "normal"
- **Example:** "dinas_luar"

### 4. latitude (OPTIONAL)
- **Type:** Double
- **Range:** -90 to 90
- **Example:** -6.2088

### 5. longitude (OPTIONAL)
- **Type:** Double
- **Range:** -180 to 180
- **Example:** 106.8456

---

## 📱 Implementasi Android/Kotlin

### Contoh 1: Override Request Dinas Luar

```kotlin
// Model/Data Class
data class OverrideRequestBody(
    @SerializedName("tanggal_absen")
    val tanggalAbsen: String,        // WAJIB: format "YYYY-MM-DD"
    
    @SerializedName("reason")
    val reason: String,              // WAJIB: min 10 karakter
    
    @SerializedName("attendance_type")
    val attendanceType: String = "normal",  // OPTIONAL
    
    @SerializedName("latitude")
    val latitude: Double? = null,    // OPTIONAL
    
    @SerializedName("longitude")
    val longitude: Double? = null    // OPTIONAL
)

// Response Model
data class OverrideResponse(
    val success: Boolean,
    val message: String,
    val data: OverrideData?
)

data class OverrideData(
    @SerializedName("absensi_id")
    val absensiId: Int,
    val status: String,
    @SerializedName("attendance_type")
    val attendanceType: String,
    val message: String
)

// API Service (Retrofit)
interface AttendanceApi {
    @POST("api/v1/attendance/override-request")
    suspend fun requestOverride(
        @Header("Authorization") token: String,
        @Body request: OverrideRequestBody
    ): Response<OverrideResponse>
}

// Usage dalam ViewModel atau Repository
suspend fun requestOverrideForDinasLuar(
    date: String,              // Format: "2025-10-31"
    reason: String,           // Min 10 karakter
    latitude: Double,
    longitude: Double
): Result<OverrideResponse> {
    return try {
        val token = "Bearer ${getAuthToken()}"
        
        val requestBody = OverrideRequestBody(
            tanggalAbsen = date,           // PENTING: gunakan nama parameter yang benar
            reason = reason,
            attendanceType = "dinas_luar", // untuk dinas luar
            latitude = latitude,
            longitude = longitude
        )
        
        val response = api.requestOverride(token, requestBody)
        
        if (response.isSuccessful && response.body() != null) {
            Result.success(response.body()!!)
        } else {
            val errorBody = response.errorBody()?.string()
            Log.e("Override", "Error: $errorBody")
            Result.failure(Exception(errorBody ?: "Unknown error"))
        }
    } catch (e: Exception) {
        Log.e("Override", "Exception: ${e.message}", e)
        Result.failure(e)
    }
}
```

### Contoh 2: Dengan OkHttp

```kotlin
fun sendOverrideRequest(
    date: String,
    reason: String,
    type: String = "dinas_luar",
    lat: Double,
    lng: Double
) {
    val client = OkHttpClient()
    
    // Buat JSON body
    val jsonBody = JSONObject().apply {
        put("tanggal_absen", date)        // WAJIB
        put("reason", reason)             // WAJIB
        put("attendance_type", type)      // OPTIONAL
        put("latitude", lat)              // OPTIONAL
        put("longitude", lng)             // OPTIONAL
    }
    
    val requestBody = jsonBody.toString()
        .toRequestBody("application/json".toMediaType())
    
    val request = Request.Builder()
        .url("$BASE_URL/api/v1/attendance/override-request")
        .post(requestBody)
        .addHeader("Authorization", "Bearer $token")
        .addHeader("Content-Type", "application/json")
        .addHeader("Accept", "application/json")
        .build()
    
    client.newCall(request).enqueue(object : Callback {
        override fun onFailure(call: Call, e: IOException) {
            Log.e("Override", "Request failed", e)
            // Handle failure
        }
        
        override fun onResponse(call: Call, response: Response) {
            val responseBody = response.body?.string()
            Log.d("Override", "Response: $responseBody")
            
            if (response.isSuccessful) {
                // Parse success response
                val jsonResponse = JSONObject(responseBody ?: "{}")
                val success = jsonResponse.getBoolean("success")
                val message = jsonResponse.getString("message")
                // Handle success
            } else {
                // Handle error
                Log.e("Override", "Error ${response.code}: $responseBody")
            }
        }
    })
}
```

---

## 🧪 Testing & Debugging

### 1. Log Request Sebelum Dikirim

```kotlin
// Log untuk debugging
Log.d("Override", "=== OVERRIDE REQUEST ===")
Log.d("Override", "URL: $BASE_URL/api/v1/attendance/override-request")
Log.d("Override", "Method: POST")
Log.d("Override", "Headers:")
Log.d("Override", "  Authorization: Bearer ${token.take(20)}...")
Log.d("Override", "  Content-Type: application/json")
Log.d("Override", "Body:")
Log.d("Override", "  tanggal_absen: $date")
Log.d("Override", "  reason: $reason")
Log.d("Override", "  attendance_type: $type")
Log.d("Override", "  latitude: $lat")
Log.d("Override", "  longitude: $lng")
```

### 2. Validasi Data Sebelum Dikirim

```kotlin
fun validateOverrideRequest(date: String, reason: String): ValidationResult {
    // Validasi tanggal_absen
    if (date.isBlank()) {
        return ValidationResult.Error("Tanggal absen tidak boleh kosong")
    }
    
    // Validasi format tanggal (YYYY-MM-DD)
    val datePattern = "^\\d{4}-\\d{2}-\\d{2}$".toRegex()
    if (!date.matches(datePattern)) {
        return ValidationResult.Error("Format tanggal harus YYYY-MM-DD")
    }
    
    // Validasi reason
    if (reason.isBlank()) {
        return ValidationResult.Error("Alasan tidak boleh kosong")
    }
    
    if (reason.length < 10) {
        return ValidationResult.Error("Alasan minimal 10 karakter")
    }
    
    if (reason.length > 500) {
        return ValidationResult.Error("Alasan maksimal 500 karakter")
    }
    
    return ValidationResult.Success
}

sealed class ValidationResult {
    object Success : ValidationResult()
    data class Error(val message: String) : ValidationResult()
}
```

### 3. Handle Error Response

```kotlin
fun handleOverrideError(errorCode: Int, errorBody: String?) {
    when (errorCode) {
        401 -> {
            // Token invalid atau expired
            Log.e("Override", "Unauthenticated - Token invalid/expired")
            // Redirect ke login atau refresh token
        }
        
        422 -> {
            // Validation error
            try {
                val jsonError = JSONObject(errorBody ?: "{}")
                val message = jsonError.optString("message", "Validation error")
                val errors = jsonError.optJSONObject("errors")
                
                Log.e("Override", "Validation Error: $message")
                
                // Parse specific field errors
                errors?.let {
                    if (it.has("tanggal_absen")) {
                        val tanggalError = it.getJSONArray("tanggal_absen")
                        Log.e("Override", "tanggal_absen error: ${tanggalError.getString(0)}")
                        showError("Tanggal absen: ${tanggalError.getString(0)}")
                    }
                    
                    if (it.has("reason")) {
                        val reasonError = it.getJSONArray("reason")
                        Log.e("Override", "reason error: ${reasonError.getString(0)}")
                        showError("Alasan: ${reasonError.getString(0)}")
                    }
                }
            } catch (e: Exception) {
                Log.e("Override", "Error parsing validation response", e)
            }
        }
        
        400 -> {
            // Business logic error (duplicate, already approved, etc)
            try {
                val jsonError = JSONObject(errorBody ?: "{}")
                val message = jsonError.optString("message", "Bad request")
                Log.e("Override", "Business Error: $message")
                showError(message)
            } catch (e: Exception) {
                Log.e("Override", "Error parsing error response", e)
            }
        }
        
        405 -> {
            // Method not allowed - kemungkinan URL salah
            Log.e("Override", "405 Method Not Allowed - Check URL!")
            showError("Kesalahan sistem. Hubungi admin.")
        }
        
        else -> {
            Log.e("Override", "Unexpected error: $errorCode")
            showError("Terjadi kesalahan. Silakan coba lagi.")
        }
    }
}
```

---

## ✅ Checklist Sebelum Request

Sebelum mengirim override request, pastikan:

- [ ] ✅ URL benar: `/api/v1/attendance/override-request`
- [ ] ✅ Method: POST
- [ ] ✅ Header `Authorization` ada dengan format `Bearer {token}`
- [ ] ✅ Header `Content-Type: application/json`
- [ ] ✅ Header `Accept: application/json`
- [ ] ✅ Parameter `tanggal_absen` ada dan tidak kosong
- [ ] ✅ Parameter `tanggal_absen` format YYYY-MM-DD
- [ ] ✅ Parameter `reason` ada dan tidak kosong
- [ ] ✅ Parameter `reason` minimal 10 karakter
- [ ] ✅ Token masih valid (belum expired)

---

## 🔍 Contoh Response

### Success (201 Created)
```json
{
    "success": true,
    "message": "Permintaan override telah dikirim. Menunggu persetujuan Manager SDM.",
    "data": {
        "absensi_id": 123,
        "status": "pending",
        "attendance_type": "dinas_luar",
        "message": "Permintaan override berhasil dikirim ke Manager SDM"
    }
}
```

### Error 422 - Validation
```json
{
    "message": "The tanggal absen field is required. (and 1 more error)",
    "errors": {
        "tanggal_absen": [
            "The tanggal absen field is required."
        ],
        "reason": [
            "The reason field must be at least 10 characters."
        ]
    }
}
```

### Error 400 - Duplicate Request
```json
{
    "success": false,
    "message": "Anda sudah memiliki permintaan override yang sedang diproses untuk tanggal ini.",
    "error": null
}
```

### Error 401 - Unauthenticated
```json
{
    "success": false,
    "message": "Authentication required",
    "error": "Missing or invalid authorization header"
}
```

---

## 🐛 Troubleshooting

### Error: "The tanggal absen field is required"

**Penyebab:**
- Parameter tidak dikirim
- Nama parameter salah (typo)
- Body request kosong

**Solusi:**
```kotlin
// ❌ SALAH
val body = JSONObject().apply {
    put("tanggalAbsen", date)  // SALAH! Pakai underscore
}

// ✅ BENAR
val body = JSONObject().apply {
    put("tanggal_absen", date)  // BENAR! Sesuai dokumentasi API
}
```

### Error: "The reason field must be at least 10 characters"

**Penyebab:**
- Alasan terlalu pendek (kurang dari 10 karakter)

**Solusi:**
```kotlin
// ❌ SALAH
val reason = "Lupa"  // Terlalu pendek (4 karakter)

// ✅ BENAR
val reason = "Lupa melakukan check-in karena langsung meeting pagi"  // Lebih dari 10 karakter
```

### Error 405: "Method Not Allowed"

**Penyebab:**
- Menggunakan GET instead of POST
- URL salah

**Solusi:**
```kotlin
// ❌ SALAH
.url("$BASE_URL/api/v1/attendance/request-override")  // URL salah!
.get()  // Method salah!

// ✅ BENAR
.url("$BASE_URL/api/v1/attendance/override-request")  // URL benar
.post(requestBody)  // Method POST
```

---

## 📞 Bantuan Lebih Lanjut

Jika masih ada error setelah mengikuti panduan ini:

1. **Aktifkan debug logging** di mobile app
2. **Copy-paste log lengkap** termasuk:
   - URL yang dikirim
   - Headers
   - Body request
   - Response status code
   - Response body
3. **Share dengan backend team** untuk troubleshooting

### Contoh Log yang Baik:

```
=== OVERRIDE REQUEST DEBUG ===
URL: https://api.example.com/api/v1/attendance/override-request
Method: POST
Headers:
  Authorization: Bearer eyJ0eXAiOiJKV1Qi...
  Content-Type: application/json
  Accept: application/json
Request Body:
{
  "tanggal_absen": "2025-10-31",
  "reason": "Dinas luar ke client Jakarta untuk presentasi",
  "attendance_type": "dinas_luar",
  "latitude": -6.2088,
  "longitude": 106.8456
}

Response Code: 422
Response Body:
{
  "message": "The tanggal absen field is required.",
  "errors": {
    "tanggal_absen": ["The tanggal absen field is required."]
  }
}
```

Dengan log seperti ini, backend team bisa langsung identifikasi masalahnya!
