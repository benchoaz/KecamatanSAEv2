# Cloud Shell Deployment - Ready to Deploy 🚀

Your **KECAMATAN-LAYANAN-WHATSAPP** project is now ready to deploy to Google Cloud Shell!

## What's Included

✅ **Automated deployment script** - `deploy-cloudshell.sh`
✅ **Quick-start script** - `cloudshell-quickstart.sh`
✅ **Cloud Shell optimized compose** - `docker-compose.cloudshell.yml`
✅ **Complete documentation** - `CLOUD_SHELL_DEPLOY.md`

## 🚀 Quick Start (Copy & Paste)

Open Google Cloud Shell and run:

```bash
# Clone the repo
git clone https://github.com/benchoaz/KECAMATAN-LAYANAN-WHATSAPP.git
cd KECAMATAN-LAYANAN-WHATSAPP

# Run quick start
bash cloudshell-quickstart.sh
```

**That's it!** Services will start automatically.

## 🌐 Access Your App

Once deployed, use **Cloud Shell's Web Preview**:

1. Click the **Web Preview** button (top-right corner of Cloud Shell)
2. Choose **Preview on port 8000**
3. Your Laravel app opens automatically

**Service URLs:**
- Main App: http://localhost:8000
- n8n: http://localhost:5679
- WAHA: http://localhost:3000
- Database: localhost:3307

## 📋 Manual Deployment (Full Control)

If you prefer step-by-step deployment:

```bash
# 1. Clone
git clone https://github.com/benchoaz/KECAMATAN-LAYANAN-WHATSAPP.git
cd KECAMATAN-LAYANAN-WHATSAPP

# 2. Setup environment
cp .env.example .env

# 3. Start services
docker-compose up -d

# 4. Wait ~1-2 minutes, then run migrations
docker-compose exec app php artisan migrate

# 5. View logs
docker-compose logs -f
```

## 🛠️ Useful Commands

```bash
# View all services
docker-compose ps

# View logs (all services)
docker-compose logs -f

# View logs (specific service)
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f db

# Restart services
docker-compose restart

# Stop all services
docker-compose stop

# Stop and remove everything
docker-compose down

# Run Artisan command
docker-compose exec app php artisan tinker
```

## ⚠️ Important Notes

### Database Credentials
Located in `.env`:
- **Host**: `db` (container name, not localhost)
- **Port**: `3306` (internal), `3307` (exposed to host)
- **User**: `root`
- **Password**: `root`

### Environment Variables
All config is in `.env`. Key variables:
```
DB_HOST=db
DB_DATABASE=dashboard_kecamatan
DB_USERNAME=root
DB_PASSWORD=root
WAHA_API_KEY=waha_secret_key_2024
```

### Storage & Persistence
- Cloud Shell provides **5 GB persistent storage** in home directory
- Docker volumes are created automatically in `/tmp`
- **Data persists** between sessions (30 days inactivity timeout)

### Resource Limits
Cloud Shell resources:
- **Memory**: 1 GB (lightweight services recommended)
- **CPU**: Shared (sufficient for development)
- **Storage**: 5 GB (home directory)

If you hit limits:
1. Stop services: `docker-compose stop`
2. Clean up: `docker system prune -a`
3. Restart: `docker-compose up -d`

## 🔧 Troubleshooting

### Services not starting?
```bash
docker-compose logs db
docker-compose logs app
docker-compose logs nginx
```

### Port 8000 already in use?
```bash
# Find process
sudo lsof -i :8000

# Kill it
sudo kill -9 <PID>

# Restart
docker-compose restart
```

### Database connection error?
```bash
# Check MySQL is ready
docker-compose exec db mysqladmin ping -u root -proot

# Check connectivity
docker-compose exec app ping db
```

### Out of memory?
```bash
# Check usage
docker stats

# Clean up
docker system prune -a
docker-compose down -v
docker-compose up -d
```

## 📚 Full Documentation

See `CLOUD_SHELL_DEPLOY.md` for comprehensive guide including:
- Database migrations
- Production deployment options
- Advanced troubleshooting
- Security considerations

## 🆘 Need Help?

1. Check logs: `docker-compose logs`
2. Read documentation: `CLOUD_SHELL_DEPLOY.md`
3. Verify services: `docker-compose ps`
4. Test connectivity: `docker network inspect dashboard-kecamatan_app-network`

## 📝 Next Steps

After deployment:

1. **Update environment variables** if deploying to production
2. **Configure ngrok/tunnel** for external webhooks (n8n, WAHA)
3. **Set up backup strategy** for database
4. **Monitor logs** and performance
5. **Plan migration** to Cloud Run or Compute Engine for production

---

**Ready?** Run the quick start command above and your app will be live in 5 minutes! 🎉
