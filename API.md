# API Documentation

This application uses [Inertia.js](https://inertiajs.com/) to bridge Laravel and Vue. All routes return Inertia responses (server-rendered Vue pages) except for the Stripe webhook which returns JSON.

## Authentication

| Method | URI | Description | Auth |
|--------|-----|-------------|------|
| GET | `/register` | Registration page | Guest |
| POST | `/register` | Create account | Guest |
| GET | `/login` | Login page | Guest |
| POST | `/login` | Authenticate | Guest |
| POST | `/logout` | Log out | Auth |
| GET | `/verify-email` | Email verification prompt | Auth |
| GET | `/verify-email/{id}/{hash}` | Verify email link | Auth, Signed |
| POST | `/email/verification-notification` | Resend verification | Auth |

### POST /register

| Field | Rules |
|-------|-------|
| name | required, string, max:255 |
| email | required, email, lowercase, max:255, unique |
| password | required, confirmed, min:8 |

### POST /login

| Field | Rules |
|-------|-------|
| email | required, email |
| password | required, string |

Rate limited to 5 attempts per email+IP.

---

## Products

| Method | URI | Description | Auth |
|--------|-----|-------------|------|
| GET | `/products` | Browse products | Public |

### GET /products

**Query parameters:**

| Param | Rules | Description |
|-------|-------|-------------|
| brand_id | nullable, exists:brands | Filter by brand |
| category_id | nullable, exists:categories | Filter by category |
| search | nullable, string, max:100 | Search by product name |

**Response shape (per product):**

```json
{
  "id": 1,
  "name": "Product Name",
  "slug": "product-name",
  "description": "...",
  "price": 10000,
  "effectivePrice": 8500,
  "formattedPrice": "RM 100.00",
  "hasDiscount": true,
  "brand": { "id": 1, "name": "Brand" },
  "category": { "id": 1, "name": "Category" },
  "stock": 50,
  "isActive": true
}
```

Prices are in **cents** (integer). `effectivePrice` reflects user-specific pricing when applicable.

---

## Cart

All cart routes require authentication + verified email.

| Method | URI | Description | Auth |
|--------|-----|-------------|------|
| GET | `/cart` | View cart | Auth |
| POST | `/cart/items` | Add item to cart | Auth |
| PATCH | `/cart/items/{cartItem}` | Update quantity | Auth |
| DELETE | `/cart/items/{cartItem}` | Remove item | Auth |
| DELETE | `/cart` | Clear all items | Auth |

### POST /cart/items

| Field | Rules |
|-------|-------|
| product_id | required, exists:products |
| quantity | required, integer, min:1, max:99 |

Adding the same product again increments the existing quantity. Stock is validated before adding.

### PATCH /cart/items/{cartItem}

| Field | Rules |
|-------|-------|
| quantity | required, integer, min:1, max:99 |

Authorization: users can only modify their own cart items.

**Response shape (cart):**

```json
{
  "id": 1,
  "items": [
    {
      "id": 1,
      "productId": 1,
      "quantity": 2,
      "product": {
        "id": 1,
        "name": "Product Name",
        "price": 10000,
        "effectivePrice": 8500,
        "stock": 50,
        "brand": { "id": 1, "name": "Brand" },
        "category": { "id": 1, "name": "Category" }
      }
    }
  ],
  "itemCount": 2,
  "subtotal": 17000,
  "formattedSubtotal": "RM 170.00"
}
```

---

## Orders

All order routes require authentication + verified email.

| Method | URI | Description | Auth |
|--------|-----|-------------|------|
| GET | `/orders` | Order history (paginated, 15/page) | Auth |
| POST | `/orders` | Place order (redirects to Stripe) | Auth |
| GET | `/orders/{order}` | Order detail | Auth |
| POST | `/orders/{order}/pay` | Retry payment for unpaid order | Auth |
| GET | `/checkout/{order}/success` | Stripe success callback | Auth |
| GET | `/checkout/{order}/cancel` | Stripe cancel callback | Auth |

### POST /orders

| Field | Rules |
|-------|-------|
| notes | nullable, string, max:500 |

Creates order from active cart, validates stock, snapshots prices at checkout time, deactivates cart, then redirects to Stripe Checkout. Uses pessimistic locking to prevent duplicate orders.

### POST /orders/{order}/pay

No body required. Creates a new Stripe Checkout session for unpaid orders. Returns error if order is already paid.

Authorization: users can only access their own orders.

**Response shape (per order):**

```json
{
  "id": 1,
  "orderNumber": "ORD-20260419-2CAF",
  "totalAmount": 20000,
  "formattedTotal": "RM 200.00",
  "status": "pending",
  "paymentStatus": "paid",
  "paidAt": "2026-04-19T05:31:00.000000Z",
  "notes": "Please deliver fast",
  "items": [
    {
      "id": 1,
      "productId": 1,
      "productName": "Product Name",
      "quantity": 2,
      "unitPrice": 10000,
      "totalPrice": 20000,
      "formattedUnitPrice": "RM 100.00",
      "formattedTotalPrice": "RM 200.00"
    }
  ],
  "createdAt": "2026-04-19T05:30:00.000000Z",
  "formattedDate": "19 Apr 2026, 05:30 AM"
}
```

---

## Stripe Webhook

| Method | URI | Description | Auth |
|--------|-----|-------------|------|
| POST | `/stripe/webhook` | Handle Stripe events | None (CSRF exempt) |

Handles `checkout.session.completed` event. Matches order by `stripe_checkout_session_id` and marks it as paid. Signature verification is enforced when `STRIPE_WEBHOOK_SECRET` is configured.

**Response:**

```json
{ "status": "ok" }
```

---

## Error Responses

Validation errors return a `422` response with errors keyed by field name:

```json
{
  "message": "The product id field is required.",
  "errors": {
    "product_id": ["The product id field is required."]
  }
}
```

Authorization failures return `403 Forbidden`. Unauthenticated requests redirect to `/login`.
