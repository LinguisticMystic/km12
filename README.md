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
   docker compose exec app php artisan db:seed
   docker compose exec app php artisan storage:link
   ```

4. Open **`http://localhost:<HTTP_PORT>`** (default 8080).

   - **Members** (admin and non-admin): sign in at **`/login`**, then open **Door opener** or **Wayfinder** from the home page. There is no public registration; admins create users in the panel.
   - **Admin panel**: **`/admin/login`** (seeded admin from `ADMIN_EMAIL` / `ADMIN_PASSWORD` in `.env`). Non-admin accounts cannot access the panel.
   - After `db:seed`, a non-admin test user is available: `test@example.com` / `password`.

   Filament CSS/JS are copied into `public/` during the image build (`php artisan filament:assets`). After changing Filament version, rebuild: `docker compose up -d --build`.

Useful: `docker compose logs -f`, `docker compose down` (add `-v` to drop MySQL data).

**502 Bad Gateway** after recreating `app`: restart `web` (`docker compose restart web`) or rebuild the `web` image (nginx now re-resolves `app` via Docker DNS). If you see **500** instead, check `docker compose logs app` and run `migrate` if the database is empty.

After JS/CSS changes: `docker compose up -d --build`. For Vite hot reload, run `npm run dev` on the host instead.

## Door opener — firmware & API

Firmware lives under `firmware/`:

| Path | Use |
|------|-----|
| `firmware/km12_door_opener/km12_door_opener.ino` | **Flash this** — KM12 website API |
| `firmware/legacy/virinatajs_1.81/virinatajs_1.81.ino` | Original Telegram bot (reference only) |

`km12_door_opener.ino` is your `virinatajs_1.81.ino` with the Telegram bot swapped for the KM12 website API. Everything else (pins, relays, timing, button, `handleUpdates` / `loop` structure) is unchanged.

Open `firmware/km12_door_opener/` in the Arduino IDE (sketch folder name must match the `.ino` file).

### Secrets (`secrets.h`)

Wi‑Fi and API credentials live in **`secrets.h`** (gitignored), not in the `.ino` file. Before the first upload:

```bash
cp firmware/km12_door_opener/secrets.h.example firmware/km12_door_opener/secrets.h
# edit secrets.h — WIFI_SSID, WIFI_PASSWORD, SERVER_URL, API_TOKEN
```

For the legacy sketch, copy `secrets.h.example` in that folder instead (needs `BOT_TOKEN` and `GROUP_CHAT_ID` too).

| Define | Used by |
|--------|---------|
| `WIFI_SSID`, `WIFI_PASSWORD` | Both sketches |
| `SERVER_URL`, `API_TOKEN` | `km12_door_opener.ino` only |
| `BOT_TOKEN`, `GROUP_CHAT_ID` | `virinatajs_1.81.ino` only |

If this repo was ever pushed with real credentials in the `.ino` files, rotate Wi‑Fi password, Telegram bot token, and `DOOR_OPENER_ARDUINO_TOKEN`.

### Sketch changes

`km12_door_opener.ino` is the same as `virinatajs_1.81.ino` except Telegram is replaced by `#include "secrets.h"` and polling the KM12 API. Remove the **UniversalTelegramBot** library — the new sketch only needs the ESP32 core (`WiFi`, `WiFiClientSecure`, `HTTPClient`).

Inside `handleUpdates()`, Telegram messages are replaced by polling the API (still every 2 s). Commands map like this:

| Was (Telegram) | Now (API `command`) |
|----------------|---------------------|
| `/durvis` / 🚪 Durvis | `door` |
| `/varti` / ⛩️ Vārti | `gate` |

Doorbell in `loop()` still fires on a long button press; instead of a Telegram message it `POST`s to `/api/door-opener/doorbell`.

### KM12 API (what the ESP32 calls)

All ESP32 endpoints use the same auth:

```http
Authorization: Bearer <apiToken>
```

(`?token=<apiToken>` also works, but the sketch uses the header.)

**Poll for commands** — `GET {serverUrl}/api/door-opener/poll`

Call about every 2 seconds (same interval as the old `getUpdates` loop). Response JSON:

```json
{ "command": "door", "door_open_seconds": 10 }
```

| Field | Meaning |
|-------|---------|
| `command` | `door`, `gate`, or `null` if nothing queued |
| `door_open_seconds` | Sent every poll; firmware still uses the fixed 10 s door duration like before |

When someone clicks **Open door** or **Open gate** on the website, the server stores a one-shot command. The **first successful poll** returns it and clears it. If nothing polls within `DOOR_OPENER_COMMAND_TTL` seconds (default 60), the command is dropped.

**Doorbell** — `POST {serverUrl}/api/door-opener/doorbell`

Empty body `{}` is fine. Acknowledged with `200`; no relay action (notifications can be added on the server later).

**Website → queue** (not called from the ESP32):

| Method | Path | Auth |
|--------|------|------|
| `POST` | `/door-opener/door` | Logged-in member (session cookie) |
| `POST` | `/door-opener/gate` | Logged-in member (session cookie) |

### Server `.env`

Whoever deploys the site sets (see `.env.example`):

```env
DOOR_OPENER_ARDUINO_TOKEN=<shared secret for the ESP32>
DOOR_OPENER_COMMAND_TTL=60
DOOR_OPENER_DOOR_SECONDS=10
```

Share `DOOR_OPENER_ARDUINO_TOKEN` with whoever flashes the board — it must match `apiToken` in the sketch.

### Quick test

```bash
curl -s -H "Authorization: Bearer YOUR_TOKEN" "https://km12.lv/api/door-opener/poll"
```

Usually `{"command":null,"door_open_seconds":10}`. After **Open door** on the site, the next poll may return `"command":"door"` until the ESP32 consumes it.
