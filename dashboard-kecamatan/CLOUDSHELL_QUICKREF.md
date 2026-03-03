# Cloud Shell Quick Reference Card

## 🚀 Deploy (Just Copy & Paste)
```bash
git clone https://github.com/benchoaz/KECAMATAN-LAYANAN-WHATSAPP.git && cd KECAMATAN-LAYANAN-WHATSAPP && bash cloudshell-quickstart.sh
```

---

## 📱 Access Services
| Service | URL | Purpose |
|---------|-----|---------|
| **Main App** | http://localhost:8000 | Laravel Dashboard |
| **n8n** | http://localhost:5679 | Workflow Automation |
| **WAHA** | http://localhost:3000 | WhatsApp API |
| **Database** | localhost:3307 | MySQL (external) |

---

## 🔧 Essential Commands
```bash
docker-compose ps              # List all services
docker-compose logs -f         # View logs (all)
docker-compose logs -f app     # View app logs
docker-compose restart         # Restart all services
docker-compose restart app     # Restart specific service
docker-compose stop            # Stop services (keep data)
docker-compose down            # Stop and remove containers
docker-compose down -v         # Stop and delete everything

docker-compose exec app bash                    # Access app shell
docker-compose exec app php artisan migrate    # Run migrations
docker-compose exec db mysql -u root -proot    # Access database
```

---

## 🆘 Quick Fixes
```bash
# Services not starting?
docker-compose down -v && docker-compose up -d

# Check if everything is running
docker-compose ps

# View recent errors
docker-compose logs --tail=50

# Restart specific service that's failing
docker-compose restart app

# Check resource usage (if slow)
docker stats
```

---

## 📊 Status Checks
```bash
# All containers running?
docker-compose ps

# Resources OK?
docker stats

# Logs show errors?
docker-compose logs

# Database responding?
docker-compose exec db mysqladmin ping -u root -proot

# App connected to database?
docker-compose exec app php artisan migrate --dry-run
```

---

## 📂 Important Files
- `.env` - Configuration & secrets
- `docker-compose.yml` - Service definitions
- `app/` - Laravel application code
- `database/` - Migrations & seeds
- `storage/` - Logs & uploads

---

## 🌐 Web Preview
1. Click **Web Preview** button (top-right)
2. Select **Port 8000**
3. App opens automatically

---

## 💡 Pro Tips
- Keep Cloud Shell tab open for continuous operation
- Use `docker-compose logs -f` to monitor real-time issues
- Stop unused services to save memory: `docker-compose stop waha`
- Database persists in Docker volume (safe)
- Data persists 30 days in Cloud Shell

---

## 🆘 Help
- Full guide: `CLOUD_SHELL_GUIDE.md`
- Detailed docs: `CLOUD_SHELL_DEPLOY.md`
- View logs: `docker-compose logs`
- Check status: `docker-compose ps`
