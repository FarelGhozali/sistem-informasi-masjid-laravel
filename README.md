# Mosque Information System (Sistem Informasi Masjid)

A modern, decoupled Mosque Information System built with:
- **Backend & Admin Panel:** [Laravel 11+](https://laravel.com) + [Filament v3](https://filamentphp.com) (PHP 8.3+)
- **Frontend:** [Astro](https://astro.build) (Static Site Generation / SSG with Tailwind CSS)
- **Database:** PostgreSQL / MySQL / SQLite

The core features include **Financial Transparency** managed by the mosque treasurer (**Bendahara**) and **Activity Schedules & Agendas** managed by the mosque committee (**Takmir**). Both roles have separate, isolated admin panels with strictly separated access permissions.

---

## Architecture Overview

```
+------------------------------------+             +----------------------------------+
|           Frontend (Astro)         |    JSON     |          Backend (Laravel)       |
|  - Public Static / SSG Website     | <=========> |  - REST API (/api/v1/...)        |
|  - Cash Transparency & Schedules   |  HTTP API   |  - Isolated Filament Panels      |
+------------------------------------+             +----------------------------------+
                                                                   |
                                                     +-------------+-------------+
                                                     |                           |
                                           [Bendahara Panel]              [Takmir Panel]
                                           (/bendahara)                   (/takmir)
                                           - Income/Expense               - Activities
                                           - Financial Reports            - Agendas & Posters
```

---

## Prerequisites

Before starting, ensure you have the following installed on your system:
- **PHP** >= 8.3 with extensions (`pdo`, `pdo_pgsql` or `pdo_mysql`, `mbstring`, `openssl`, `curl`, `intl`)
- **Composer** (PHP Package Manager)
- **Node.js** >= 20.x and **npm**
- **Database Server** (PostgreSQL, MySQL, or SQLite)

---

## Step-by-Step Installation & Setup

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/sistem-informasi-masjid-laravel.git
cd sistem-informasi-masjid-laravel
```

---

### 2. Backend Setup (Laravel & Filament)

#### a. Install PHP Dependencies
```bash
composer install
```

#### b. Setup Environment File
Copy the example environment file:
```bash
cp .env.example .env
```

Generate the Laravel application key:
```bash
php artisan key:generate
```

#### c. Configure Database Connection
Open `.env` and set your database credentials. For example, for PostgreSQL:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=masjid
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password
```
*(If you are using MySQL or SQLite, adjust `DB_CONNECTION` and credentials accordingly).*

#### d. Create Storage Symlink
Required for public access to uploaded transaction receipts and event posters:
```bash
php artisan storage:link
```

#### e. Run Migrations & Database Seeder
Run the database migrations and populate the initial roles, demo accounts, categories, and sample data:
```bash
php artisan migrate --seed
```

---

### 3. Frontend Setup (Astro)

The public visitor frontend is located inside the `Frontend-Astro` directory.

#### a. Navigate & Install Node Dependencies
```bash
cd Frontend-Astro
npm install
```

#### b. Configure Environment
If needed, create/verify `Frontend-Astro/.env`:
```env
PUBLIC_API_BASE_URL=http://localhost:8000/api
```

---

## Running the Application Locally

You will need two terminal windows running concurrently:

### Terminal 1: Laravel Backend & Admin Panels
From the **project root**:
```bash
php artisan serve --port=8000
```
- **Backend API:** `http://localhost:8000/api`
- **Bendahara Panel:** `http://localhost:8000/bendahara`
- **Takmir Panel:** `http://localhost:8000/takmir`
- **Super Admin Panel:** `http://localhost:8000/admin`

### Terminal 2: Astro Frontend
From the **Frontend-Astro** folder:
```bash
cd Frontend-Astro
npm run dev
```
- **Public Website:** `http://localhost:4321`

---

## Default Login Credentials

The seeder creates pre-configured accounts with strict role separation:

| Role | Email | Password | Allowed Panel | Description |
| :--- | :--- | :--- | :--- | :--- |
| **Treasurer (Bendahara)** | `bendahara@masjid.com` | `password123` | `/bendahara` | Manages cash in/out, transaction categories, and financial statistics. |
| **Committee (Takmir)** | `takmir@masjid.com` | `password123` | `/takmir` | Manages mosque schedules, lectures, and event flyers. |
| **Super Admin** | `admin@masjid.com` | `password123` | `/admin` | Full administrative access. |

> **Role Isolation Note:** A user logged in as `bendahara` cannot access `/takmir` (access denied), and a user logged in as `takmir` cannot access `/bendahara`.

---

## Public API Endpoints

The Astro frontend consumes the following public REST API endpoints:

- `GET /api/v1/keuangan/ringkasan` - Financial summary (Total Income, Total Expense, Current Cash Balance).
- `GET /api/v1/keuangan/transaksi` - Paginated cash transactions with category and proof image.
- `GET /api/v1/kegiatan` - List of upcoming and completed mosque events.
- `GET /api/v1/kegiatan/{slug}` - Detail of a specific activity by its slug.

---

## Production Build & Deployment

### Building Astro for Production
Astro generates pure static HTML/CSS/JS files:
```bash
cd Frontend-Astro
npm run build
```
The output files will be generated in `Frontend-Astro/dist/`. These files can be uploaded directly to `public_html` on any shared hosting or CDN (Cloudflare Pages, Vercel, Netlify).

### Optimizing Laravel
On the production server:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache
```

---

## License

This project is open-sourced under the [MIT License](LICENSE).
