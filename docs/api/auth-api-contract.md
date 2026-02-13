# Auth API Contract (Flutter)

This document is the API contract for **Authentication** in Hakeem. It is intended for Flutter developers integrating against the Hakeem backend. It covers login, logout, forgot password, reset password, update profile, and change password. The API is **implemented** in the backend.

---

## 1. Overview

### Base URL

All routes are prefixed with `/api`. Base URL: `https://hakeem.mustafafares.com/api`.

### Authentication

- **Login** and **forgot/reset password** endpoints do **not** require a Bearer token.
- **Logout**, **update-info**, and **change-password** require **Laravel Sanctum** authentication:
  - **Header:** `Authorization: Bearer <token>`
  - Obtain the token via `POST /api/auth/login` with `username` and `password`.

Unauthenticated requests to protected endpoints receive **401 Unauthorized**.

### Request and Response Format

- **Content-Type:** `application/json` for request body (use `multipart/form-data` when uploading `primaryImage` on update-info).
- **Request body and query parameters:** **camelCase**.
- **Response body:** **camelCase** (Laravel API Resources return camelCase keys).

---

## 2. Auth Endpoints

### Login

**`POST /api/auth/login`**

Authenticates the user and returns an access token and user data.

**Request body:**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `username` | string | Yes | Min 3, max 191; must exist in `users` |
| `password` | string | Yes | Min 1, max 255 |

**Response:** `200 OK`. Auth resource (see Auth Resource Shape below) and `message` (e.g. "Log in successfully").

**Errors:**

- **401 Unauthorized** — Invalid credentials.
- **403 Forbidden** — Email not verified (for non-admin users).
- **422 Unprocessable Entity** — Validation failed (e.g. username does not exist).

---

### Logout

**`POST /api/auth/logout`**

**Authentication:** Required (Bearer token).

Invalidates the current access token.

**Request body:** None.

**Response:** `200 OK`. JSON with `message` (e.g. "Logged out successfully").

---

### Forgot Password

**`POST /api/auth/forget-password`**

Sends a password reset OTP to the user's email. The OTP expires after 15 minutes.

**Request body:**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `email` | string | Yes | Valid email; must exist in `users` |

**Response:** `200 OK`. JSON with `message` (status message).

**Errors:**

- **422 Unprocessable Entity** — Email invalid or not found.

---

### Reset Password

**`POST /api/auth/reset-password`**

Resets the user's password using the OTP sent by forgot-password.

**Request body:**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `email` | string | Yes | Valid email; must exist in `users` |
| `otp` | string | Yes | Exactly 6 characters |
| `password` | string | Yes | Min 8, max 191; must be confirmed |
| `password_confirmation` | string | Yes | Must match `password` |

**Response:** `200 OK`. JSON with `message`.

**Errors:**

- **403 Forbidden** — Invalid or expired OTP.
- **422 Unprocessable Entity** — Validation failed.

---

### Update Profile (Update Info)

**`PUT /api/auth/update-info`**

**Authentication:** Required (Bearer token).

Updates the authenticated user's profile. Accepts the same payload as the User update endpoint (see [User Management API contract](user-management-api-contract.md)).

**Request body:** Same fields as User update: `name`, `username`, `email`, `phoneNumber`, `language`, `timeFormat`, `primaryImage` (file when multipart). All optional. Unique rules for `username` and `email` ignore the current user.

**Response:** `200 OK`. Auth resource with updated `user` and `token: null`, plus `message` (e.g. "Profile updated successfully").

---

### Change Password

**`PUT /api/auth/change-password`**

**Authentication:** Required (Bearer token).

Changes the authenticated user's password.

**Request body:**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `currentPassword` | string | Yes | Must match current password (Sanctum) |
| `newPassword` | string | Yes | Min 8; must be confirmed |
| `newPassword_confirmation` | string | Yes | Must match `newPassword` |

**Response:** `200 OK`. JSON with `message` (e.g. "Password changed successfully").

**Errors:**

- **422 Unprocessable Entity** — Current password incorrect or validation failed.

---

## 3. Auth Resource Shape (camelCase)

Returned by **login** and **update-info** (with `token: null` for update-info).

| Field | Type | Description |
|-------|------|-------------|
| `token` | string \| null | Bearer token (null on update-info) |
| `tokenType` | string | Always `"Bearer"` |
| `user` | object | User resource (see [User Management API contract](user-management-api-contract.md)) |
| `subscriptionStatus` | object \| null | Subscription status when present (canUseApp, reason, trialEndsAt, activeUntil, hasPendingOrder) |

---

## 4. Request/Response Examples

### Login

**Request**

```http
POST /api/auth/login
Content-Type: application/json
```

```json
{
  "username": "doctor_clinic",
  "password": "password123"
}
```

**Response (200 OK)**

```json
{
  "data": {
    "token": "1|abc123...",
    "tokenType": "Bearer",
    "user": {
      "id": "9d3e8c1a-4f2b-4a5e-8c3d-1b2a3c4d5e6f",
      "name": "Dr. Ahmed Ali",
      "username": "doctor_clinic",
      "email": "ahmed@example.com",
      "phoneNumber": "+963912345678",
      "language": "en",
      "timeFormat": "12hr",
      "primaryImage": null,
      "tenant": null,
      "createdAt": "2025-01-01 12:00:00",
      "updatedAt": "2025-01-31 12:00:00"
    },
    "subscriptionStatus": {
      "canUseApp": true,
      "reason": "active",
      "trialEndsAt": null,
      "activeUntil": "2026-01-31 12:00:00",
      "hasPendingOrder": false
    }
  },
  "message": "Log in successfully"
}
```

### Reset Password

**Request**

```http
POST /api/auth/reset-password
Content-Type: application/json
```

```json
{
  "email": "user@example.com",
  "otp": "123456",
  "password": "newPassword123",
  "password_confirmation": "newPassword123"
}
```

---

## 5. Error Handling and Validation

| Status | Meaning | Typical cause |
|--------|---------|----------------|
| **401 Unauthorized** | Not authenticated | Missing or invalid Bearer token (logout, update-info, change-password) |
| **403 Forbidden** | Not allowed | Invalid/expired reset OTP; or email not verified on login |
| **422 Unprocessable Entity** | Validation failed | Invalid or missing fields; username/email not found; password rules |

**422 response body** example:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "username": ["The selected username is invalid."],
    "password": ["The password field is required."]
  }
}
```

---

## 6. Flutter-Oriented Notes

- **Login:** Use **username** (not email) with password. Store the returned `token` and send it as `Authorization: Bearer <token>` on all subsequent requests except login, forgot-password, and reset-password.
- **Update profile:** Reuse the same request shape as User update; the backend returns Auth resource with updated user and `token: null` (keep using the existing token).
- **Subscription status:** After login, use `subscriptionStatus` to gate app access (e.g. `canUseApp`, `reason`, `activeUntil`). See [Subscription and Tenant Types API contract](subscription-and-tenant-types-api-contract.md) for full subscription status shape.

---

## 7. Implementation Status

The Auth API is **implemented** in the backend. Base path: `/api/auth`. All endpoints are live and follow the shapes above.
