# Super Mart

A web-based grocery storefront implemented with **Laravel 12**, developed as a
practical study of **Model–View–Controller (MVC) architecture**. The application
provides sufficient domain complexity — catalogue browsing, a shopping cart,
authentication, and an administrative product import — to exercise each layer of
the pattern in a non-trivial manner.

> This entire project is an attempt to understand MVC Architecture.

---

## 1. Purpose

The project exists to make the constituent parts of MVC observable in practice:
how an incoming request is routed, how the data model is expressed, how
presentation is kept separate from business logic, and how controllers
coordinate between them without assuming responsibility for either.

The supermarket domain was selected deliberately. It is complex enough to
require relational models, stateful cart handling, and an administrative import
workflow, yet constrained enough to remain a pedagogical exercise rather than a
production system.

---

## 2. Request Lifecycle

The following diagram traces a request as it traverses the architecture:

```
HTTP request
   │
   ▼
routes/web.php, routes/auth.php        — routing: URL → controller@method
   │
   ▼
Middleware (EnsureAuthenticated / EnsureGuest)   — gate the request
   │
   ▼
Controller (Http/Controllers/*)        — thin: validate, query models, return a view
   │
   ├──▶ Models (App\Models\Item, Category, User)   — data, relationships, accessors
   │         │
   │         └──▶ Eloquent → database (SQLite by default)
   │
   ▼
View (resources/views/*.blade.php)    — render-only; no business logic
   │
   ▼
HTTP response
```

### 2.1 Models — `app/Models/`

| Model | Responsibility |
|-------|----------------|
| `Category` | Holds `name`, `slug`, `icon`, and `heroimg`; has a one-to-many relationship with `Item`. Exposes the `hero_image_url` and `has_hero_image` accessors so that templates never resolve image paths or render broken elements. |
| `Item` | A product. Belongs to a `Category`. Stores `price`, `sale_price`, `currency`, and the `is_clearance` and `is_featured` flags. |
| `User` | Authentication user with a hashed password cast. |

The `Category` accessors exemplify the principle that presentation logic belongs
to the model rather than the view: the template invokes
`{{ $category->hero_image_url }}` and is absolved of any decision regarding the
image source or fallback.

### 2.2 Controllers — `app/Http/Controllers/`

| Controller | Responsibility |
|------------|----------------|
| `HomeController` | Renders the storefront: all categories, featured items, clearance items, and a hero. |
| `CategoryController` | `display()` lists all categories; `show($slug)` retrieves a single category and its items. |
| `CartController` | Session-backed cart implementing `add`, `remove`, `update`, and `index`. |
| `ImportProductController` | Administrative product upload: Excel/CSV import and image upload. |
| `Auth\LoginController` | Email/password authentication, persisting `user_id` (and name/email) to the session; logout clears them. |
| `Auth\RegisterController` | Validates and creates a user, then establishes the session. |

Controllers are deliberately thin. `CartController` encapsulates all session
access behind `getCart()` and `setCart()`, and computes the subtotal within
`index()` rather than deferring that work to the view.

### 2.3 Views — `resources/views/`

The view layer comprises `welcome` (home), `category`, `allcategories`, `cart`,
`admin`, `account-info`, and `login`, together with the `auth/`, `components/`,
and `partials/` directories. Views render only the variables passed to them and
do not query the database directly.

### 2.4 Middleware — `app/Http/Middleware/`

- `EnsureAuthenticated` — redirects to `/account` when the session lacks a `user_id`.
- `EnsureGuest` — the complement; redirects authenticated users away from the login and registration routes.

Middleware executes before the controller, allowing authentication concerns to
be expressed once rather than repeated across controller methods.

---

## 3. Features

- **Storefront** — category navigation, featured items, and clearance items.
- **Category pages** — slug-routed, with a hero image and a paginated limit of thirty-six items.
- **Shopping cart** — add, remove, and update quantities, backed by the session, with a live subtotal.
  - Add-to-cart is submitted via `POST` (the `cart/add/{item}` route is POST-only); removal uses `DELETE`; quantity updates use `POST`.
- **Authentication** — registration, login, logout, and an account page. The implementation is session-based and written explicitly, storing `user_id` in the session rather than relying on Laravel's built-in `Auth::attempt`, in order to expose the session mechanics directly.
- **Administrative import** — accepts an `.xlsx`, `.xls`, or `.csv` file together with optional product images. Two import paths are provided:
  - `ProductsImport`, an Excel `ToModel` implementation with `WithHeadingRow`, invoked by `store()`.
  - A manual `uploadExcel()` path that reads rows into arrays, resolves or creates categories as needed, normalises prices and boolean values, and wraps the operation in a database transaction.

---

## 4. Database

The default database is SQLite (`database/database.sqlite`). The principal
migrations are as follows:

- `create_users_table` — Laravel's standard users table.
- `create_categories_and_items_tables` — defines `categories` and `items`, with a `category_id` foreign key configured for `cascadeOnDelete`. Items carry `price`, `sale_price`, `currency` (default `NGN`), and the `is_clearance` and `is_featured` flags.
- `create_carts_and_orders_tables` — an `orders` and `order_items` design in which a `status` field (`cart|ordered|cancelled|completed`) permits a single table to represent both an active cart and order history.

It should be noted that the `orders` and `order_items` tables are not currently
wired into the running application; the active cart is session-backed. The
migration serves as scaffolding for persisting carts in a subsequent phase.

---

## 5. Getting Started

```bash
composer install                 # PHP dependencies
cp .env.example .env             # then edit database credentials as required
php artisan key:generate
php artisan migrate              # creates the SQLite database and tables
php artisan serve                # http://127.0.0.1:8000
```

A consolidated setup task is defined in `composer.json`:

```bash
composer setup
```

For development with live reloading (server, queue, logs, and Vite concurrently):

```bash
composer dev
```

To execute the test suite:

```bash
composer test          # equivalent to: php artisan config:clear && php artisan test
```

---

## 6. Technology Stack

- **Laravel 12** (PHP 8.2+), Eloquent ORM, Blade templates
- **PHPUnit** — testing
- **Laravel Telescope** — request and debug inspection (local only)
- **maatwebsite/excel** — spreadsheet import

---

## 7. Project Structure

```
app/
  Http/
    Controllers/        # the Controller layer
      Auth/
    Middleware/         # request gatekeepers
  Imports/             # Excel import classes
  Models/               # the Model layer
database/migrations/    # schema (categories, items, orders)
resources/views/        # the View layer (Blade)
routes/
  web.php               # storefront, cart, and administrative routes
  auth.php              # registration, login, and logout
```

---

## 8. Status and Notes

- Authentication is intentionally session-based and implemented explicitly within `LoginController` and `RegisterController`, rather than derived from Laravel's authentication scaffolding, in order to render the flow transparent.
- The `orders` and `order_items` tables exist in schema but are not exercised by the running cart; persistence of the session cart to these tables is deferred to a later phase.
- The current task list is maintained in `TODO.md`.

---

## 9. License

Released under the MIT license.
