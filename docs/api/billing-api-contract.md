# Billing API Contract (Flutter)

This document is the API contract for the **Billing** feature in Hakeem (incoming and outgoing financial entries linked to patients, users, and medical records). It is intended for Flutter developers integrating against the Hakeem backend. The API is **implemented** in the backend.

---

## 1. Overview and Authentication

### Base URL

All routes are prefixed with `/api`. Base URL: `https://hakeem.mustafafares.com/api`.

### Authentication

All Billing endpoints require authentication via **Laravel Sanctum**:

- **Header:** `Authorization: Bearer <token>`
- **Obtain token:** `POST /api/auth/login` with `username` and `password`.

Unauthenticated requests receive **401 Unauthorized**.

### Tenancy

The backend is multi-tenant. The authenticated user's tenant context is applied automatically; you do **not** send a tenant ID in requests (though `tenantId` can be sent as nullable in request body for store/update).

### Request and Response Format

- **Content-Type:** `application/json`
- **Request body and query parameters:** **camelCase**
- **Response body:** **camelCase** (Laravel API Resources return camelCase keys)

---

## 2. Enums (Source of Truth for Dropdowns)

### Billing Type

| API Value  | Display Label |
|------------|----------------|
| `incoming` | Incoming       |
| `outgoing` | Outgoing       |

### Outgoing Type (when type is `outgoing`)

| API Value  | Display Label |
|------------|----------------|
| `medicine`  | Medicine      |
| `equipment` | Equipment    |

---

## 3. Billings Endpoints

### List Billings

**`GET /api/billings`**

**Query parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `perPage` | integer | Page size (1–100). Default: 20 |
| `filter[type]` | string | Exact: `incoming` or `outgoing` |
| `filter[patientId]` | UUID | Filter by patient; use `null` for no patient |
| `filter[userId]` | UUID | Filter by user; use `null` for no user |
| `filter[medicalRecordId]` | UUID | Filter by medical record; use `null` for none |
| `filter[tenantId]` | UUID | Filter by tenant; use `null` for unassigned |
| `filter[date]` | string | Partial match on date |
| `filter[createdAfter]` | date | Created at ≥ |
| `filter[createdBefore]` | date | Created at ≤ |
| `search` | string | Search across case_name, item_name, type, outgoing_type |
| `sort` | string | `type`, `-type`, `patientId`, `-patientId`, `userId`, `-userId`, `medicalRecordId`, `-medicalRecordId`, `tenantId`, `-tenantId`, `date`, `-date`, `createdAt`, `-createdAt`. Default: `-created_at` |

**Response:** Paginated collection with `data`, `links`, `meta`. Each item follows the Billing resource shape below.

---

### Create Billing

**`POST /api/billings`**

**Request body:**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `type` | string | Yes | `incoming` or `outgoing` |
| `date` | string | Yes | Date, format `Y-m-d` |
| `tenantId` | UUID | No | Max 36 chars |
| `patientId` | UUID | No | Max 36 chars |
| `userId` | UUID | No | Max 36 chars |
| `medicalRecordId` | UUID | No | Max 36 chars |
| `caseName` | string | When type=incoming | Max 255 |
| `paidAmount` | number | When type=incoming | Numeric, ≥ 0 |
| `totalCost` | number | When type=incoming | Numeric, ≥ 0 |
| `itemName` | string | When type=outgoing | Max 255 |
| `quantity` | integer | When type=outgoing | Integer, ≥ 0 |
| `amount` | number | When type=outgoing | Numeric, ≥ 0 |
| `outgoingType` | string | When type=outgoing | `medicine` or `equipment` |

When `type` is `incoming`, `caseName`, `paidAmount`, and `totalCost` are required. When `type` is `outgoing`, `itemName`, `quantity`, `amount`, and `outgoingType` are required.

**Response:** `201 Created`. Body includes `data` (full billing resource) and `message`.

---

### Show Billing

**`GET /api/billings/{id}`**

**Response:** `200 OK`. Single billing resource.

---

### Update Billing

**`PUT /api/billings/{id}`** or **`PATCH /api/billings/{id}`**

**Request body:** Same fields as create, all optional (`sometimes`). When `type` is present and `incoming`, `caseName`, `paidAmount`, `totalCost` are conditionally required; when `type` is `outgoing`, `itemName`, `quantity`, `amount`, `outgoingType` are conditionally required.

**Response:** `200 OK`. Full billing resource.

---

### Delete Billing

**`DELETE /api/billings/{id}`**

**Response:** `200 OK`. Deleted resource in `data` and `message`.

---

## 4. Billing Resource Shape (camelCase)

| Field | Type | Description |
|-------|------|-------------|
| `id` | string (UUID) | Primary key |
| `tenantId` | string (UUID) | Tenant reference |
| `type` | string | `incoming` or `outgoing` |
| `date` | string | Date only, `YYYY-MM-DD` |
| `patientId` | string (UUID) \| null | Patient reference |
| `userId` | string (UUID) \| null | User reference |
| `medicalRecordId` | string (UUID) \| null | Medical record reference |
| `caseName` | string \| null | Case name (incoming) |
| `paidAmount` | number \| null | Paid amount (incoming) |
| `totalCost` | number \| null | Total cost (incoming) |
| `itemName` | string \| null | Item name (outgoing) |
| `quantity` | integer \| null | Quantity (outgoing) |
| `amount` | number \| null | Amount (outgoing) |
| `outgoingType` | string \| null | `medicine` or `equipment` (outgoing) |
| `createdAt` | string | ISO date-time |
| `updatedAt` | string | ISO date-time |

---

## 5. Error Handling and Validation

| Status | Meaning | Typical cause |
|--------|---------|----------------|
| **401 Unauthorized** | Unauthenticated | Missing or invalid Bearer token |
| **403 Forbidden** | Not allowed | User lacks permission (policy) |
| **404 Not Found** | Resource missing | Invalid UUID or deleted billing |
| **422 Unprocessable Entity** | Validation failed | Invalid or missing required fields for type (incoming vs outgoing) |

---

## 6. Implementation Status

The Billing API is **implemented** in the backend. Base path: `/api/billings`. All endpoints live under `auth:sanctum` and tenant + subscription middleware.
