# Setup Instructions

## Option 1: Docker (Recommended)

The only prerequisite is [Docker](https://www.docker.com/products/docker-desktop/).

```bash
# Clone the repository
git clone https://github.com/Deryew/supplycart-interview-case-study.git
cd supplycart-interview-case-study

# Build and start the application
docker compose up --build -d

# Seed the database
docker compose exec app php artisan db:seed --force
```

Visit `http://localhost:8080` to access the application.

### Stripe Payment (Optional)

To enable Stripe Checkout, pass your test keys when starting:

```bash
STRIPE_KEY=pk_test_... STRIPE_SECRET=sk_test_... docker compose up --build -d
```

#### Webhook (Local Testing)

The webhook endpoint at `POST /stripe/webhook` is fully implemented — it verifies the Stripe signature and marks orders as paid on `checkout.session.completed` events.

Since Stripe cannot reach localhost directly, use the [Stripe CLI](https://stripe.com/docs/stripe-cli) to forward webhook events:

```bash
stripe listen --forward-to localhost:8080/stripe/webhook
```

Copy the webhook signing secret from the output and set it:

```bash
STRIPE_WEBHOOK_SECRET=whsec_... docker compose up --build -d
```

### Useful Commands

```bash
# View logs
docker compose logs -f

# Run tests
docker compose exec app php artisan test

# Run artisan commands
docker compose exec app php artisan tinker

# Stop the application
docker compose down

# Stop and remove all data
docker compose down -v
```

---

## Option 2: Local Development

### Prerequisites

- PHP 8.4+
- Composer
- Node.js 18+
- npm

### Installation

```bash
# Clone the repository
git clone https://github.com/Deryew/supplycart-interview-case-study.git
cd supplycart-interview-case-study

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database (SQLite by default — no setup needed)
touch database/database.sqlite
php artisan migrate --seed

# Build frontend assets
npm run build

# Start the application
php artisan serve
```

Visit `http://localhost:8000` to access the application.

For development with hot reload, run in a separate terminal:

```bash
npm run dev
```

### Stripe Payment (Optional)

Add your test keys to `.env`:

```
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
```

Use test card `4242 4242 4242 4242` with any future expiry and CVC.

To receive webhook events locally, run in a separate terminal:

```bash
stripe listen --forward-to localhost:8000/stripe/webhook
```

Then add the signing secret to `.env`:

```
STRIPE_WEBHOOK_SECRET=whsec_...
```

### Running Tests

```bash
php artisan test
```

65 feature tests covering authentication, cart, orders, payments, and products.

---

## Email (Mailpit)

The app uses [Mailpit](https://mailpit.axllent.org/) to capture outgoing emails locally (e.g. email verification, password reset). No emails are actually sent — they are caught and viewable in a web UI.

### Local Development

Install and run Mailpit:

```bash
# macOS
brew install mailpit
mailpit
```

Mailpit will listen on SMTP port `1025` and serve a web UI at `http://localhost:8025`.

The default `.env` is already configured for Mailpit:

```
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
```

### Docker

Mailpit is included in the Docker setup. Access the inbox at `http://localhost:8025` after running `docker compose up`.

---

## Switching to MySQL (Optional)

Update `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=supplycart
DB_USERNAME=root
DB_PASSWORD=
```

Then run `php artisan migrate --seed`.
