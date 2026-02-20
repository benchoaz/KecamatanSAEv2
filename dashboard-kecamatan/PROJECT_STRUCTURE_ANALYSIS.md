# Dashboard Kecamatan - Project Structure Analysis

## Project Overview
**Project Name:** Dashboard Kecamatan  
**Framework:** Laravel (PHP)  
**Admin Panel:** Filament  
**Architecture:** Domain-Driven Design (DDD) with MVC pattern  
**Purpose:** Comprehensive district/sub-district (Kecamatan) management system for Indonesian government administration

---

## Root Directory Structure

```
dashboard-kecamatan/
├── .agent/                    # Agent configuration
├── .composer/                 # Composer cache
├── .config/                   # Configuration files
├── _migration/                # Migration SQL files
│   └── database.sql          # Database schema backup
├── app/                      # Application core
├── bootstrap/                # Bootstrap files
├── config/                   # Configuration files
├── database/                 # Database migrations & seeders
├── public/                   # Public assets
├── resources/                # Views, CSS, JS
├── routes/                   # Route definitions
├── storage/                  # Application storage
├── tests/                    # Test files
├── vendor/                   # Composer dependencies
├── .env.example             # Environment template
├── .gitignore               # Git ignore rules
├── .gitattributes           # Git attributes
├── .gitconfig               # Git configuration
├── .editorconfig            # Editor configuration
├── artisan                  # Laravel CLI
├── composer.json            # PHP dependencies
├── composer.lock            # Dependency lock file
├── docker-compose.yml       # Docker configuration
├── package.json             # Node.js dependencies
├── phpunit.xml              # PHPUnit configuration
└── README.md                # Project documentation
```

---

## Configuration Files

### Root Configuration
- **`.env.example`** - Environment variables template
- **`composer.json`** - PHP dependencies (Laravel, Filament, Spatie, etc.)
- **`package.json`** - Frontend dependencies (Tailwind, Alpine.js, etc.)
- **`docker-compose.yml`** - Docker containerization setup
- **`phpunit.xml`** - Testing configuration

### Config Directory (`config/`)
- `app.php` - Application configuration
- `auth.php` - Authentication settings
- `database.php` - Database connections
- `filesystems.php` - File storage configuration
- `permission.php` - Spatie Permission configuration
- `sanctum.php` - API authentication
- `session.php` - Session management
- `view.php` - View configuration

---

## Application Structure (`app/`)

### 1. Console (`app/Console/`)
```
Console/
├── Kernel.php                    # Console kernel
└── Commands/
    └── CheckDuplicateDesa.php    # Custom command to check duplicate villages
```

### 2. Domains (`app/Domains/`) - Domain-Driven Design
```
Domains/
├── Analisa/                      # Analysis domain
│   └── Services/
├── Ekbang/                       # Economic Development domain
│   ├── Models/
│   └── Services/
├── Kesra/                        # Social Welfare domain
│   └── Models/
├── Pemerintahan/                 # Government domain
│   ├── Actions/
│   ├── Models/
│   └── Policies/
├── Shared/                       # Shared domain
│   ├── Models/
│   └── Traits/
└── Trantibum/                    # Public Order & Security domain
    └── Models/
```

### 3. Exceptions (`app/Exceptions/`)
```
Exceptions/
└── Handler.php                   # Global exception handler
```

### 4. Filament (`app/Filament/`)
```
Filament/
├── Admin/                        # Admin panel resources
│   ├── Resources/
│   │   ├── AnnouncementResource.php
│   │   ├── AuditLogResource.php
│   │   ├── BeritaResource.php
│   │   ├── DesaResource.php
│   │   ├── JobVacancyResource.php
│   │   ├── PelayananFaqResource.php
│   │   ├── UmkmLocalResource.php
│   │   ├── UmkmResource.php
│   │   └── UserResource.php
│   ├── Resources/.../Pages/      # CRUD pages for each resource
│   └── Widgets/
│       └── StatsOverview.php     # Dashboard statistics widget
└── Resources/
    └── WorkDirectoryResource.php # Work directory management
```

### 5. Helpers (`app/Helpers/`)
```
Helpers/
├── profile_helper.php            # Profile-related helper functions
└── SaeHelper.php                 # SAE (Sistem Akuntabilitas) helper
```

### 6. Http (`app/Http/`)

#### Controllers (`app/Http/Controllers/`)
```
Controllers/
├── Controller.php                    # Base controller
├── ApplicationProfileController.php  # App profile management
├── AuthController.php                # Authentication
├── DashboardController.php           # Main dashboard
├── FileController.php                # File handling
├── LandingController.php             # Landing page
├── PublicBeritaController.php        # Public news
├── PublicLokerController.php         # Public job vacancies
├── PublicServiceController.php       # Public services
├── PublicUmkmController.php          # Public UMKM
├── ReceiptController.php             # Receipt generation
├── SitemapController.php             # Sitemap generation
├── SpjTemplateController.php         # SPJ templates
├── UmkmRakyatController.php          # UMKM Rakyat (public)
└── WorkDirectoryController.php       # Work directory

├── Desa/                             # Village-level controllers
│   ├── AdministrasiController.php    # Village administration
│   ├── BltController.php             # BLT (Bantuan Langsung Tunai)
│   ├── DashboardController.php       # Village dashboard
│   ├── EkbangController.php          # Economic development
│   ├── FileController.php            # Village files
│   ├── KesraController.php           # Social welfare
│   ├── MusdesController.php          # Musyawarah Desa
│   ├── PaguAnggaranController.php    # Budget allocation
│   ├── PembangunanController.php     # Development projects
│   ├── PembangunanLogbookController.php # Development logbook
│   ├── PemerintahanController.php    # Government
│   ├── PerencanaanController.php     # Planning
│   ├── ProfileController.php         # Village profile
│   ├── SubmissionController.php      # Submissions
│   ├── TrantibumController.php       # Public order
│   ├── TrantibumKejadianController.php # Incidents
│   └── TrantibumRelawanController.php # Volunteers

├── Kecamatan/                        # District-level controllers
│   ├── AnnouncementController.php    # Announcements
│   ├── BeritaController.php          # News
│   ├── DashboardController.php       # District dashboard
│   ├── EkbangController.php          # Economic development
│   ├── FileController.php            # District files
│   ├── GeospasialWilayahController.php # Geospatial data
│   ├── KesraController.php           # Social welfare
│   ├── LaporanController.php         # Reports
│   ├── LayananPublikController.php   # Public services
│   ├── PelayananController.php       # Services management
│   ├── PembangunanController.php     # Development
│   ├── PemerintahanController.php    # Government
│   ├── ReferenceDataController.php   # Reference data
│   ├── TrantibumController.php       # Public order
│   ├── UserManagementController.php  # User management
│   └── VerifikasiController.php      # Verification

├── Master/                           # Master data controllers
│   └── DesaMasterController.php      # Village master data

└── Pemerintahan/                     # Government controllers
    └── AparaturController.php        # Government officials
```

#### Middleware (`app/Http/Middleware/`)
```
Middleware/
├── Authenticate.php                  # Authentication check
├── CheckMenuToggle.php               # Menu toggle check
├── CheckRole.php                     # Role-based access
├── EncryptCookies.php                # Cookie encryption
├── PreventRequestsDuringMaintenance.php # Maintenance mode
├── RedirectIfAuthenticated.php       # Redirect authenticated users
├── TrimStrings.php                   # Trim input strings
├── TrustHosts.php                    # Trusted hosts
├── TrustProxies.php                  # Trusted proxies
├── ValidateSignature.php             # Signature validation
└── VerifyCsrfToken.php               # CSRF protection
```

#### Kernel (`app/Http/`)
```
Http/
└── Kernel.php                        # HTTP kernel with middleware
```

### 7. Listeners (`app/Listeners/`)
```
Listeners/
└── AuditActivityListener.php         # Audit activity logging
```

### 8. Models (`app/Models/`)
```
Models/
├── Announcement.php                  # Announcement model
├── AparaturDesa.php                  # Village officials
├── AparaturDocument.php              # Official documents
├── AppProfile.php                    # Application profile
├── Aspek.php                         # Aspects (indicators)
├── AuditLog.php                      # Audit logs
├── Berita.php                        # News articles
├── BltDesa.php                       # Village BLT
├── BuktiDukung.php                   # Supporting evidence
├── Desa.php                          # Village
├── DesaPaguAnggaran.php              # Village budget
├── DokumenDesa.php                   # Village documents
├── Indikator.php                     # Indicators
├── InventarisDesa.php                # Village inventory
├── JawabanIndikator.php              # Indicator answers
├── JobVacancy.php                    # Job vacancies
├── LembagaDesa.php                   # Village institutions
├── Loker.php                         # Job listings
├── MasterBidang.php                  # Master sectors
├── MasterDokumen.php                 # Master documents
├── MasterKegiatan.php                # Master activities
├── MasterKomponenBelanja.php         # Master expense components
├── MasterLayanan.php                 # Master services
├── MasterSbu.php                     # Master SBU (Standar Biaya Umum)
├── MasterSsh.php                     # Master SSH (Standar Satuan Harga)
├── MasterSubBidang.php               # Master sub-sectors
├── Menu.php                          # Menu items
├── PelayananFaq.php                  # Service FAQs
├── PembangunanDesa.php               # Village development
├── PembangunanDokumenSpj.php         # SPJ documents
├── PembangunanLogbook.php            # Development logbook
├── PengunjungKecamatan.php           # District visitors
├── PerencanaanDesa.php               # Village planning
├── PersonilDesa.php                  # Village personnel
├── PublicService.php                 # Public services
├── PublicServiceAttachment.php       # Service attachments
├── RiwayatJabatanPersonil.php        # Personnel job history
├── Role.php                          # User roles
├── SubIndikator.php                  # Sub-indicators
├── Submission.php                    # Submissions
├── TrantibumKejadian.php             # Public order incidents
├── TrantibumRelawan.php              # Public order volunteers
├── Umkm.php                          # UMKM (MSME)
├── UmkmAdminLog.php                  # UMKM admin logs
├── UmkmLocal.php                     # Local UMKM
├── UmkmProduct.php                   # UMKM products
├── UmkmVerification.php              # UMKM verification
├── User.php                          # Users
├── UsulanMusrenbang.php              # Musrenbang proposals
├── Verifikasi.php                    # Verification
└── WorkDirectory.php                 # Work directory

├── Desa/                             # Village-specific models
│   ├── DesaSubmission.php
│   ├── DesaSubmissionDetail.php
│   ├── DesaSubmissionFile.php
│   ├── DesaSubmissionLog.php
│   ├── DesaSubmissionNote.php
│   └── DesaSubmissionValue.php

├── Scopes/
│   └── DesaScope.php                 # Village query scopes
```

### 9. Observers (`app/Observers/`)
```
Observers/
├── AspekObserver.php                 # Aspek model observer
├── IndikatorObserver.php             # Indikator model observer
└── MenuObserver.php                  # Menu model observer
```

### 10. Policies (`app/Policies/`)
```
Policies/
└── BeritaPolicy.php                  # Berita authorization policy
```

### 11. Providers (`app/Providers/`)
```
Providers/
├── AppServiceProvider.php            # App service provider
├── AuthServiceProvider.php           # Auth service provider
├── BroadcastServiceProvider.php      # Broadcasting service provider
├── EventServiceProvider.php          # Event service provider
└── Filament/
    └── AdminPanelProvider.php        # Filament admin panel configuration
```

### 12. Repositories (`app/Repositories/`)
```
Repositories/
├── SubmissionRepository.php          # Submission repository
└── Interfaces/
    └── SubmissionRepositoryInterface.php
```

### 13. Services (`app/Services/`)
```
Services/
├── AnnouncementService.php           # Announcement business logic
├── AnomalyDetectionService.php       # Anomaly detection
├── ApplicationProfileService.php     # App profile logic
├── MasterDataService.php            # Master data management
├── SpjRuleEngine.php                 # SPJ rule engine
├── SpjTemplateService.php            # SPJ template logic
├── SubmissionService.php             # Submission business logic
├── TaxAssistant.php                  # Tax assistance
└── Interfaces/                       # Service interfaces
```

### 14. Traits (`app/Traits/`)
```
Traits/
└── Auditable.php                     # Audit trail trait
```

---

## Routes (`routes/`)

```
routes/
├── api.php              # API routes
├── channels.php         # Broadcasting channels
├── console.php          # Console routes
├── debug.php            # Debug routes
├── desa.php             # Village-level routes
├── kecamatan.php        # District-level routes
├── temp_check.php       # Temporary check routes
└── web.php              # Web routes
```

---

## Database (`database/`)

### Migrations (`database/migrations/`)
Key migration files (chronological):
- `create_desa_table.php` - Villages
- `create_roles_table.php` - User roles
- `create_users_table.php` - Users
- `create_menu_table.php` - Menu system
- `create_aspek_table.php` - Aspects
- `create_indikator_table.php` - Indicators
- `create_submission_table.php` - Submissions
- `create_verifikasi_table.php` - Verification
- `create_audit_log_table.php` - Audit logs
- `create_aparatur_desa_table.php` - Village officials
- `create_public_services_table.php` - Public services
- `create_app_profiles_table.php` - App profiles
- `create_pelayanan_faqs_table.php` - Service FAQs
- `create_announcements_table.php` - Announcements
- `create_pembangunan_desa_tables.php` - Development projects
- `create_trantibum_kejadians_table.php` - Public order incidents
- `create_trantibum_relawans_table.php` - Public order volunteers
- `create_umkm_locals_table.php` - Local UMKM
- `create_job_vacancies_table.php` - Job vacancies
- `create_umkm_rakyat_tables.php` - UMKM Rakyat
- `create_lokers_table.php` - Job listings
- `create_work_directory_table.php` - Work directory

### Seeders (`database/seeders/`)
```
seeders/
├── AdminUserSeeder.php
├── AnnouncementSeeder.php
├── AppProfileSeeder.php
├── AspekSeeder.php
├── BeritaSeeder.php
├── DatabaseSeeder.php
├── DesaSeeder.php
├── IndikatorSeeder.php
├── MasterDokumenSeeder.php
├── MasterKegiatanSeeder.php
├── MasterKomponenBelanjaSeeder.php
├── MasterLayananSeeder.php
├── MasterSbuSeeder.php
├── MasterSshSeeder.php
├── MenuSeeder.php
├── MusdesTestSeeder.php
├── PelayananFaqSeeder.php
├── RolesAndPermissionsSeeder.php
├── RoleSeeder.php
├── UserSeeder.php
├── VillageSeeder.php
└── WorkDirectorySeeder.php
```

---

## Resources (`resources/`)

### Views (`resources/views/`)

#### Layouts (`resources/views/layouts/`)
```
layouts/
├── app.blade.php              # Main app layout
├── desa.blade.php             # Village layout
├── kecamatan.blade.php        # District layout
├── modern.blade.php           # Modern layout
├── public.blade.php           # Public layout
├── umkm.blade.php             # UMKM layout
└── partials/
    ├── header.blade.php
    ├── sidebar.blade.php
    ├── header/desa.blade.php
    ├── public/
    │   ├── announcements.blade.php
    │   ├── bottom-bar.blade.php
    │   ├── footer.blade.php
    │   └── navbar.blade.php
    └── sidebar/
        ├── desa.blade.php
        └── kecamatan.blade.php
```

#### Public Views (`resources/views/public/`)
```
public/
├── berita/
│   ├── index.blade.php        # News listing
│   └── show.blade.php         # News detail
├── loker/
│   ├── create.blade.php       # Job vacancy creation
│   └── index.blade.php        # Job listings
├── umkm/
│   ├── index.blade.php        # UMKM listing
│   └── show.blade.php         # UMKM detail
├── umkm_rakyat/
│   ├── all_products.blade.php
│   ├── create.blade.php
│   ├── dashboard.blade.php
│   ├── index.blade.php
│   ├── login.blade.php
│   ├── manage.blade.php
│   ├── manage_products.blade.php
│   ├── nearby.blade.php
│   ├── settings.blade.php
│   ├── show.blade.php
│   └── verify.blade.php
└── tracking.blade.php         # Service tracking
```

#### District Views (`resources/views/kecamatan/`)
```
kecamatan/
├── announcements/
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── index.blade.php
├── berita/
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── index.blade.php
├── dashboard/
│   └── index.blade.php
├── ekbang/
│   ├── index.blade.php
│   ├── audit/
│   │   └── index.blade.php
│   ├── dana-desa/
│   │   └── index.blade.php
│   ├── fisik/
│   │   └── index.blade.php
│   ├── kecamatan/
│   │   └── index.blade.php
│   ├── kepatuhan/
│   │   └── index.blade.php
│   └── realisasi/
│       └── index.blade.php
├── kesra/
│   ├── dashboard.blade.php
│   ├── monitoring/
│   │   └── index.blade.php
│   └── rekomendasi/
│       └── index.blade.php
├── laporan/
│   ├── ekbang.blade.php
│   ├── index.blade.php
│   ├── kesra.blade.php
│   ├── pemerintahan.blade.php
│   └── trantibum.blade.php
├── layanan/
│   ├── loker/
│   │   ├── form.blade.php
│   │   └── index.blade.php
│   └── umkm/
│       ├── edit_admin.blade.php
│       ├── form.blade.php
│       ├── form_bantuan.blade.php
│       ├── handover.blade.php
│       └── index.blade.php
├── master/
│   └── desa/
│       └── index.blade.php
├── pelayanan/
│   ├── inbox.blade.php
│   ├── show.blade.php
│   ├── statistics.blade.php
│   ├── faq/
│   │   └── index.blade.php
│   ├── layanan/
│   │   ├── form.blade.php
│   │   └── index.blade.php
│   └── visitor/
│       └── index.blade.php
├── pembangunan/
│   ├── index.blade.php
│   ├── show.blade.php
│   └── blt/
│       └── index.blade.php
├── pemerintahan/
│   ├── index.blade.php
│   ├── aparatur/
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── components/
│   │   └── village_grid.blade.php
│   ├── dokumen/
│   │   └── index.blade.php
│   ├── inventaris/
│   │   └── index.blade.php
│   ├── laporan/
│   │   └── index.blade.php
│   ├── lembaga/
│   │   └── index.blade.php
│   ├── perencanaan/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── personil/
│   │   └── index.blade.php
│   └── visitor/
│       └── index.blade.php
├── referensi/
│   ├── sbu.blade.php
│   └── ssh.blade.php
├── settings/
│   ├── features.blade.php
│   ├── geospasial.blade.php
│   └── profile.blade.php
├── trantibum/
│   ├── index.blade.php
│   ├── show.blade.php
│   ├── kejadian.blade.php
│   ├── relawan.blade.php
│   └── kecamatan/
│       ├── darurat.blade.php
│       ├── index.blade.php
│       └── tagana.blade.php
├── users/
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── index.blade.php
├── verifikasi/
│   ├── index.blade.php
│   └── show.blade.php
```

#### Village Views (`resources/views/desa/`)
```
desa/
├── administrasi/
│   ├── index.blade.php
│   ├── dokumen/
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── index.blade.php
│   ├── lembaga/
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── index.blade.php
│   └── personil/
│       ├── create.blade.php
│       ├── edit.blade.php
│       └── index.blade.php
├── blt/
│   ├── create.blade.php
│   ├── index.blade.php
│   └── show.blade.php
├── dashboard/
│   └── index.blade.php
├── kesra/
│   └── index.blade.php
├── musdes/
│   ├── create.blade.php
│   ├── edit.blade.php
│   ├── index.blade.php
│   └── show.blade.php
├── pagu/
│   └── index.blade.php
├── pembangunan/
│   ├── edit.blade.php
│   ├── index.blade.php
│   ├── modern_create.blade.php
│   ├── modern_index.blade.php
│   ├── show.blade.php
│   ├── administrasi/
│   │   └── index.blade.php
│   ├── fisik/
│   │   ├── create.blade.php
│   │   └── index.blade.php
│   └── non-fisik/
│       ├── create.blade.php
│       └── index.blade.php
├── perencanaan/
│   ├── create.blade.php
│   ├── index.blade.php
│   └── show.blade.php
├── profile/
│   └── index.blade.php
└── trantibum/
    ├── index.blade.php
    ├── kejadian/
    │   ├── create.blade.php
    │   ├── index.blade.php
    │   └── show.blade.php
    └── relawan/
        ├── create.blade.php
        └── index.blade.php
```

#### Other Views
```
views/
├── landing.blade.php              # Landing page
├── layanan.blade.php              # Services page
├── auth/
│   └── login.blade.php            # Login page
├── components/
│   └── desa/
│       └── form/
│           ├── input.blade.php
│           ├── status-badge.blade.php
│           └── upload.blade.php
├── dashboard/
│   ├── desa.blade.php
│   └── index.blade.php
├── kerja/
│   ├── index.blade.php
│   └── show.blade.php
├── receipts/
│   └── service-receipt.blade.php
└── submissions/
    ├── create.blade.php
    └── index.blade.php
```

### CSS (`resources/css/`)
```
css/
├── app.css                        # Main stylesheet
├── accessibility.css              # Accessibility features
├── buttons-fix.css                # Button fixes
├── dashboard-premium.css          # Premium dashboard styles
├── dashboard.css                  # Dashboard styles
├── filament-custom.css            # Filament customization
├── font-fix.css                   # Font fixes
├── layout-fix.css                 # Layout fixes
├── menu-pages.css                 # Menu page styles
├── premium-forms.css              # Premium form styles
├── public-berita.css              # Public news styles
├── components/
│   └── profile.css                # Profile component styles
├── filament/
│   ├── filament/
│   │   └── app.css
│   ├── forms/
│   │   └── forms.css
│   └── support/
│       └── support.css
└── min/                           # Minified CSS files
```

### JS (`resources/js/`)
```
js/
├── app.js                         # Main JavaScript
├── bootstrap.js                   # Bootstrap initialization
├── accessibility.js               # Accessibility features
├── dashboard.js                   # Dashboard functionality
├── components/
│   └── profile.js                 # Profile component
├── filament/
│   ├── filament/
│   │   ├── app.js
│   │   └── echo.js
│   ├── forms/
│   │   └── components/
│   │       ├── color-picker.js
│   │       ├── date-time-picker.js
│   │       ├── file-upload.js
│   │       ├── key-value.js
│   │       ├── markdown-editor.js
│   │       ├── rich-editor.js
│   │       ├── select.js
│   │       ├── tags-input.js
│   │       └── textarea.js
│   ├── notifications/
│   │   └── notifications.js
│   ├── support/
│   │   └── support.js
│   ├── tables/
│   │   └── components/
│   │       └── table.js
│   └── widgets/
│       └── components/
│           ├── chart.js
│           └── stats-overview/
│               └── stat/
│                   └── chart.js
└── min/                           # Minified JS files
```

---

## Public Assets (`public/`)

```
public/
├── .htaccess                      # Apache configuration
├── favicon.ico                    # Favicon
├── index.php                      # Entry point
├── robots.txt                     # Robots configuration
├── css/                           # Compiled CSS
│   ├── accessibility.css
│   ├── buttons-fix.css
│   ├── dashboard-premium.css
│   ├── dashboard.css
│   ├── filament-custom.css
│   ├── font-fix.css
│   ├── layout-fix.css
│   ├── menu-pages.css
│   ├── premium-forms.css
│   ├── public-berita.css
│   ├── components/
│   ├── filament/
│   └── min/
├── data/                          # Data files
│   ├── kecamatan_besuk.geojson
│   └── geo/
│       ├── layer_desa.geojson
│       ├── layer_kecamatan.geojson
│       └── layer_poi.geojson
├── img/                           # Images
│   ├── listening-ear.png
│   └── voice-guide-icon.png
├── js/                            # Compiled JavaScript
│   ├── accessibility.js
│   ├── dashboard.js
│   ├── components/
│   ├── filament/
│   └── min/
├── media/                         # Media files
│   ├── camat-landing.png
│   └── login_side_image.png
└── voice-guide/                   # Voice guide system
    ├── voice.actions.js
    ├── voice.bundle.js
    ├── voice.config.js
    ├── voice.init.js
    ├── voice.intent.rules.js
    ├── voice.lexicon.js
    ├── voice.normalizer.js
    ├── voice.parser.js
    ├── voice.recognition.js
    ├── voice.speech.js
    ├── voice.state.js
    └── min/
        └── voice.bundle.min.js
```

---

## Storage (`storage/`)

```
storage/
├── app/                           # Application files
│   ├── public/
│   │   ├── app/
│   │   ├── backgrounds/
│   │   ├── logos/
│   │   └── media/
│   └── sk_personil/
├── framework/                     # Framework files
│   ├── cache/
│   ├── sessions/
│   ├── testing/
│   └── views/
└── logs/                          # Application logs
```

---

## Tests (`tests/`)

```
tests/
├── CreatesApplication.php         # Test bootstrap
├── TestCase.php                   # Base test case
├── Feature/
│   └── ExampleTest.php            # Feature tests
└── Unit/
    └── ExampleTest.php            # Unit tests
```

---

## Key Features & Modules

### 1. **User Management**
- Multi-role authentication (Admin, Kecamatan, Desa, Public)
- Role-based access control (Spatie Permission)
- User profiles with photos and contact info

### 2. **Village (Desa) Management**
- Village profiles and administration
- Personnel and officials management
- Document management
- Budget allocation (Pagu Anggaran)
- Development projects (Pembangunan)
- Social welfare (Kesra)
- Public order (Trantibum)

### 3. **District (Kecamatan) Management**
- Dashboard with statistics
- Economic development monitoring (Ekbang)
- Social welfare monitoring
- Development project oversight
- Public order management
- User management
- Verification and reporting

### 4. **Public Services**
- Service request system
- FAQ management
- Service tracking
- Receipt generation
- Visitor statistics

### 5. **News & Announcements**
- News articles (Berita)
- Announcements
- Public-facing news portal

### 6. **UMKM (MSME) Management**
- UMKM registration
- Product management
- UMKM Rakyat (public self-service)
- Verification system
- Marketplace integration

### 7. **Job Vacancies (Loker)**
- Job posting system
- Public job listings
- Application management

### 8. **Work Directory**
- Work directory management
- File organization

### 9. **Audit & Logging**
- Comprehensive audit trail
- Activity logging
- Anomaly detection

### 10. **Geospatial Features**
- GeoJSON data layers
- Village boundaries
- District boundaries
- Points of interest

### 11. **Accessibility**
- Voice guide system
- Accessibility features
- Screen reader support

### 12. **Filament Admin Panel**
- Resource management
- CRUD operations
- Dashboard widgets
- Statistics overview

---

## Technology Stack

### Backend
- **Framework:** Laravel 11
- **Admin Panel:** Filament PHP
- **Authentication:** Laravel Sanctum
- **Permissions:** Spatie Laravel Permission
- **Database:** MySQL (likely)
- **ORM:** Eloquent

### Frontend
- **Template Engine:** Blade
- **CSS Framework:** Tailwind CSS
- **JavaScript:** Alpine.js
- **Charts:** Chart.js (likely)

### Development Tools
- **PHP:** Composer
- **Node.js:** npm
- **Containerization:** Docker
- **Testing:** PHPUnit

---

## Architecture Patterns

### 1. **Domain-Driven Design (DDD)**
- Organized by business domains (Ekbang, Kesra, Pemerintahan, Trantibum)
- Domain-specific models and services
- Shared domain for common functionality

### 2. **Repository Pattern**
- Repository interfaces and implementations
- Separation of data access logic

### 3. **Service Layer**
- Business logic in service classes
- Reusable service components

### 4. **Observer Pattern**
- Model observers for event handling
- Automatic audit logging

### 5. **Policy Pattern**
- Authorization policies
- Role-based access control

### 6. **Middleware Pattern**
- Request/response processing
- Authentication and authorization

---

## Key Design Decisions

1. **Multi-level Administration:** Separate controllers for Desa (village) and Kecamatan (district) levels
2. **Modular Structure:** Domain-based organization for better maintainability
3. **Comprehensive Audit Trail:** Audit logs for all critical operations
4. **Public-facing Portal:** Separate public views for citizens
5. **Voice Accessibility:** Built-in voice guide for accessibility
6. **Geospatial Integration:** GeoJSON support for mapping
7. **Self-service UMKM:** UMKM Rakyat allows business owners to manage their own profiles

---

## File Naming Conventions

- **Controllers:** `{Module}Controller.php` (e.g., `DashboardController.php`)
- **Models:** Singular nouns (e.g., `User.php`, `Desa.php`)
- **Migrations:** Timestamped descriptive names (e.g., `2026_01_23_152230_create_desa_table.php`)
- **Views:** Kebab-case with `.blade.php` extension
- **Services:** `{Module}Service.php`
- **Repositories:** `{Module}Repository.php`

---

## Security Features

1. **CSRF Protection:** VerifyCsrfToken middleware
2. **Authentication:** Multiple auth guards (web, sanctum)
3. **Authorization:** Role-based access control
4. **Input Validation:** Form request validation
5. **SQL Injection Protection:** Eloquent ORM with parameter binding
6. **XSS Protection:** Blade auto-escaping

---

## Performance Considerations

1. **Database Indexing:** Strategic indexes on frequently queried columns
2. **Caching:** Laravel cache system
3. **Asset Optimization:** Minified CSS and JS files
4. **Lazy Loading:** Eloquent relationships
5. **Query Optimization:** Eager loading to prevent N+1 queries

---

## Summary

This is a comprehensive Laravel-based district management system for Indonesian government administration. The project follows modern software architecture patterns including Domain-Driven Design, Repository Pattern, and Service Layer. It provides multi-level administration (village and district), public-facing services, UMKM management, job vacancies, and extensive audit logging. The system includes accessibility features with voice guidance and geospatial capabilities for mapping.

---

*Analysis completed on: 2026-02-10*
*Project Location: d:/Projectku/dashboard-kecamatan*
