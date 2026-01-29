# Task 3: API Documentation

## REST API Endpoints

Base URL: `http://localhost/task3/php/api.php`

All requests require authentication (must be logged in).

### Authentication
- Uses session-based authentication
- Admin-only endpoints require role_id = 2
- Non-admin users can only access their own data

---

## User Endpoints

### Get Current User
**GET** `/api.php?action=get_user`

Response:
```json
{
  "success": true,
  "data": {
    "user_id": 1,
    "username": "admin",
    "email": "admin@example.com",
    "first_name": "Admin",
    "last_name": "User",
    "phone": "",
    "profile_picture": null,
    "role_name": "Admin",
    "is_active": 1,
    "created_at": "2026-01-24 10:00:00"
  }
}
```

### Get Specific User
**GET** `/api.php?action=get_user&id=1`

Parameters:
- `id` (int): User ID

---

## User List Endpoints

### Get All Users (Admin Only)
**GET** `/api.php?action=get_users&page=1`

Parameters:
- `page` (int, optional): Page number (default: 1)
- Limit: 10 users per page

Response:
```json
{
  "success": true,
  "data": {
    "users": [...],
    "total": 5,
    "pages": 1,
    "current_page": 1
  }
}
```

### Search Users
**GET** `/api.php?action=search&q=admin`

Parameters:
- `q` (string): Search query (min 2 characters)
- Searches: username, email, first_name, last_name

Response:
```json
{
  "success": true,
  "data": [
    {
      "user_id": 1,
      "username": "admin",
      "email": "admin@example.com",
      "first_name": "Admin",
      "last_name": "User",
      "role_name": "Admin"
    }
  ]
}
```

---

## Profile Endpoints

### Update User Profile
**POST** `/api.php?action=update_profile`

Request Body:
```json
{
  "user_id": 1,
  "first_name": "John",
  "last_name": "Doe",
  "phone": "+1234567890"
}
```

Response:
```json
{
  "success": true,
  "message": "Profile updated successfully"
}
```

---

## File Upload Endpoints

### Upload Profile Picture
**POST** `/api.php?action=upload_picture`

Form Data:
- `file` (file): Image file (JPG, PNG, GIF, max 5MB)
- `user_id` (int, optional): User ID (default: current user)

Response:
```json
{
  "success": true,
  "message": "Profile picture uploaded successfully"
}
```

---

## Admin Endpoints

### Delete User (Admin Only)
**POST** `/api.php?action=delete_user`

Request Body:
```json
{
  "user_id": 2
}
```

Response:
```json
{
  "success": true,
  "message": "User deleted successfully"
}
```

### Change User Role (Admin Only)
**POST** `/api.php?action=change_role`

Request Body:
```json
{
  "user_id": 2,
  "role_id": 2
}
```

Role IDs:
- 1 = User
- 2 = Admin

Response:
```json
{
  "success": true,
  "message": "User role updated"
}
```

---

## Error Responses

### 401 Unauthorized
```json
{
  "error": "Unauthorized"
}
```

### 403 Forbidden
```json
{
  "error": "Access denied"
}
```

### 404 Not Found
```json
{
  "error": "Unknown action"
}
```

### 405 Method Not Allowed
```json
{
  "error": "Method not allowed"
}
```

---

## JavaScript Examples

### Fetch User
```javascript
fetch('/task3/php/api.php?action=get_user')
  .then(res => res.json())
  .then(data => console.log(data.data))
```

### Search Users
```javascript
fetch('/task3/php/api.php?action=search&q=john')
  .then(res => res.json())
  .then(data => console.log(data.data))
```

### Upload Profile Picture
```javascript
const formData = new FormData();
formData.append('file', fileInput.files[0]);

fetch('/task3/php/api.php?action=upload_picture', {
  method: 'POST',
  body: formData
})
.then(res => res.json())
.then(data => console.log(data.message))
```

### Update Profile
```javascript
fetch('/task3/php/api.php?action=update_profile', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/x-www-form-urlencoded'
  },
  body: 'first_name=John&last_name=Doe&phone=+1234567890'
})
.then(res => res.json())
.then(data => console.log(data.message))
```

---

## CORS

API endpoints support CORS with:
- Allow-Origin: *
- Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
- Allow-Headers: Content-Type

---

## Rate Limiting

No rate limiting currently implemented. For production, consider:
- IP-based rate limiting
- User-based rate limiting
- Token-based authentication

---

## Authentication

Requests use session-based authentication:
- Login creates a session
- Session cookie required for API calls
- Session expires on browser close
- Admin endpoints check role_id in session

---

## Best Practices

1. **Always check response.success** before using data
2. **Handle error responses** appropriately
3. **Use POST** for state-changing operations
4. **Validate file sizes** on client side
5. **Check permissions** before sensitive operations
6. **Log API errors** for debugging
