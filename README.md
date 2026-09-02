# AdEMNEA

Adaptive Environmental Monitoring Networks for East Africa (AdEMNEA) is a combined research
and capacity development project funded by the Norwegian Agency for Development Cooperation
(Norad) under the Norwegian Programme for Capacity Development in Higher Education and
Research for Development (NORHED II). It is a cooperation between the Norwegian University of
Science and Technology, NTNU (leading institution), Makerere University in Uganda (leading
southern institution), the University of Juba in South Sudan, the Dar es Salaam Institute of
Technology (DIT) in Tanzania, and the University of Bergen, Norway.

The project designs, develops, and deploys a flexible network of data-gathering and monitoring
stations for meteorological data as well as audio, image, video, field-report, and telemetry data,
integrating both existing sensing platforms and custom components for specific research areas,
targeting the thematic sub-area of Climate Change and Natural Resources.

Data points are aggregated through resilient, energy-efficient ICT networks from the field to
researchers, who apply machine learning, pattern recognition, and other analytical methods.
Building on infrastructure from the NORHED WIMEA-ICT project, the initial application domain is
entomology: weather monitoring alongside direct analysis of the presence, prevalence, and
behavior of pollinators and pests, to understand the effects of climate change on natural resources
and agriculture. The project goal is improved insect pest (mango fruit fly) control and insect
pollinator (bee) biodiversity conservation through automated, continuous, systematic species
monitoring across wild and agricultural landscapes, contributing to increased agricultural yields in
the partner countries.

This repository is the AdEMNEA web platform: a public research site (publications, scholarships,
newsletters, team, gallery, events, work packages) plus an admin-only dashboard for managing
beehive field stations and their sensor data (temperature, humidity, CO2, weight, VOC, vibration,
entrance bee counts, and audio/video/photo captures), farms and farmers, and generated analytics
reports.

## Tech stack

- **Backend:** Laravel 8 (PHP ^7.3 | ^8.0), MySQL
- **Frontend:** Blade templates, jQuery + Bootstrap 4, some Vue 2 components, Laravel Mix
- **Data/exports:** yajra/laravel-datatables-oracle, maatwebsite/excel, barryvdh/laravel-dompdf
- **Realtime/telemetry:** Pusher + Laravel Echo, ThingSpeak integration
- **Field ingestion & reporting:** standalone Python scripts (see `MODULES/`)

## Project layout

- `app/Http/Controllers/Admin/` — resourceful CRUD controllers for the admin dashboard
  (`/admin/*`, behind `auth:web`): hives, sensor readings, farms/farmers, blog, publications,
  scholarships, newsletters, team, gallery, work packages, feedback.
- `app/Http/Controllers/` — public-facing site controllers.
- `app/Models/` — Eloquent models for both the content site and the sensor domain (`Hive`,
  `HiveTemperature`, `HiveHumidity`, `HiveCarbondioxide`, `HiveWeight`, `HiveVOC`,
  `Vibration`, `HiveEntranceBeeCount`, `HiveAudio`/`HiveVideo`/`HivePhoto`, `BatteryReading`,
  `BeehiveInspection`, `Farm`, `Farmer`, etc.).
- `routes/web.php` / `routes/api.php` — route definitions.
- `resources/views/` — Blade views, split into `website/` (public) and `admin/`.
- `database/migrations/` — schema history.
- `MODULES/` — Python toolchain that runs independently of Laravel:
  - `register_hivetemp_hivehumidity.py`, `register_hiveaudios.py`, `register_hiveimages.py`,
    `register_hivevideos.py`, `register_hivevibration.py`, `register_media.py` — watchdog-based
    scripts that watch a folder for files dropped by Raspberry Pi field stations and register them
    into the database.
  - `BeeDetection/` — bee-detection computer vision module.
  - `report_scripts/`, `attribute_scripts/` — generate the PDF analytics reports (temperature,
    humidity, CO2, weight, correlation) served from the admin data-reports page.

## Local setup

1. Install PHP dependencies: `composer install`
2. Install JS dependencies: `npm install`
3. Copy the environment file and generate an app key:
   ```
   cp .env.example .env
   php artisan key:generate
   ```
4. Configure your database in `.env` (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`), then run
   migrations:
   ```
   php artisan migrate
   ```
5. Build frontend assets: `npm run dev` (or `npm run watch` while developing)
6. Serve the app: `php artisan serve`

## Deploying the field-ingestion scripts (media from Raspberry Pi)

These scripts run on the server independently of the Laravel app and watch a folder for media
pushed from Raspberry Pi field stations.

1. Install the Python `watchdog` package:
   ```
   pip install watchdog
   ```
2. In `MODULES/register_media.py`, under `database_connection()`, set your database
   credentials:
   ```python
   mydb = mysql.connector.connect(host="", user="", passwd="", database="")
   ```
3. In `MODULES/register_hiveaudios.py`, set the folders to watch and deliver into:
   ```python
   folder_to_track = r""       # folder that receives hive audio from the field
   folder_destination = r""    # Laravel storage folder linked to hive audios
   ```
4. Repeat step 3 for `MODULES/register_hiveimages.py` and `MODULES/register_hivevideos.py`.
5. Open `MODULES/register_hivetemp_hivehumidity.py` and edit line 56 as directed by the comment
   there.
6. Run `MODULES/register_media.py` — it launches the other watcher scripts in turn.
