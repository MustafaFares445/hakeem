# Clinic API Contract (Flutter)

This document is the API contract for the **Clinic** (current tenant) feature in Hakeem. It is intended for Flutter developers integrating against the Hakeem backend. The clinic is the authenticated user's tenant (e.g. clinic or practice). The API is **implemented** in the backend.

---

## 1. Overview and Authentication

### Base URL

All routes are prefixed with `/api`. Base URL: `https://hakeem.mustafafares.com/api`.

### Authentication

All Clinic endpoints require authentication via **Laravel Sanctum**:

- **Header:** `Authorization: Bearer <token>`
- **Obtain token:** `POST /api/auth/login` with `username` and `password`.

Unauthenticated requests receive **401 Unauthorized**.

### Tenancy

The backend is multi-tenant. The clinic endpoints operate on the **current** tenant for the authenticated user; you do **not** send a tenant ID. The response is the tenant (clinic) the user belongs to.

### Request and Response Format

- **Content-Type:** `application/json` for request body (use `multipart/form-data` when uploading `primaryImage` on update).
- **Request body and query parameters:** **camelCase**
- **Response body:** **camelCase** (Laravel API Resources return camelCase keys)

---

## 2. Clinic Endpoints

### Get Current Clinic

**`GET /api/clinic`**

Returns the current tenant (clinic) for the authenticated user, with `tenantType` loaded.

**Request body:** None.

**Response:** `200 OK`. Single tenant (clinic) resource in `data` and `message`. Shape below (Clinic/tenant resource shape).

---

### Update Clinic

**`PUT /api/clinic`**

Updates the current tenant (clinic). Number of doctors and secretaries are calculated automatically from users with those roles; they are read-only in the response.

**Request body:** All fields optional (`sometimes` or `nullable`). Send as **`multipart/form-data`** when including `primaryImage`.

| Field | Type | Validation |
|-------|------|------------|
| `clinicName` | string | Max 255 (maps to clinic name) |
| `phoneNumber` | string | Max 50 |
| `phoneNumber2` | string | Max 50 |
| `specialties` | array | Array of strings; each max 255 |
| `mapPin` | object | Optional; can include `lat`, `lng` |
| `city` | string | Max 255 |
| `address` | string | No max |
| `instagram` | string | Max 255 |
| `facebook` | string | Max 255 |
| `startWorkingDay` | string | Max 100 |
| `endWorkingDay` | string | Max 100 |
| `startWorkingTime` | string | Time format `H:i` (e.g. `09:00`) |
| `endWorkingTime` | string | Time format `H:i` (e.g. `18:00`) |
| `primaryImage` | file | Image (png, jpg, svg, webp), max 3000 KB |

**Response:** `200 OK`. Full tenant (clinic) resource with `tenantType` loaded, plus `message`.

---

## 3. Clinic (Tenant) Resource Shape (camelCase)

| Field | Type | Description |
|-------|------|-------------|
| `id` | string (UUID) | Primary key |
| `name` | string | Clinic name |
| `tenantTypeId` | string (UUID) | Tenant type reference |
| `tenantType` | object \| null | Tenant type resource when loaded (id, key, name, description, isActive, createdAt, updatedAt) |
| `data` | object \| array | Tenant custom data |
| `phoneNumber` | string \| null | Primary phone |
| `phoneNumber2` | string \| null | Secondary phone |
| `specialties` | array \| null | List of specialty strings |
| `numberOfDoctors` | integer \| null | Computed from users with doctor role (read-only) |
| `numberOfSecretaries` | integer \| null | Computed from users with secretary role (read-only) |
| `mapPin` | object \| null | e.g. `{ "lat": 24.0, "lng": 46.0 }` |
| `city` | string \| null | City |
| `address` | string \| null | Address |
| `instagram` | string \| null | Instagram URL or handle |
| `facebook` | string \| null | Facebook URL or handle |
| `startWorkingDay` | string \| null | Start working day |
| `endWorkingDay` | string \| null | End working day |
| `startWorkingTime` | string \| null | Start time (e.g. `09:00`) |
| `endWorkingTime` | string \| null | End time (e.g. `18:00`) |
| `trialStartsAt` | string \| null | ISO date-time |
| `trialEndsAt` | string \| null | ISO date-time |
| `domainName` | string \| null | Tenant domain |
| `createdAt` | string \| null | ISO date-time |
| `updatedAt` | string \| null | ISO date-time |

**Note:** Primary image for the clinic is stored via Spatie Media Library; the API may expose it in a nested or separate structure. Confirm with backend if you need a dedicated `primaryImage` URL in this resource.

---

## 4. Error Handling and Validation

| Status | Meaning | Typical cause |
|--------|---------|----------------|
| **401 Unauthorized** | Unauthenticated | Missing or invalid Bearer token |
| **403 Forbidden** | Not allowed | User lacks permission |
| **422 Unprocessable Entity** | Validation failed | Invalid field format (e.g. time not H:i, image type/size) |

---

## 5. Implementation Status

The Clinic API is **implemented** in the backend. Base path: `/api/clinic`. Endpoints live under `auth:sanctum` and tenant + subscription middleware.
