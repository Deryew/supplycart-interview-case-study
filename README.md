<p align="center">
    <img align="center" src="https://supplycart.my/wp-content/uploads/2019/09/sc_logo_tm.png">
</p>

# Supplycart Interview Case Study

This case study is designed for candidates to showcase their skills and coding style focusing on Laravel, Vue and TailwindCSS. You may use more technologies apart from the 3 mentioned.

## Instructions

- Fork this repo to your github account
- Complete the tasks given
- Once completed, create a PR to this repository
- Lastly, add some guidance or instruction on how to run your code
- Failure to meet the basic requirements outlined in this case study may result in your submission being deprioritized during the review process

## Requirements

You must work on this assignment using:
- Vue
- TailwindCSS
- Laravel

If you prefer, you're allowed to use a different tech stack, but your solution must include both a backend and a frontend built using a JavaScript framework (e.g. Vue, React, etc.). Blade-only submissions (without a JS framework) will not be accepted.

## Tasks

1. As guest, I want to be able to register an account
2. As guest, I want to be able to login using registered account
3. As user, I want to see list of products after login
4. As user, I want to be able to add product to cart
5. As user, I want to be able to place order for added products in cart
6. As user, I want to see my order history
7. As user, I want to be able to logout

## Bonus Tasks

1. Verify email after registration
2. User activity log e.g. login, logout, add to cart, place order etc
3. Product attributes and filtering e.g brand, category
4. Different user can see different price for products
5. Add unit tests
6. Deploy app to a server

## Key Evaluation Criteria

**Backend**

While completing the above tasks, we will be particularly looking at how you handle the following aspects:

1. **Data Validation**: Proper validation of user inputs and data integrity checks.

2. **Data Transformation**: Efficient and logical transformation of data between different parts of the application.

3. **Query Efficiency**: Optimization of database queries, including proper use of Laravel's query builder and Eloquent ORM features.

4. **Consistent Naming Convention**: Use of snake_case for database columns, camelCase for PHP and JavaScript variables, and adherence to Laravel and Vue.js naming conventions.

5. **Proper Handling of Monetary Values**: Accurate representation and calculation of prices and totals.

6. **Database Design**: Well-structured migration files that demonstrate thoughtful schema design, including appropriate indexes.

7. **Code Organization**: Clear, modular, and maintainable code structure.

8. **Security Best Practices**: Implementation of necessary security measures to protect against common vulnerabilities.

9. **API Design** (if applicable): RESTful design principles and clear documentation.

10. **Error Handling**: Graceful error handling and informative error messages.

**Frontend**

1. **Use of JavaScript Framework**: A modern JS framework (e.g., Vue, React) is required. Blade-only implementations will not be accepted.

2. **Component Structure**: UI broken into reusable, manageable components.

3. **User Experience**: Clear navigation, responsiveness, and usability.

4. **Styling**: Use of TailwindCSS or another CSS framework to ensure consistent design.

5. **Frontend Error Handling**: Proper user feedback for failed actions or edge cases.

## Submission Guidelines

- Ensure your code is well-commented and follows PSR-12 coding standards for PHP.
- Include a README.md file with setup instructions and any assumptions made.
- If you have suggestions for improving this case study, feel free to include them in your submission.

We look forward to reviewing your implementation and discussing your approach during the interview process.

---

P/S: If you think there is a better way for us to assess your technical skills, feel free to suggest. We are constantly looking to improve our interview process.

---

## Usage

### Login Credentials

| Email | Password | Description |
|---|---|---|
| `admin@supplycart.my` | `password` | Admin user |
| `vip@supplycart.my` | `password` | VIP customer — sees discounted prices on selected products (15% off) |
| `user@supplycart.my` | `password` | Regular user — sees standard prices |

You can also register a new account via the registration page.

### Core Flow

1. **Login** — Use any of the credentials above, or register a new account
2. **Browse Products** — Filter by brand or category using the sidebar
3. **Add to Cart** — Click the "Add to Cart" button on any product, adjust quantity as needed
4. **Place Order** — Go to the cart page, review items, and click "Place Order"
5. **Pay** — You'll be redirected to Stripe Checkout (use test card `4242 4242 4242 4242`, any future expiry, any CVC)
6. **Order History** — View past orders and their payment status from the orders page

### User-Specific Pricing

The VIP user sees lower prices on 8 randomly selected products (15% discount). Compare by logging in as `vip@supplycart.my` vs `user@supplycart.my` to see the difference.

## Documentation

- [SETUP.md](SETUP.md) — Installation and setup instructions (local or Docker)
- [API.md](API.md) — Route documentation, request/response shapes, and validation rules

## Architecture

- **Service Layer** — Business logic in `app/Services/` (OrderService, CartService, ProductService, ActivityLogService)
- **Event-Driven Logging** — Activity log via Laravel events/listeners (login, logout, add to cart, place order)
- **Inertia.js** — Server-side routing with Vue 3 frontend, no separate API
- **Monetary Values** — Stored as integers (cents) to avoid floating point issues
- **User-Specific Pricing** — Per-user product prices via `user_prices` table with COALESCE fallback
- **Stripe Checkout** — Hosted payment page with webhook for payment confirmation
- **Pessimistic Locking** — Prevents duplicate order creation from race conditions

## Assumptions

- Products do not have images (placeholder icons used)
- Single currency (MYR) throughout
- Order fulfillment status is tracked in the database but hidden from UI — designed for future logistics integration
- SQLite is used for simplicity; migrations are database-agnostic and work with MySQL/PostgreSQL
