# Chronic Medications API Contract (Flutter)

This document is the API contract for the **Chronic Medications** feature in Hakeem (patient-scoped chronic medications). It is intended for Flutter developers integrating against the Hakeem backend. The API is **implemented** in the backend.

---

## 1. Overview and Authentication

### Base URL

All routes are prefixed with `/api`. Base URL: `https://hakeem.mustafafares.com/api`.

### Authentication

All Chronic Medications endpoints require authentication via **Laravel Sanctum**:

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

## 2. Chronic Medications Endpoints

### List Chronic Medications

**`GET /api/chronic_medications`**

Used for: **Patient profile – “Chronic Medications” tab**; filter by `patientId` to show one patient’s chronic medications.

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

**Response:** Paginated collection with `data`, `links`, `meta`. Each item follows the Chronic Medication resource shape below.

---

### Create Chronic Medication

**`POST /api/chronic_medications`**

Used for: **New Patient form** or **Patient profile – Chronic Medications** “+ Add”.

**Request body:**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `patientId` | string (UUID) | Yes | Must exist in `patients` |
| `title` | string | Yes | Max 255 |

**Response:** `201 Created`. Full chronic medication resource in `data` and `message`.

---

### Show Chronic Medication

**`GET /api/chronic_medications/{id}`**

**Response:** `200 OK`. Single chronic medication resource.

---

### Update Chronic Medication

**`PUT /api/chronic_medications/{id}`** or **`PATCH /api/chronic_medications/{id}`**

**Request body:** Same fields as create, both optional: `patientId` (required when present), `title` (required when present).

**Response:** `200 OK`. Full chronic medication resource.

---

### Delete Chronic Medication

**`DELETE /api/chronic_medications/{id}`**

**Response:** `200 OK`. Deleted resource in `data` and `message`.

---

### Chronic Medication Resource Shape (camelCase)

| Field | Type | Description |
|-------|------|-------------|
| `id` | string (UUID) | Primary key |
| `patientId` | string (UUID) | Patient reference |
| `title` | string | Medication title/description |
| `createdAt` | string | ISO date-time |
| `updatedAt` | string | ISO date-time |

---

## 3. Related Endpoints and UI

- **Patient profile – Chronic Medications tab:** `GET /api/chronic_medications?filter[patientId]={patientId}`. See [Patient Management API contract](patient-management-api-contract.md).
- **New Patient form – Chronic Medications “+ Add”:** After creating the patient, call `POST /api/chronic_medications` with the new `patientId` for each entry.

---

## 4. Request/Response Example

### List Chronic Medications for a Patient

**Request**

```http
GET /api/chronic_medications?filter[patientId]=9d4e2c1a-1234-5678-abcd-000000000001&sort=-created_at
Authorization: Bearer <token>
```

**Response (200 OK)** — Paginated list; `data` is an array of chronic medication resources.

### Create Chronic Medication

**Request**

```http
POST /api/chronic_medications
Content-Type: application/json
Authorization: Bearer <token>
```

```json
{
  "patientId": "9d4e2c1a-1234-5678-abcd-000000000001",
  "title": "Metformin 500 mg, twice daily."
}
```

**Response (201 Created)** — `data` contains full chronic medication resource; `message` in body.

---

## 5. Error Handling and Validation

| Status | Meaning |
|--------|---------|
| **401** | Unauthenticated |
| **403** | Forbidden |
| **404** | Chronic medication not found |
| **422** | Validation failed (e.g. invalid or missing patientId, title) |

**Validation (summary):** Create: `patientId` required, UUID, must exist in patients; `title` required, max 255. Update: same fields optional but when provided must pass same rules.

---

## 6. Flutter-Oriented Notes

- Use **camelCase** for all JSON keys. IDs are **UUID** strings.
- Pagination: `meta.current_page`, `meta.last_page`, `links.next` / `links.prev`, `perPage`.
- For the patient’s Chronic Medications tab, always filter by `patientId`.

---

## 7. Implementation Status

The Chronic Medications API is **implemented** in the backend.
