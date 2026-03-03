@echo off
REM Push deployment files to GitHub

cd /d "d:\Projectku\dashboard-kecamatan"

echo.
echo ========================================
echo Pushing Deployment Files to GitHub
echo ========================================
echo.

git add cloudshell-quickstart.sh
git add deploy-cloudshell.sh
git add COMMANDS.sh
git add CLOUD_SHELL_SETUP.md
git add RUN_NOW.md
git add 00_START_HERE.md
git add CLOUDSHELL_QUICKREF.md
git add CLOUDSHELL_README.md
git add CLOUD_SHELL_DEPLOY.md
git add CLOUD_SHELL_DEPLOY_README.md
git add CLOUD_SHELL_GUIDE.md
git add ARCHITECTURE.md
git add DEPLOYMENT_COMPLETE.md
git add docker-compose.cloudshell.yml

echo.
echo Step 1: Files added to staging area
echo.

git commit -m "Add Google Cloud Shell deployment automation

- cloudshell-quickstart.sh: One-command deployment
- deploy-cloudshell.sh: Advanced deployment with options
- COMMANDS.sh: Copy-paste command reference
- Comprehensive documentation for Cloud Shell
- Docker Compose optimized for Cloud Shell
- Architecture diagrams and guides"

echo.
echo Step 2: Committed to local repository
echo.

git push origin main

echo.
echo ========================================
echo SUCCESS! Files pushed to GitHub
echo ========================================
echo.
echo Next steps:
echo 1. Go to Cloud Shell
echo 2. Run: cd ~/KECAMATAN-LAYANAN-WHATSAPP
echo 3. Run: git pull origin main
echo 4. Run: bash cloudshell-quickstart.sh
echo.
pause
