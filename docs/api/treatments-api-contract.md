# Treatments API Contract (Flutter)

This document is the API contract for the **Treatments** feature in Hakeem (treatment types / services). It is intended for Flutter developers integrating against the Hakeem backend. Treatments are used as a lookup in medical record treatment sessions and can be referenced in appointments. The API is **implemented** in the backend.

---

## 1. Overview and Authentication

### Base URL

All routes are prefixed with `/api`. Base URL: `https://hakeem.mustafafares.com/api`.

### Authentication

All Treatment endpoints require authentication via **Laravel Sanctum**:

- **Header:** `Authorization: Bearer <token>`
- **Obtain token:** `POST /api/auth/login` with `username` and `password` (or `email` and `password`, depending on backend configuration).

Unauthenticated requests receive **401 Unauthorized**.

### Tenancy

The backend is multi-tenant. The authenticated user’s tenant context is applied automatically; you do **not** send a tenant ID in requests.

### Request and Response Format

- **Content-Type:** `application/json`
- **Request body and query parameters:** **camelCase**
- **Response body:** **camelCase** (Laravel API Resources)

---

## 2. Treatments Endpoints

### List Treatments

**`GET /api/treatments`**

Used for: **“Treatment Name” dropdown** in medical record treatment sessions; appointment type/label. See [Medical Record API contract](medical-record-api-contract.md) and [Booking API contract](booking-api-contract.md).

**Query parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `perPage` | integer | Page size (1–100). Default: 20 |
| `filter[name]` | string | Partial match on name |
| `filter[description]` | string | Partial match on description |
| `filter[createdAfter]` | date | Created at ≥ |
| `filter[createdBefore]` | date | Created at ≤ |
| `search` | string | Search across name and description |
| `sort` | string | `name`, `-name`, `defaultCost`, `-defaultCost`. Default: `-created_at` |

**Response:** Paginated collection with `data`, `links`, `meta`. Each item follows the Treatment resource shape below.

---

### Create Treatment

**`POST /api/treatments`**

**Request body:**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `name` | string | Yes | Max 255 |
| `description` | string | No | Max 1000 |
| `defaultCost` | number | No | Numeric, ≥ 0 |

**Response:** `201 Created`. Full treatment resource in `data` and `message`.

---

### Show Treatment

**`GET /api/treatments/{id}`**

**Response:** `200 OK`. Single treatment resource.

---

### Update Treatment

**`PUT /api/treatments/{id}`** or **`PATCH /api/treatments/{id}`**

**Request body:** Same fields as create, all optional: `name`, `description`, `defaultCost`.

**Response:** `200 OK`. Full treatment resource.

---

### Delete Treatment

**`DELETE /api/treatments/{id}`**

**Response:** `200 OK`. Deleted resource in `data` and `message`.

---

### Treatment Resource Shape (camelCase)

| Field | Type | Description |
|-------|------|-------------|
| `id` | string (UUID) | Primary key |
| `name` | string | Treatment name (e.g. “Initial Cleaning & Cavity Preparation”) |
| `description` | string \| null | Description |
| `defaultCost` | string (decimal) | Default cost |
| `createdAt` | string | ISO date-time |
| `updatedAt` | string | ISO date-time |

---

## 3. Related Endpoints and UI

- **Medical Record – treatment session “Treatment Name”:** Use `GET /api/treatments` for dropdown; pass selected ID as `treatmentId` when creating/updating a medical record treatment. See [Medical Record API contract](medical-record-api-contract.md).
- **Booking – appointment type:** Treatments may be used to label or categorize appointments; see [Booking API contract](booking-api-contract.md).
- **Tooth Details card:** May display treatment name from the treatment’s `treatment` relation.

---

## 4. Request/Response Example

### List Treatments (for dropdown)

**Request**

```http
GET /api/treatments?perPage=50&sort=name
Authorization: Bearer <token>
```

**Response (200 OK)** — Paginated list; `data` is an array of treatment resources.

### Create Treatment

**Request**

```http
POST /api/treatments
Content-Type: application/json
Authorization: Bearer <token>
```

```json
{
  "name": "Initial Cleaning & Cavity Preparation",
  "description": "Standard cleaning and cavity prep",
  "defaultCost": 150
}
```

**Response (201 Created)** — `data` contains full treatment resource; `message` in body.

---

## 5. Error Handling and Validation

| Status | Meaning |
|--------|---------|
| **401** | Unauthenticated |
| **403** | Forbidden |
| **404** | Treatment not found |
| **422** | Validation failed (e.g. missing name, invalid defaultCost) |

**Validation (summary):** Create: `name` required, max 255; `description` optional, max 1000; `defaultCost` optional, numeric, min 0. Update: same fields optional.

---

## 6. Flutter-Oriented Notes

- Use **camelCase** for all JSON keys. IDs are **UUID** strings.
- `defaultCost` is returned as a string (decimal). Pagination: `meta.current_page`, `meta.last_page`, `links.next` / `links.prev`, `perPage`.

---

## 7. Implementation Status

The Treatments API is **implemented** in the backend.
