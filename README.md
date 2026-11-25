# CargoTrace — Global Shipment Visibility & Tracking Platform

**CargoTrace** is a Laravel-based logistics visibility and shipment tracking platform built as a software engineering portfolio and UAS Web Programming 2 final project. It provides centralized shipment lifecycle management, deterministic ETA/delay calculation, append-only tracking timelines, interactive GIS route mapping, external weather & geocoding integration, role-based access control (RBAC), and automated testing.

---

## 1. Problem Statement & Solution

### Problems Addressed
1. Shipment information is fragmented across multiple carriers and spreadsheets.
2. Customers lack transparent, chronological visibility into their cargo's current location and history.
3. Operational delays and exceptions (customs holds, port congestion, weather) are detected late and handled inconsistently.
4. Logistics operators need a live analytics dashboard to prioritize delayed or problematic shipments.

### CargoTrace Solution
- **Centralized Hub:** Unified management of `shipments`, `shipment_items`, `carriers`, `locations`, and `tracking_events`.
- **Deterministic State Machine:** Enforces sequential status transitions (`CREATED → BOOKED → PICKED_UP → IN_TRANSIT → DELIVERED`) with terminal-state protection and logged Admin Override.
- **Configurable Delay Engine:** Compares `estimated_arrival` against `expected_arrival` using configurable thresholds in `config/cargotrace.php` (`LOW`, `MEDIUM`, `HIGH`).
- **Interactive GIS & External APIs:** Leaflet.js + OpenStreetMap route visualization, Nominatim Geocoding API, and Open-Meteo Maritime Weather API with timeout, retry, caching, and offline fallbacks.

---

## 2. Tech Stack & Layered Architecture

- **Backend:** PHP 8.2+, Laravel 12, Eloquent ORM, Laravel Sanctum (Bearer Token Auth), Laravel Policies & Middleware
- **Database:** SQLite (local/testing) / MySQL 8+ (production) with foreign keys, unique constraints, and indexes
- **Frontend:** Modular Blade Partials (`resources/views/partials/`), Tailwind CSS, Vanilla JavaScript (`public/js/`), Leaflet.js, Chart.js
- **Testing:** PHPUnit Feature & Unit Tests

```text
Browser / API Client
  └── Routes & Middleware (Sanctum Auth + RoleMiddleware)
        └── Form Request Validation
              └── API Controllers + ShipmentPolicy (Gate)
                    └── Service Layer (ShipmentService, TrackingService, EtaService,
                                       ExceptionService, DashboardService, AuditService,
                                       GeocodingService, WeatherService)
                          └── Eloquent Models & Database
```

---

## 3. Role-Based Access Control (RBAC)

| Module / Capability | Administrator | Logistics Operator | Customer |
| :--- | :---: | :---: | :---: |
| User Management (`CRUD`) | Full CRUD | No Access (403) | No Access (403) |
| Master Carriers & Locations | Full CRUD | Read Only | Read Only |
| View Shipments & Dashboard | All Shipments | All Shipments | Own Shipments Only |
| Create Shipments | Yes | Yes | Own Account Only |
| Transition Status & Tracking Events | Yes | Yes | No Access (403) |
| Terminal Status Override | Yes (Logged) | No Access (403) | No Access (403) |
| Manage Exceptions | Yes | Yes | Read Only (Own) |
| Audit Logs | Yes | No Access (403) | No Access (403) |

---

## 4. Database Schema (9 Core Tables)

1. `users` — User accounts and roles (`admin`, `operator`, `customer`)
2. `carriers` — Shipping lines / logistics carriers (`code` unique)
3. `locations` — Ports, hubs, warehouses with `latitude` & `longitude`
4. `shipments` — Core operational shipments, status, ETA, `is_delayed`, `delay_hours`, `delay_severity`, `delay_reason`
5. `shipment_items` — Cargo manifest items (`1-to-Many` from `shipments`)
6. `tracking_events` — Append-only chronological milestones (`occurred/event_time`, coordinates, creator)
7. `shipment_exceptions` — Operational incidents (`OPEN → INVESTIGATING → RESOLVED`)
8. `notifications` — In-app alerts for status updates, delays, and exceptions
9. `audit_logs` — Forensic trail of critical mutations (`old_values`, `new_values`, `ip_address`)

---

## 5. Local Setup & Running the Application

```bash
# 1. Install PHP dependencies
composer install

# 2. Configure environment
cp .env.example .env
php artisan key:generate

# 3. Run migrations & seed realistic logistics demo data
php artisan migrate:fresh --seed

# 4. Run automated test suite
php artisan test

# 5. Start local development server
php artisan serve
```

Open in browser: `http://127.0.0.1:8000/app`

### Demo Accounts (Password: `password`)
| Role | Email | Access Scope |
| :--- | :--- | :--- |
| **Administrator** | `admin@cargotrace.com` | Full system access, master data, user management, audit logs |
| **Logistics Operator** | `operator@cargotrace.com` | Shipment lifecycle, tracking events, exception resolution |
| **Customer** | `customer@cargotrace.com` | Strictly isolated to PT Global Logistik Nusantara shipments |

---

## 6. REST API Endpoints & Postman

A ready-to-import Postman Collection is included at `cargotrace_postman_collection.json`.
- **Auth:** `POST /api/register`, `POST /api/login`, `POST /api/logout`, `GET /api/user`
- **Dashboard:** `GET /api/dashboard`, `/shipments-by-status`, `/shipments-by-carrier`, `/delay-trend`
- **Shipments & Tracking:** `GET/POST /api/shipments`, `GET/PUT/PATCH/DELETE /api/shipments/{id}`, `PATCH /api/shipments/{id}/status`, `POST /api/shipments/{id}/override-status`, `GET /api/shipments/{id}/map`, `GET/POST /api/shipments/{id}/tracking`
- **Exceptions:** `GET /api/exceptions`, `GET/POST /api/shipments/{id}/exceptions`, `PATCH /api/exceptions/{id}`
- **Master Data & External APIs:** `/api/carriers`, `/api/locations`, `/api/geocode`, `/api/reverse-geocode`, `/api/weather`
- **Notifications & Admin:** `GET /api/notifications`, `PATCH /api/notifications/read-all`, `PATCH /api/notifications/{id}/read`, `/api/users`, `GET /api/audit-logs`

---

## 7. Known Limitations & Future Work

- **Known Limitations:**
  - Uses synthetic seeded logistics data rather than live carrier EDI/AIS vessel transponders.
  - Geocoding and weather depend on public OpenStreetMap Nominatim and Open-Meteo endpoints (mitigated with 24h/30m caching and static fallbacks).
  - In-app notifications use database polling rather than WebSocket push.
- **Future Work:**
  - Real-time WebSocket broadcasting (Laravel Reverb) for live vessel coordinates and instant notifications.
  - Email / Webhook notification channels and PDF Bill of Lading export.

---

## 8. Attribution

- **Laravel Framework & Sanctum** — Backend MVC, ORM, authentication, and validation ([laravel.com](https://laravel.com))
- **Tailwind CSS** — Utility-first UI styling ([tailwindcss.com](https://tailwindcss.com))
- **Leaflet.js & OpenStreetMap** — Interactive GIS map & tiles (`© OpenStreetMap contributors`)
- **Chart.js** — Analytics dashboard charts ([chartjs.org](https://www.chartjs.org))
- **Open-Meteo & Nominatim** — Public weather telemetry and geocoding APIs
