# 🚀 Cloud Shell Deployment Guide
# KECAMATAN-LAYANAN-WHATSAPP

Automated deployment guide for running your WhatsApp integration project on Google Cloud Shell.

---

## ⚡ TL;DR (Just Want It Running?)

```bash
git clone https://github.com/benchoaz/KECAMATAN-LAYANAN-WHATSAPP.git
cd KECAMATAN-LAYANAN-WHATSAPP
bash cloudshell-quickstart.sh
```

**Wait 5 minutes → Click Web Preview → Done!**

---

## 📋 What This Deploys

| Service | Port | Purpose |
|---------|------|---------|
| **Laravel App** | 8000 | Main dashboard application |
| **Nginx** | 8000 | Web server (reverse proxy) |
| **PHP-FPM** | 9000 | PHP runtime (internal only) |
| **MySQL** | 3307 | Database |
| **n8n** | 5679 | Workflow automation (WhatsApp integration) |
| **WAHA** | 3000 | WhatsApp HTTP API |

All services run in Docker containers within Cloud Shell.

---

## 🎯 Step-by-Step Deployment

### Step 1: Open Cloud Shell

Go to: https://shell.cloud.google.com/?show=ide%2Cterminal

The IDE and Terminal tabs will open automatically.

### Step 2: Clone Repository

In the terminal (bottom half), run:

```bash
git clone https://github.com/benchoaz/KECAMATAN-LAYANAN-WHATSAPP.git
cd KECAMATAN-LAYANAN-WHATSAPP
```

### Step 3: Run Automatic Deployment

```bash
bash cloudshell-quickstart.sh
```

The script will:
- ✅ Set up `.env` file
- ✅ Pull Docker images
- ✅ Start all containers
- ✅ Wait for services to be ready
- ✅ Run database migrations
- ✅ Show you the URLs

**Duration: 3-5 minutes on first run**

### Step 4: Access Your App

Once the script finishes, you'll see:
```
✅ Deployment complete!

📱 Access your services:
  • Main App:  http://localhost:8000
  • n8n:       http://localhost:5679
  • WAHA:      http://localhost:3000
```

**Option A: Web Preview (Recommended)**
1. Click the **Web Preview** button in the top-right corner
2. Select **Preview on port 8000**
3. Your app opens in a new tab automatically

**Option B: Direct URL**
- Main App: http://localhost:8000
- n8n Dashboard: http://localhost:5679
- WAHA Dashboard: http://localhost:3000

---

## 🔧 Common Commands

### View Services Status
```bash
docker-compose ps
```

### View Real-Time Logs
```bash
docker-compose logs -f
```

### View Specific Service Logs
```bash
# PHP App logs
docker-compose logs -f app

# Nginx logs
docker-compose logs -f nginx

# Database logs
docker-compose logs -f db

# n8n logs
docker-compose logs -f n8n

# WAHA logs
docker-compose logs -f waha
```

### Restart Services
```bash
# Restart all
docker-compose restart

# Restart specific service
docker-compose restart app
docker-compose restart nginx
docker-compose restart db
```

### Stop Services (Keep Data)
```bash
docker-compose stop
```

### Stop and Remove Everything
```bash
docker-compose down
```

### Stop and Remove Everything + Delete Data
```bash
docker-compose down -v
```

### Run Database Migrations Again
```bash
docker-compose exec app php artisan migrate
```

### Access PHP Shell
```bash
docker-compose exec app bash
```

### Run Artisan Commands
```bash
docker-compose exec app php artisan tinker
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
```

---

## 📊 Monitoring

### Check Resource Usage
```bash
docker stats
```

Shows CPU, Memory, and I/O usage for each container.

### Check Disk Space
```bash
df -h
docker system df
```

### Check Port Usage
```bash
sudo netstat -tulpn | grep LISTEN
```

---

## 🆘 Troubleshooting

### Services Not Starting?

**Check logs:**
```bash
docker-compose logs db
docker-compose logs app
docker-compose logs nginx
```

**Common issues:**
- MySQL taking too long to start (wait 30-60 seconds)
- PHP-FPM not connecting to MySQL (wait for DB to be ready)
- Port conflicts (uncommon in Cloud Shell)

**Solution:**
```bash
docker-compose down -v
docker-compose up -d
```

### Database Connection Error?

```bash
# Check MySQL is running
docker-compose ps db

# Check MySQL is responding
docker-compose exec db mysqladmin ping -u root -proot

# Check connectivity from app
docker-compose exec app ping db
```

### "Port Already in Use"?

```bash
# Find what's using port 8000
sudo lsof -i :8000

# Kill it
sudo kill -9 <PID>

# Restart services
docker-compose restart
```

### Out of Memory?

```bash
# Check usage
docker stats

# Clean up unused images/containers
docker system prune -a

# Restart services
docker-compose down -v
docker-compose up -d
```

### Web Preview Not Working?

1. Make sure port 8000 is running: `docker-compose ps nginx`
2. Try accessing directly: `curl http://localhost:8000`
3. If that works, try Web Preview again
4. If Web Preview still fails, use the direct URL in a new tab

---

## 🔐 Environment Variables

Key variables in `.env`:

```
# App Configuration
APP_NAME=Kecamatan SAE
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_HOST=db
DB_DATABASE=dashboard_kecamatan
DB_USERNAME=root
DB_PASSWORD=root

# WhatsApp Integration
WAHA_API_KEY=waha_secret_key_2024
WHATSAPP_API_TOKEN=62a72516dd1b418499d9dd22075ccfa0
DASHBOARD_API_TOKEN=fJJCz33U8jkHIKXEhTpv91GZJz97VGPHmItYlvxPNUi8obg05BYsZCh5TmfAznma

# n8n Configuration
N8N_REPLY_WEBHOOK_URL=http://dashboard-n8n:5678/webhook/whatsapp-primary
```

**To change variables:**
1. Edit `.env` file
2. Restart services: `docker-compose restart`

---

## 💾 Data Persistence

Cloud Shell provides **5 GB persistent storage** in your home directory (`~`).

**What persists:**
- ✅ Source code
- ✅ `.env` file
- ✅ Database data (MySQL)
- ✅ Workflow configurations (n8n)
- ✅ WhatsApp sessions (WAHA)

**What doesn't persist:**
- ❌ Docker images (re-downloaded on restart)
- ❌ Container logs (cleared on restart)

**Data persists for 30 days** of inactivity. After that, your Cloud Shell environment is deleted.

---

## 🚀 Keeping Services Running

Cloud Shell sessions timeout after 1 hour of inactivity. To keep services running:

**Option 1: Keep Cloud Shell Tab Open**
- Services keep running as long as your browser tab is open
- If you close the tab, services stop (data is preserved)

**Option 2: Deploy to Cloud Run (Recommended for Production)**
- Serverless, always running
- Better for continuous operation
- Can handle webhooks from WAHA/n8n

Contact your DevOps team for Cloud Run deployment.

---

## 📈 Performance Tips

### Speed up first deployment:
```bash
# Pre-pull images
docker-compose pull

# Then start
docker-compose up -d
```

### Reduce memory usage:
```bash
# Stop WAHA if not using WhatsApp
docker-compose stop waha

# Stop n8n if not using workflows
docker-compose stop n8n

# Only use what you need
```

### Speed up subsequent runs:
```bash
# Bring services back up (no rebuild)
docker-compose up -d

# This reuses existing containers
```

---

## 🔗 Useful Links

- **Laravel Docs**: https://laravel.com/docs
- **n8n Docs**: https://docs.n8n.io
- **WAHA Docs**: https://github.com/devlikeapro/waha
- **Docker Compose Docs**: https://docs.docker.com/compose/reference/
- **Google Cloud Shell Docs**: https://cloud.google.com/shell/docs

---

## 📞 Support

For issues:

1. **Check logs first**: `docker-compose logs -f`
2. **Restart services**: `docker-compose restart`
3. **Check connectivity**: `docker-compose ps`
4. **Read documentation**: This file + CLOUD_SHELL_DEPLOY.md

For detailed troubleshooting, see `CLOUD_SHELL_DEPLOY.md`.

---

## ✅ Checklist

- [ ] Opened Cloud Shell
- [ ] Cloned repository
- [ ] Ran `cloudshell-quickstart.sh`
- [ ] Services are running (`docker-compose ps`)
- [ ] Accessed app via Web Preview on port 8000
- [ ] Checked logs for errors
- [ ] Created `.env` with proper credentials

---

**Ready to deploy?** Run this:

```bash
git clone https://github.com/benchoaz/KECAMATAN-LAYANAN-WHATSAPP.git
cd KECAMATAN-LAYANAN-WHATSAPP
bash cloudshell-quickstart.sh
```

Your app will be running in 5 minutes! 🎉
