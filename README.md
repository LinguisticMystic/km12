# KM12

Laravel app with tools intended for members of the **KM12** community. Local development uses Docker: **PHP-FPM**, **nginx**, **MySQL 8**, and a **scheduler** container.

## Docker (local)

1. Copy `.env.example` to `.env` if needed. For local Docker set at least:
   - `APP_KEY` — generate once and paste into `.env` (see below); do not run `key:generate` inside the container (`.env` is mounted read-only)
   - `APP_URL` — must match the URL you open (e.g. `http://localhost:8081` if you changed `HTTP_PORT`)
   - `DB_CONNECTION=mysql`, `DB_DATABASE`, `DB_USERNAME` (not `root`), `DB_PASSWORD`
   - Optional: `HTTP_PORT` (default **8080** so port 80 stays free for other stacks)

   Generate a key and copy the full line into `.env`:

   ```bash
   docker compose run --rm app php artisan key:generate --show
   ```

   Paste as `APP_KEY=base64:...` in `.env` on your machine, then save the file.

   Compose overrides `DB_HOST` to `mysql` inside app containers.

2. Start:

   ```bash
   docker compose up -d --build
   ```

3. One-time setup:

   ```bash
   docker compose exec app php artisan migrate
   ```

4. Open **`http://localhost:<HTTP_PORT>`** (default 8080).

Useful: `docker compose logs -f`, `docker compose down` (add `-v` to drop MySQL data).

**502 Bad Gateway** after recreating `app`: restart `web` (`docker compose restart web`) or rebuild the `web` image (nginx now re-resolves `app` via Docker DNS). If you see **500** instead, check `docker compose logs app` and run `migrate` if the database is empty.

After JS/CSS changes: `docker compose up -d --build`. For Vite hot reload, run `npm run dev` on the host instead.
