# Admin Absensi API Documentation

## Overview
This API provides comprehensive endpoints for the PTVisdatTeknikUtama Android application to communicate with the admin-absensi Laravel backend. The API supports employee authentication, attendance management, location validation, and data synchronization.

## Base URL
```
http://your-domain.com/api/v1
```

## Authentication
The API uses session-based authentication with the `karyawan` guard. After successful login, subsequent requests will be authenticated using session cookies.

## Response Format
All API responses follow a consistent JSON format:

### Success Response
```json
{
    "success": true,
    "message": "Operation successful",
    "data": {
        // Response data here
    }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error message",
    "errors": "Additional error details (optional)"
}
```

## API Endpoints

### Public Endpoints (No Authentication Required)

#### 1. Employee Login
**POST** `/api/v1/employee/login`

Login an employee to the system.

**Request Body:**
```json
{
    "email": "employee@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "employee@example.com",
        "username": "johndoe",
        "department": "IT Department",
        "position": "Software Developer",
        "status": "active"
    }
}
```

#### 2. Get Work Locations
**GET** `/api/v1/locations`

Retrieve all work locations with geofencing data.

**Response:**
```json
{
    "success": true,
    "message": "Locations retrieved successfully",
    "data": [
        {
            "id": 1,
            "lokasi_kerja": "Main Office",
            "latitude": -6.200000,
            "longitude": 106.816666,
            "radius": 100
        }
    ]
}
```

#### 3. Get Departments
**GET** `/api/v1/departments`

Retrieve all departments.

**Response:**
```json
{
    "success": true,
    "message": "Departments retrieved successfully",
    "data": [
        {
            "id_departemen": 1,
            "nama_departemen": "IT Department"
        }
    ]
}
```

#### 4. Get Positions
**GET** `/api/v1/positions/{departmentId?}`

Retrieve positions, optionally filtered by department.

**Response:**
```json
{
    "success": true,
    "message": "Positions retrieved successfully",
    "data": [
        {
            "id_posisi": 1,
            "nama_posisi": "Software Developer",
            "id_departemen": 1
        }
    ]
}
```

#### 5. Get App Settings
**GET** `/api/v1/settings`

Retrieve application settings and configuration.

**Response:**
```json
{
    "success": true,
    "message": "Settings retrieved successfully",
    "data": {
        "app_name": "Admin Absensi",
        "app_version": "1.0.0",
        "timezone": "Asia/Singapore",
        "work_hours": {
            "start_time": "08:00:00",
            "end_time": "17:00:00",
            "break_duration": 60
        },
        "attendance_rules": {
            "late_threshold_minutes": 15,
            "early_departure_threshold_minutes": 30,
            "overtime_start_time": "17:00:00",
            "max_work_hours_per_day": 12
        },
        "photo_requirements": {
            "max_size_mb": 2,
            "allowed_formats": ["jpeg", "jpg", "png"],
            "required_for_checkin": false,
            "required_for_checkout": false
        },
        "location_settings": {
            "validation_required": true,
            "default_radius_meters": 100,
            "gps_accuracy_required_meters": 50
        }
    }
}
```

### Protected Endpoints (Authentication Required)

#### 6. Get Employee Profile
**GET** `/api/v1/employee/profile`

Retrieve the authenticated employee's profile.

**Response:**
```json
{
    "success": true,
    "message": "Profile retrieved successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "employee@example.com",
        "username": "johndoe",
        "department": {
            "id": 1,
            "name": "IT Department"
        },
        "position": {
            "id": 1,
            "name": "Software Developer"
        },
        "status": "active"
    }
}
```

#### 7. Update Employee Profile
**PUT** `/api/v1/employee/profile`

Update the authenticated employee's profile.

**Request Body:**
```json
{
    "name": "John Doe Updated",
    "username": "johndoe_updated",
    "current_password": "current_password",
    "new_password": "new_password",
    "new_password_confirmation": "new_password"
}
```

#### 8. Check In
**POST** `/api/v1/attendance/check-in`

Record employee check-in.

**Request Body (multipart/form-data):**
```
latitude: -6.200000
longitude: 106.816666
foto: [image file] (optional)
overtime: true/false (optional, defaults to false)
```

**Response:**
```json
{
    "success": true,
    "message": "Check-in successful",
    "data": {
        "attendance_id": 1,
        "check_in_time": "08:00:00",
        "date": "2024-01-15",
        "location": "Main Office",
        "coordinates": "-6.200000,106.816666",
        "photo_url": "http://domain.com/storage/attendance_photos/photo.jpg"
    }
}
```

#### 9. Check Out
**POST** `/api/v1/attendance/check-out`

Record employee check-out.

**Request Body (multipart/form-data):**
```
latitude: -6.200000
longitude: 106.816666
foto: [image file] (optional)
```

**Response:**
```json
{
    "success": true,
    "message": "Check-out successful",
    "data": {
        "attendance_id": 1,
        "check_out_time": "17:00:00",
        "date": "2024-01-15",
        "location": "Main Office",
        "coordinates": "-6.200000,106.816666",
        "work_duration": {
            "hours": 9,
            "minutes": 0,
            "total_minutes": 540
        },
        "photo_url": "http://domain.com/storage/attendance_photos/photo.jpg"
    }
}
```

#### 10. Set Overtime
**POST** `/api/v1/attendance/overtime`

Set overtime for today's existing check-in record.

**Request Body (multipart/form-data):**
```
latitude: -6.200000
longitude: 106.816666
foto: [image file] (optional)
keterangan: "Working on urgent project" (optional)
```

**Response:**
```json
{
    "success": true,
    "message": "Overtime set successfully",
    "data": {
        "attendance_id": 1,
        "overtime_set_time": "18:00:00",
        "date": "2024-01-15",
        "location": "Main Office",
        "coordinates": "-6.200000,106.816666",
        "notes": "Working on urgent project",
        "photo_url": "http://domain.com/storage/attendance_photos/photo.jpg"
    }
}
```

#### 11. Get Attendance History
**GET** `/api/v1/attendance/history`

Retrieve employee's attendance history with pagination.

**Query Parameters:**
- `limit` (optional): Number of records per page (1-100, default: 20)
- `page` (optional): Page number (default: 1)
- `start_date` (optional): Filter from date (YYYY-MM-DD)
- `end_date` (optional): Filter to date (YYYY-MM-DD)

**Response:**
```json
{
    "success": true,
    "message": "Attendance history retrieved successfully",
    "data": {
        "attendances": [
            {
                "id": 1,
                "date": "2024-01-15",
                "check_in_time": "08:00:00",
                "check_out_time": "17:00:00",
                "check_in_location": "-6.200000,106.816666",
                "check_out_location": "-6.200000,106.816666",
                "check_in_photo": "http://domain.com/storage/attendance_photos/photo1.jpg",
                "check_out_photo": "http://domain.com/storage/attendance_photos/photo2.jpg",
                "notes": "Working on project",
                "work_duration": {
                    "hours": 9,
                    "minutes": 0,
                    "total_minutes": 540
                },
                "is_lembur": false,
                "status": "Hadir"
            },
            {
                "id": 2,
                "date": "2024-01-16",
                "check_in_time": "08:00:00",
                "check_out_time": "18:30:00",
                "check_in_location": "-6.200000,106.816666",
                "check_out_location": "-6.200000,106.816666",
                "check_in_photo": null,
                "check_out_photo": null,
                "notes": "Overtime work",
                "work_duration": {
                    "hours": 10,
                    "minutes": 30,
                    "total_minutes": 630
                },
                "is_lembur": true,
                "status": "Lembur"
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 5,
            "per_page": 20,
            "total": 100
        }
    }
}
```

#### 12. Get Today's Attendance
**GET** `/api/v1/attendance/today`

Retrieve today's attendance record for the authenticated employee.

#### 13. Get Attendance Summary
**GET** `/api/v1/attendance/summary`

Get attendance statistics for a specific month.

**Query Parameters:**
- `month` (optional): Month (1-12, default: current month)
- `year` (optional): Year (2020-2030, default: current year)

#### 14. Validate Location
**POST** `/api/v1/location/validate`

Validate if coordinates are within allowed work areas.

**Request Body:**
```json
{
    "latitude": -6.200000,
    "longitude": 106.816666
}
```

#### 15. Logout
**POST** `/api/v1/employee/logout`

Logout the authenticated employee.

### Legacy Endpoints (Backward Compatibility)

#### 16. Legacy Login
**POST** `/api/karyawan/login`

Legacy login endpoint for backward compatibility.

#### 17. Legacy Check-in
**POST** `/api/check-in`

Legacy check-in endpoint for backward compatibility.

#### 18. Legacy Check-out
**POST** `/api/check-out`

Legacy check-out endpoint for backward compatibility.

## Error Codes

- `200` - Success
- `400` - Bad Request (validation errors)
- `401` - Unauthorized (authentication required)
- `403` - Forbidden (location outside work area, etc.)
- `404` - Not Found
- `500` - Internal Server Error

## CORS Configuration

The API is configured to accept requests from any origin with the following settings:
- **Allowed Methods:** All HTTP methods
- **Allowed Origins:** All origins (*)
- **Allowed Headers:** All headers
- **Supports Credentials:** Yes

## File Upload Requirements

For photo uploads:
- **Maximum file size:** 2MB
- **Allowed formats:** JPEG, JPG, PNG
- **Field name:** `foto`

## Testing the API

You can test the API using tools like Postman, curl, or directly from the Android application. Make sure to:

1. Start with the login endpoint to establish a session
2. Use the session cookies for subsequent authenticated requests
3. Include proper headers for file uploads when sending photos
4. Validate coordinates are within configured work locations

## Android App Integration

The PTVisdatTeknikUtama Android app should:

1. Use the new v1 API endpoints for better functionality
2. Handle session-based authentication with cookie management
3. Send multipart requests for photo uploads
4. Implement proper error handling based on the standardized response format
5. Use the settings endpoint to get app configuration dynamically

## Notes

- All timestamps are in the server's timezone
- Location validation is required for check-in/check-out operations
- Photos are stored in the `storage/app/public/attendance_photos` directory
- The API maintains backward compatibility with legacy endpoints
