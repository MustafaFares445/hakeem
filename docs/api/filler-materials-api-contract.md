# Filler Materials API Contract (Flutter)

This document is the API contract for the **Filler Materials** feature in Hakeem. It is intended for Flutter developers integrating against the Hakeem backend. Filler materials are used as a lookup in medical record treatment sessions (e.g. Composite resin, Amalgam, IRM). The API is **implemented** in the backend.

---

## 1. Overview and Authentication

### Base URL

All routes are prefixed with `/api`. Base URL: `https://hakeem.mustafafares.com/api`.

### Authentication

All Filler Material endpoints require authentication via **Laravel Sanctum**:

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

## 2. Filler Materials Endpoints

### List Filler Materials

**`GET /api/filler-materials`**

Used for: **“Filler Material” dropdown** in medical record treatment sessions. See [Medical Record API contract](medical-record-api-contract.md).

**Query parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `perPage` | integer | Page size (1–100). Default: 20 |
| `filter[name]` | string | Partial match on name |
| `filter[description]` | string | Partial match on description |
| `filter[isActive]` | boolean | Exact match (true/false) |
| `filter[createdAfter]` | date | Created at ≥ |
| `filter[createdBefore]` | date | Created at ≤ |
| `search` | string | Search across name and description |
| `sort` | string | `name`, `-name`, `createdAt`, `-createdAt`. Default: `-created_at` |

**Response:** Paginated collection with `data`, `links`, `meta`. Each item follows the Filler Material resource shape below.

---

### Create Filler Material

**`POST /api/filler-materials`**

**Request body:**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `name` | string | Yes | Max 255 |
| `description` | string | No | Max 1000 |
| `isActive` | boolean | No | Default true when omitted |

**Response:** `201 Created`. Full filler material resource in `data` and `message`.

---

### Show Filler Material

**`GET /api/filler-materials/{id}`**

**Response:** `200 OK`. Single filler material resource.

---

### Update Filler Material

**`PUT /api/filler-materials/{id}`** or **`PATCH /api/filler-materials/{id}`**

**Request body:** Same fields as create, all optional: `name`, `description`, `isActive`.

**Response:** `200 OK`. Full filler material resource.

---

### Delete Filler Material

**`DELETE /api/filler-materials/{id}`**

**Response:** `200 OK`. Deleted resource in `data` and `message`.

---

### Filler Material Resource Shape (camelCase)

| Field | Type | Description |
|-------|------|-------------|
| `id` | string (UUID) | Primary key |
| `name` | string | Material name (e.g. “Composite resin”, “Amalgam Filling”, “Temporary filling (IRM)”) |
| `description` | string \| null | Description |
| `isActive` | boolean | Whether the material is active for selection |
| `createdAt` | string | ISO date-time |
| `updatedAt` | string | ISO date-time |

---

## 3. Related Endpoints and UI

- **Medical Record – treatment session “Filler Material”:** Use `GET /api/filler-materials` for dropdown; pass selected ID as `fillerMaterialId` when creating/updating a medical record treatment (required). See [Medical Record API contract](medical-record-api-contract.md).
- **Tooth Overview legend / Tooth Details:** May display filler material name and map to chart colors (e.g. Composite resin, Amalgam, IRM).

---

## 4. Request/Response Example

### List Filler Materials (for dropdown)

**Request**

```http
GET /api/filler-materials?perPage=50&sort=name&filter[isActive]=true
Authorization: Bearer <token>
```

**Response (200 OK)** — Paginated list; `data` is an array of filler material resources.

### Create Filler Material

**Request**

```http
POST /api/filler-materials
Content-Type: application/json
Authorization: Bearer <token>
```

```json
{
  "name": "Composite resin",
  "description": "Tooth-colored filling material",
  "isActive": true
}
```

**Response (201 Created)** — `data` contains full filler material resource; `message` in body.

---

## 5. Error Handling and Validation

| Status | Meaning |
|--------|---------|
| **401** | Unauthenticated |
| **403** | Forbidden |
| **404** | Filler material not found |
| **422** | Validation failed (e.g. missing name) |

**Validation (summary):** Create: `name` required, max 255; `description` optional, max 1000; `isActive` optional, boolean. Update: same fields optional.

---

## 6. Flutter-Oriented Notes

- Use **camelCase** for all JSON keys. IDs are **UUID** strings.
- For “Select Filler Material” dropdown, you may filter by `filter[isActive]=true` to show only active materials. Pagination: `meta.current_page`, `meta.last_page`, `links.next` / `links.prev`, `perPage`.

---

## 7. Implementation Status

The Filler Materials API is **implemented** in the backend.
