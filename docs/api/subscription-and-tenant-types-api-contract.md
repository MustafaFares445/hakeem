# Subscription and Tenant Types API Contract (Flutter)

This document is the API contract for **Tenant Types**, **Subscription Plans**, **Subscription Orders**, and **Subscription Status** in Hakeem. It is intended for Flutter developers integrating against the Hakeem backend. The API is **implemented** in the backend.

---

## 1. Overview and Authentication

### Base URL

All routes are prefixed with `/api`. Base URL: `https://hakeem.mustafafares.com/api`.

### Authentication

- **Tenant types** and **subscription plans:** No authentication required. Optional: when the user is authenticated, subscription plans may be filtered by the current tenant's type.
- **Subscription orders** and **subscription status:** Require **Laravel Sanctum** and tenant context:
  - **Header:** `Authorization: Bearer <token>`
  - Obtain token via `POST /api/auth/login` with `username` and `password`.

### Tenancy

Subscription orders and status are scoped to the authenticated user's tenant. Tenant types and plans are global (or filtered by tenant type when authenticated).

### Request and Response Format

- **Content-Type:** `application/json` for request body (use `multipart/form-data` for subscription order store with `transactionImage`).
- **Request body and query parameters:** **camelCase**
- **Response body:** **camelCase**

---

## 2. Tenant Types

### List Tenant Types

**`GET /api/tenant-types`**

Returns all active tenant types (e.g. clinic, practice). Used for onboarding or tenant-type selection.

**Authentication:** Not required.

**Response:** `200 OK`. Collection of tenant type resources in `data` and `message` (not paginated). Each item follows the Tenant Type resource shape below.

**Tenant Type resource shape (camelCase):**

| Field | Type | Description |
|-------|------|-------------|
| `id` | string (UUID) | Primary key |
| `key` | string | Unique key (e.g. `clinic`) |
| `name` | string | Display name |
| `description` | string \| null | Description |
| `isActive` | boolean | Whether the type is active |
| `createdAt` | string \| null | ISO date-time |
| `updatedAt` | string \| null | ISO date-time |

---

## 3. Subscription Plans

### List Subscription Plans

**`GET /api/subscription-plans`**

Returns available subscription plans. When the user is authenticated and has a tenant, plans are filtered by the tenant's `tenantTypeId`; otherwise all active plans are returned.

**Authentication:** Not required (optional; when provided, response may be scoped to tenant type).

**Response:** `200 OK`. Collection of subscription plan resources in `data` and `message` (not paginated).

**Subscription Plan resource shape (camelCase):**

| Field | Type | Description |
|-------|------|-------------|
| `id` | string (UUID) | Primary key |
| `tenantTypeId` | string (UUID) | Tenant type this plan applies to |
| `name` | string | Plan name |
| `description` | string \| null | Description |
| `originalPrice` | number | Original price |
| `price` | number | Price |
| `currencyCode` | string | Currency code (e.g. `USD`) |
| `durationValue` | integer | Duration value (e.g. 1, 12) |
| `durationUnit` | string | e.g. `month`, `year` |
| `isLifetime` | boolean | Whether the plan is lifetime |
| `isActive` | boolean | Whether the plan is active |
| `sortOrder` | integer | Display order |
| `createdAt` | string \| null | ISO date-time |
| `updatedAt` | string \| null | ISO date-time |

---

## 4. Subscription Orders

### List Subscription Orders

**`GET /api/subscription-orders`**

**Authentication:** Required (Bearer + tenant context).

**Query parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `perPage` | integer | Page size. Default: 20 |
| `filter[status]` | string | Exact match on order status (e.g. `pending`, `confirmed`, `cancelled`) |
| `sort` | string | `createdAt`, `-createdAt`, `status`, `-status`. Default: `-created_at` |

**Response:** Paginated collection with `data`, `links`, `meta`. Each item follows the Subscription Order resource shape below.

---

### Create Subscription Order

**`POST /api/subscription-orders`**

**Authentication:** Required (Bearer + tenant context).

Creates a new subscription order (e.g. renewal). Requires a transaction proof image. Business rules apply: no pending order, no active lifetime subscription, and plan must have valid duration.

**Request:** `Content-Type: multipart/form-data`

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `subscriptionPlanId` | UUID | Yes | Must exist in `subscription_plans`, active, and match tenant type; must pass backend rules (no pending order, no lifetime if already active, etc.) |
| `transactionImage` | file | Yes | Image (jpg, jpeg, png, webp), max 5120 KB (5 MB) |

**Response:** `201 Created`. Body includes `data` (full subscription order resource) and `message`.

**Errors:**

- **422 Unprocessable Entity** — Validation failed (e.g. plan not allowed, already has pending order, or active lifetime).

---

### Cancel Subscription Order

**`PATCH /api/subscription-orders/{subscriptionOrder}/cancel`**

**Authentication:** Required (Bearer + tenant context).

Cancels a **pending** subscription order. The order must belong to the current tenant.

**Request body:** None (order ID is in the URL).

**Response:** `200 OK`. Full subscription order resource (with updated status and `cancelledAt`) and `message`.

**Errors:**

- **404 Not Found** — Order not found or not belonging to tenant.
- **422 Unprocessable Entity** — Order is not in pending status.

---

### Subscription Order resource shape (camelCase)

| Field | Type | Description |
|-------|------|-------------|
| `id` | string (UUID) | Primary key |
| `tenantId` | string (UUID) | Tenant reference |
| `subscriptionPlanId` | string (UUID) | Plan reference |
| `createdByUserId` | string (UUID) \| null | User who created the order |
| `status` | string | e.g. `pending`, `confirmed`, `cancelled` |
| `planName` | string \| null | Snapshot of plan name |
| `planDescription` | string \| null | Snapshot of plan description |
| `originalPrice` | number | Snapshot original price |
| `price` | number | Snapshot price |
| `currencyCode` | string \| null | Currency code |
| `durationValue` | integer \| null | Duration value |
| `durationUnit` | string \| null | Duration unit (e.g. `month`, `year`) |
| `isLifetime` | boolean | Whether plan is lifetime |
| `startsAt` | string \| null | ISO date-time when subscription starts |
| `endsAt` | string \| null | ISO date-time when subscription ends |
| `confirmedAt` | string \| null | ISO date-time when confirmed |
| `cancelledAt` | string \| null | ISO date-time when cancelled |
| `cancellationReason` | string \| null | Reason for cancellation (if cancelled) |
| `transactionProof` | object \| null | Media resource when loaded (transaction image) |
| `createdAt` | string \| null | ISO date-time |
| `updatedAt` | string \| null | ISO date-time |

---

## 5. Subscription Status

### Get Subscription Status

**`GET /api/subscription-status`**

**Authentication:** Required (Bearer + tenant context).

Returns whether the current tenant can use the app (subscription/trial state). Use this (or the `subscriptionStatus` in login response) to gate access.

**Response:** `200 OK`. Single subscription status resource in `data` and `message`.

**Subscription Status resource shape (camelCase):**

| Field | Type | Description |
|-------|------|-------------|
| `canUseApp` | boolean | Whether the tenant is allowed to use the app |
| `reason` | string | One of: `tenant_suspended`, `trial_active`, `subscription_active`, `lifetime_active`, `pending_confirmation`, `renewal_required` |
| `trialEndsAt` | string \| null | ISO date-time when trial ends |
| `activeUntil` | string \| null | ISO date-time when current subscription period ends |
| `hasPendingOrder` | boolean | Whether there is a pending subscription order |

**Reason values (source of truth):**

| API Value | Meaning |
|-----------|---------|
| `tenant_suspended` | Tenant has been suspended by admin |
| `trial_active` | Tenant is on trial |
| `subscription_active` | Tenant has an active paid subscription |
| `lifetime_active` | Tenant has an active lifetime subscription |
| `pending_confirmation` | Order is pending admin confirmation |
| `renewal_required` | Subscription expired; renewal needed |

---

## 6. Error Handling and Validation

| Status | Meaning | Typical cause |
|--------|---------|----------------|
| **401 Unauthorized** | Unauthenticated | Missing or invalid Bearer token (orders, status) |
| **403 Forbidden** | Not allowed | Tenant or permission issue |
| **404 Not Found** | Resource missing | Invalid UUID or order not found / not belonging to tenant |
| **422 Unprocessable Entity** | Validation failed | Invalid plan, already has pending order, or order not pending for cancel |

---

## 7. Implementation Status

Tenant types, subscription plans, subscription orders, and subscription status are **implemented** in the backend. Subscription orders and status live under `auth:sanctum` and `tenant.by.user` middleware. Subscription status is used by the backend to enforce `subscription.access` for clinic, users, patients, bookings, etc.
