# 🚀 Quick Deploy to Google Cloud Shell

Deploy this project to Google Cloud Shell in **one command**:

```bash
git clone https://github.com/benchoaz/KECAMATAN-LAYANAN-WHATSAPP.git && \
cd KECAMATAN-LAYANAN-WHATSAPP && \
bash cloudshell-quickstart.sh
```

**Time to running:** ~5 minutes ⏱️

---

## ✨ What You Get

| Component | Port | Status |
|-----------|------|--------|
| 🌐 Laravel Dashboard | 8000 | ✅ Running |
| 🔄 n8n Workflows | 5679 | ✅ Running |
| 💬 WAHA WhatsApp API | 3000 | ✅ Running |
| 🗄️ MySQL Database | 3307 | ✅ Running |

All services automatically started, configured, and ready to use!

---

## 🎯 First Time Setup

1. **Open Cloud Shell**: https://shell.cloud.google.com/
2. **Copy & paste**:
   ```bash
   git clone https://github.com/benchoaz/KECAMATAN-LAYANAN-WHATSAPP.git
   cd KECAMATAN-LAYANAN-WHATSAPP
   bash cloudshell-quickstart.sh
   ```
3. **Wait** 5 minutes for services to start
4. **Click Web Preview** → Port 8000 → Your app is live! 🎉

---

## 📚 Documentation

- **Quick Start**: `CLOUDSHELL_QUICKREF.md` (one-page cheat sheet)
- **Full Guide**: `CLOUD_SHELL_GUIDE.md` (comprehensive guide with examples)
- **Architecture**: `ARCHITECTURE.md` (see how it all connects)
- **Troubleshooting**: `CLOUD_SHELL_DEPLOY.md` (solutions to common issues)

---

## 🔧 Common Commands

```bash
# See all services running
docker-compose ps

# View logs in real-time
docker-compose logs -f

# Restart services
docker-compose restart

# Run database migrations
docker-compose exec app php artisan migrate

# Access app shell
docker-compose exec app bash
```

---

## 🌐 Access Your Services

After deployment:

**Main Application**
- Click **Web Preview** button (top-right of Cloud Shell editor)
- Select **Port 8000**
- Your Laravel app opens in a new tab

**Other Services**
- n8n: http://localhost:5679
- WAHA: http://localhost:3000
- Database: localhost:3307

---

## 💾 Data Persistence

✅ Cloud Shell provides **5GB persistent storage**
- Source code persists
- Database data persists
- Workflows & sessions persist
- Data kept for 30 days (inactivity timeout)

---

## 🆘 Issues?

**Services not starting?**
```bash
docker-compose logs
```

**Need to restart?**
```bash
docker-compose down -v && docker-compose up -d
```

**More help?** See `CLOUD_SHELL_GUIDE.md` (comprehensive troubleshooting)

---

## 📋 Files Included

| File | Purpose |
|------|---------|
| `cloudshell-quickstart.sh` | ⭐ One-click deployment |
| `CLOUDSHELL_QUICKREF.md` | Quick command reference |
| `CLOUD_SHELL_GUIDE.md` | Detailed guide & troubleshooting |
| `ARCHITECTURE.md` | System design & data flow |
| `docker-compose.yml` | Service configuration |

---

## 🎓 Learn More

- [Laravel Documentation](https://laravel.com/docs)
- [n8n Workflows](https://docs.n8n.io)
- [WAHA WhatsApp API](https://github.com/devlikeapro/waha)
- [Docker Compose Docs](https://docs.docker.com/compose/)

---

**Ready to deploy?** Start with:

```bash
bash cloudshell-quickstart.sh
```

Your app will be running in Cloud Shell in 5 minutes! 🚀
