# 🚀 Cloud Shell Setup - Using Existing Repository

Your repository already exists in Cloud Shell. Here's how to deploy:

## Step 1: Update Your Local Project

On your **local machine** (Windows), push the deployment files to GitHub:

### Option A: Using Git Command Line

```bash
cd d:\Projectku\dashboard-kecamatan

# Add deployment files
git add cloudshell-quickstart.sh deploy-cloudshell.sh COMMANDS.sh *.md docker-compose.cloudshell.yml

# Commit
git commit -m "Add Google Cloud Shell deployment scripts and documentation"

# Push to GitHub
git push origin main
```

### Option B: Using GitHub Desktop

1. Open GitHub Desktop
2. Select repository: `KECAMATAN-LAYANAN-WHATSAPP`
3. You'll see all new files in "Changes"
4. Click "Commit to main"
5. Click "Push origin"

---

## Step 2: In Cloud Shell Terminal

Once files are pushed to GitHub:

```bash
# Navigate to your project
cd ~/KECAMATAN-LAYANAN-WHATSAPP

# Pull latest changes from GitHub
git pull origin main

# Verify files are there
ls -la cloudshell-quickstart.sh

# Make script executable
chmod +x cloudshell-quickstart.sh

# Run deployment
bash cloudshell-quickstart.sh
```

---

## ✅ What Should Happen

```
✅ Files pulled from GitHub
✅ Docker images downloaded
✅ Services started (mysql, nginx, php, n8n, waha)
✅ Database migrated
✅ All services running on ports 8000, 5679, 3000
✅ URL to access: http://localhost:8000
```

---

## 📋 Files That Need to Be Pushed

These files were created locally and need to be pushed:

```
✅ cloudshell-quickstart.sh
✅ deploy-cloudshell.sh
✅ COMMANDS.sh
✅ 00_START_HERE.md
✅ CLOUDSHELL_QUICKREF.md
✅ CLOUDSHELL_README.md
✅ CLOUD_SHELL_DEPLOY.md
✅ CLOUD_SHELL_DEPLOY_README.md
✅ CLOUD_SHELL_GUIDE.md
✅ ARCHITECTURE.md
✅ DEPLOYMENT_COMPLETE.md
✅ docker-compose.cloudshell.yml
```

---

## 🆘 If You See This in Cloud Shell

```
bash: cloudshell-quickstart.sh: No such file or directory
```

**Solution:** The files haven't been pushed to GitHub yet. Do Step 1 above first!

---

## 🔄 Complete Workflow

```
1. Local Machine (Windows)
   ├─ Open terminal/PowerShell
   ├─ cd d:\Projectku\dashboard-kecamatan
   ├─ git add ... (all new files)
   ├─ git commit -m "..."
   └─ git push origin main

2. Cloud Shell
   ├─ cd ~/KECAMATAN-LAYANAN-WHATSAPP
   ├─ git pull origin main
   ├─ chmod +x cloudshell-quickstart.sh
   └─ bash cloudshell-quickstart.sh

3. Result
   └─ ✅ App running on http://localhost:8000
```

---

## ⚡ Quick Commands

### On Local Machine (Windows PowerShell/Git Bash)

```bash
cd d:\Projectku\dashboard-kecamatan
git status  # See what changed
git add -A  # Add everything new
git commit -m "Add Cloud Shell deployment"
git push origin main
```

### In Cloud Shell

```bash
cd ~/KECAMATAN-LAYANAN-WHATSAPP
git pull origin main
bash cloudshell-quickstart.sh
```

---

## ✨ Visual Workflow

```
Your Computer                    GitHub                    Cloud Shell
────────────────                 ──────                    ────────────
Files created locally            (Remote Repository)       (Environment)
        ↓
    git add
    git commit
    git push
        │
        ├────────────────────────→ Updated Repo
        │                             ↓
        │                         (Files synced)
        │
        ├──────────────────────────────────→ git pull
        │                                        ↓
        │                                   Files Downloaded
        │                                        ↓
        │                                   bash script
        │                                        ↓
        │                                   Services Start
        │                                        ↓
        └────────────────────────────────→ App Live! ✅
```

---

## 📝 Step-by-Step for Windows (PowerShell)

```powershell
# 1. Navigate to project
cd d:\Projectku\dashboard-kecamatan

# 2. Check git status
git status

# 3. Add new files
git add cloudshell-quickstart.sh
git add deploy-cloudshell.sh
git add COMMANDS.sh
git add "*.md"
git add docker-compose.cloudshell.yml

# Or add everything new:
git add -A

# 4. Commit
git commit -m "Add Cloud Shell deployment automation

- One-command deployment script
- Comprehensive documentation
- Troubleshooting guides"

# 5. Push to GitHub
git push origin main

# Verify it worked
git log --oneline -5
```

---

## 🌐 Then In Cloud Shell

```bash
# 1. Go to your repo
cd ~/KECAMATAN-LAYANAN-WHATSAPP

# 2. Pull latest from GitHub
git pull origin main

# 3. Make sure script is executable
chmod +x cloudshell-quickstart.sh

# 4. Run deployment
bash cloudshell-quickstart.sh

# 5. Wait 5 minutes for services to start

# 6. Access app
# Click Web Preview → Port 8000
# Or visit http://localhost:8000
```

---

## 🎯 You Should Now Have

After running the script:

```
✅ MySQL database running
✅ Laravel app running on port 8000
✅ Nginx web server running
✅ n8n automation on port 5679
✅ WAHA WhatsApp API on port 3000
✅ All databases migrated
✅ All services healthy and auto-restarting
```

---

## 📚 Reference

- **Quick Ref**: `CLOUDSHELL_QUICKREF.md`
- **Full Guide**: `CLOUD_SHELL_GUIDE.md`
- **All Commands**: `COMMANDS.sh`

---

## ⚠️ Common Issues

**"fatal: destination path already exists"**
→ You're already in the repo (good!) Just do `git pull`

**"bash: cloudshell-quickstart.sh: No such file or directory"**
→ Files not pushed yet. Push from local machine first.

**"Docker is not running"**
→ Docker starts automatically in Cloud Shell, wait a moment

**"Port 8000 in use"**
→ Something else is using it. Try: `sudo lsof -i :8000`

---

## ✅ Checklist

- [ ] All deployment files created on local machine
- [ ] `git add` all new files
- [ ] `git commit -m "..."` with description
- [ ] `git push origin main` successful
- [ ] Verified on GitHub (files visible in repo)
- [ ] In Cloud Shell: `git pull origin main`
- [ ] `bash cloudshell-quickstart.sh` runs
- [ ] Services start successfully
- [ ] Access app at http://localhost:8000

---

**Ready?** Push from your local machine first, then pull and deploy in Cloud Shell!
