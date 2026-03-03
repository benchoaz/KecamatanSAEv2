# Architecture Diagram - Cloud Shell Deployment

## 🏗️ System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                     Google Cloud Shell                          │
│                   (5GB Persistent Storage)                      │
│                                                                 │
│  ┌────────────────────────────────────────────────────────────┐│
│  │                    Docker Network                          ││
│  │              (dashboard-kecamatan_app-network)             ││
│  │                                                            ││
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐   ││
│  │  │   Nginx      │  │  PHP-FPM     │  │   MySQL      │   ││
│  │  │  :80→8000    │→ │  :9000       │→ │  :3306→3307  │   ││
│  │  │  (Web Server)│  │ (App Engine) │  │ (Database)   │   ││
│  │  └──────────────┘  └──────────────┘  └──────────────┘   ││
│  │         ↓                 ↓                  ↓             ││
│  │  ┌──────────────┐  ┌──────────────┐                      ││
│  │  │    n8n       │  │    WAHA      │                      ││
│  │  │ :5678→5679   │  │ :3000→3000   │                      ││
│  │  │ (Workflows)  │  │(WhatsApp API)│                      ││
│  │  └──────────────┘  └──────────────┘                      ││
│  │                                                            ││
│  └────────────────────────────────────────────────────────────┘│
│                                                                 │
│  Volumes:                                                       │
│  • dbdata → MySQL data persistence                             │
│  • n8n_data_dashboard → n8n workflows & config                │
│  • waha_sessions → WhatsApp sessions                           │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
                              ↓
                   Your Browser (Web Preview)
                              ↓
            ┌──────────────────────────────────┐
            │  http://localhost:8000           │
            │  ├─ Main App Dashboard           │
            │  ├─ http://localhost:5679 (n8n)  │
            │  └─ http://localhost:3000 (WAHA) │
            └──────────────────────────────────┘
```

## 📊 Data Flow

```
User Browser
    ↓
[Web Preview / localhost:8000]
    ↓
    ├──→ Nginx (Port 8000)
    │      ├──→ Laravel PHP-FPM (Port 9000)
    │      │      ├──→ MySQL Database (Port 3306)
    │      │      ├──→ WAHA API (Port 3000)
    │      │      └──→ n8n Workflows (Port 5678)
    │      └──→ Static Files (public/)
    │
    ├──→ n8n Dashboard (Port 5679)
    │      └──→ n8n API (Port 5678)
    │             ├──→ WAHA (WhatsApp)
    │             └──→ Laravel API
    │
    └──→ WAHA Dashboard (Port 3000)
           ├──→ WhatsApp Integration
           └──→ Webhooks → n8n → Laravel
```

## 🔄 Service Dependencies

```
MySQL (db)
    ↑
    └─ Depends on: Nothing (starts first)

Laravel (app)
    ↑
    ├─ Depends on: MySQL (db)
    └─ Health Check: php-fpm -t

Nginx (web server)
    ↑
    ├─ Depends on: Laravel (app)
    └─ Health Check: nginx -t

n8n (Workflows)
    ↑
    ├─ Depends on: Nginx
    └─ Runs workflows

WAHA (WhatsApp)
    ↑
    ├─ Depends on: Laravel (app)
    └─ Health Check: API ping
```

## 📦 Container Lifecycle

```
docker-compose up -d

    ↓
    
Step 1: Pull Images (Docker Hub)
├─ mysql:8.0
├─ nginx:alpine
├─ php:8.1-fpm-alpine (build locally)
├─ n8nio/n8n:latest
└─ devlikeapro/waha:latest

    ↓

Step 2: Create Network
└─ dashboard-kecamatan_app-network

    ↓

Step 3: Create Volumes
├─ dbdata
├─ n8n_data_dashboard
└─ waha_sessions

    ↓

Step 4: Start Containers (in order)
├─ db (MySQL) → waits for healthy
├─ app (PHP) → waits for db healthy
├─ nginx → waits for app healthy
├─ n8n → waits for nginx healthy
└─ waha → waits for app started

    ↓

Step 5: Health Checks
├─ db: mysqladmin ping
├─ app: php-fpm -t
├─ nginx: nginx -t
└─ waha: wget API endpoint

    ↓

✅ All Services Ready!
```

## 🔌 Port Mappings

```
Host Machine          Container          Service
─────────────────────────────────────────────────
localhost:8000    →  nginx:80            Web Server
localhost:5679    →  n8n:5678            Workflow Engine
localhost:3000    →  waha:3000           WhatsApp API
localhost:3307    →  mysql:3306          Database
(none)            →  php:9000            PHP-FPM (internal only)
```

## 💾 Data Persistence

```
Cloud Shell (Persistent Home Directory)
│
├─ Project Source Code
│  ├─ app/
│  ├─ config/
│  ├─ database/
│  ├─ public/
│  └─ .env (secrets & config)
│
├─ Docker Volumes (Persist Data)
│  ├─ dbdata/
│  │  └─ /var/lib/mysql (MySQL data)
│  ├─ n8n_data_dashboard/
│  │  └─ /home/node/.n8n (Workflows & config)
│  └─ waha_sessions/
│     └─ /app/.sessions (WhatsApp sessions)
│
└─ Docker Images (Downloaded, not persistent)
   ├─ mysql:8.0
   ├─ nginx:alpine
   ├─ php:8.1-fpm-alpine
   ├─ n8nio/n8n:latest
   └─ devlikeapro/waha:latest
```

## 🔐 Security & Networking

```
┌─────────────────────────────────────┐
│    Cloud Shell Environment          │
│                                     │
│  Internal Docker Network            │
│  (172.18.0.0/16)                   │
│                                     │
│  ├─ nginx:172.18.0.2               │
│  ├─ app:172.18.0.6                 │
│  ├─ db:172.18.0.4                  │
│  ├─ n8n:172.18.0.3                 │
│  └─ waha:172.18.0.5                │
│                                     │
│  ✅ Services communicate via name  │
│     (e.g., db = 172.18.0.4)        │
│                                     │
└─────────────────────────────────────┘
        ↓
  Exposed Ports (localhost)
  ├─ :8000 → nginx (public)
  ├─ :5679 → n8n (public)
  ├─ :3000 → waha (public)
  └─ :3307 → mysql (public)
```

## 📈 Resource Usage

```
Container          Memory    CPU      Disk Usage
─────────────────────────────────────────────
mysql:8.0          ~200MB    Low      ~500MB (data)
nginx:alpine       ~10MB     Low      ~5MB
php:8.1-fpm        ~80MB     Low      ~150MB
n8nio/n8n          ~300MB    Medium   ~1GB (varies)
waha:latest        ~200MB    Low      ~200MB (data)
───────────────────────────────────────────────
Total              ~800MB    Low      ~2-3GB

Cloud Shell Limits:
├─ Memory: 1GB (should be OK)
├─ CPU: Shared
└─ Storage: 5GB (plenty of room)
```

## 🚀 Deployment Flow

```
User Action: bash cloudshell-quickstart.sh

    ↓

Script Actions:
├─ Clone/Pull repo
├─ Create .env file
├─ docker-compose pull (get images)
├─ docker-compose down -v (clean slate)
├─ docker-compose up -d (start all)
├─ Wait for MySQL ready (30s-1m)
├─ Wait for PHP-FPM ready (10-20s)
├─ Run artisan config:cache
├─ Run artisan migrate
└─ Show status & URLs

    ↓

Result:
✅ All services running
✅ Database migrated
✅ App ready to use
✅ URLs displayed
```

---

## 📚 Files in This Architecture

```
docker-compose.yml          ← Main service definitions
docker/
├─ php/
│  ├─ Dockerfile           ← Builds Laravel image
│  ├─ docker-entrypoint.sh ← Startup script
│  └─ local.ini            ← PHP config
├─ nginx/
│  └─ conf.d/              ← Nginx config
└─ mysql/                  ← MySQL setup (optional)

app/                        ← Laravel application
├─ config/                 ← App config
├─ routes/                 ← API routes
├─ database/
│  └─ migrations/          ← DB schema
└─ storage/                ← Logs & uploads

.env                        ← Environment variables
                           (Database passwords, API keys, etc.)
```

This architecture ensures:
- ✅ Scalability (easily add more services)
- ✅ Isolation (containers don't interfere)
- ✅ Persistence (data survives restarts)
- ✅ Health checks (automatic recovery)
- ✅ Easy development (live code changes)
