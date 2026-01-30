# Chronic Diseases API Contract (Flutter)

This document is the API contract for the **Chronic Diseases** feature in Hakeem (patient-scoped chronic conditions). It is intended for Flutter developers integrating against the Hakeem backend. The API is **implemented** in the backend.

---

## 1. Overview and Authentication

### Base URL

All routes are prefixed with `/api`. Base URL: `https://hakeem.mustafafares.com/api`.

### Authentication

All Chronic Diseases endpoints require authentication via **Laravel Sanctum**:

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

## 2. Chronic Diseases Endpoints

### List Chronic Diseases

**`GET /api/chronic_diseases`**

Used for: **Patient profile – “Chronic Diseases” tab**; filter by `patientId` to show one patient’s chronic diseases.

**Query parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `perPage` | integer | Page size (1–100). Default: 20 |
| `filter[patientId]` | string (UUID) | Filter by patient |
| `filter[title]` | string | Partial match on title |
| `filter[createdAfter]` | date | Created at ≥ |
| `filter[createdBefore]` | date | Created at ≤ |
| `search` | string | Search across patient_id and title |
| `sort` | string | `patientId`, `-patientId`, `title`, `-title`. Default: `-created_at` |

**Response:** Paginated collection with `data`, `links`, `meta`. Each item follows the Chronic Disease resource shape below.

---

### Create Chronic Disease

**`POST /api/chronic_diseases`**

Used for: **New Patient form** or **Patient profile – Chronic Diseases** “+ Add”.

**Request body:**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `patientId` | string (UUID) | Yes | Must exist in `patients` |
| `title` | string | Yes | Max 255 |

**Response:** `201 Created`. Full chronic disease resource in `data` and `message`.

---

### Show Chronic Disease

**`GET /api/chronic_diseases/{id}`**

**Response:** `200 OK`. Single chronic disease resource.

---

### Update Chronic Disease

**`PUT /api/chronic_diseases/{id}`** or **`PATCH /api/chronic_diseases/{id}`**

**Request body:** Same fields as create, both optional: `patientId` (required when present), `title` (required when present).

**Response:** `200 OK`. Full chronic disease resource.

---

### Delete Chronic Disease

**`DELETE /api/chronic_diseases/{id}`**

**Response:** `200 OK`. Deleted resource in `data` and `message`.

---

### Chronic Disease Resource Shape (camelCase)

| Field | Type | Description |
|-------|------|-------------|
| `id` | string (UUID) | Primary key |
| `patientId` | string (UUID) | Patient reference |
| `title` | string | Condition title/description |
| `createdAt` | string | ISO date-time |
| `updatedAt` | string | ISO date-time |

---

## 3. Related Endpoints and UI

- **Patient profile – Chronic Diseases tab:** `GET /api/chronic_diseases?filter[patientId]={patientId}`. See [Patient Management API contract](patient-management-api-contract.md).
- **New Patient form – Chronic Diseases “+ Add”:** After creating the patient, call `POST /api/chronic_diseases` with the new `patientId` for each entry.

---

## 4. Request/Response Example

### List Chronic Diseases for a Patient

**Request**

```http
GET /api/chronic_diseases?filter[patientId]=9d4e2c1a-1234-5678-abcd-000000000001&sort=-created_at
Authorization: Bearer <token>
```

**Response (200 OK)** — Paginated list; `data` is an array of chronic disease resources.

### Create Chronic Disease

**Request**

```http
POST /api/chronic_diseases
Content-Type: application/json
Authorization: Bearer <token>
```

```json
{
  "patientId": "9d4e2c1a-1234-5678-abcd-000000000001",
  "title": "Ibuprofen 200 mg, take 3 times a day with food for 3 days."
}
```

**Response (201 Created)** — `data` contains full chronic disease resource; `message` in body.

---

## 5. Error Handling and Validation

| Status | Meaning |
|--------|---------|
| **401** | Unauthenticated |
| **403** | Forbidden |
| **404** | Chronic disease not found |
| **422** | Validation failed (e.g. invalid or missing patientId, title) |

**Validation (summary):** Create: `patientId` required, UUID, must exist in patients; `title` required, max 255. Update: same fields optional but when provided must pass same rules.

---

## 6. Flutter-Oriented Notes

- Use **camelCase** for all JSON keys. IDs are **UUID** strings.
- Pagination: `meta.current_page`, `meta.last_page`, `links.next` / `links.prev`, `perPage`.
- For the patient’s Chronic Diseases tab, always filter by `patientId`.

---

## 7. Implementation Status

The Chronic Diseases API is **implemented** in the backend.
