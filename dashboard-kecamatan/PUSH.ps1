# Push deployment files to GitHub
# Run this in PowerShell from your project directory

cd "d:\Projectku\dashboard-kecamatan"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Pushing Deployment Files to GitHub" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Stage files
Write-Host "📝 Staging files..." -ForegroundColor Yellow
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

Write-Host "✅ Files staged" -ForegroundColor Green
Write-Host ""

# Commit
Write-Host "💾 Committing..." -ForegroundColor Yellow
git commit -m "Add Google Cloud Shell deployment automation

- cloudshell-quickstart.sh: One-command deployment
- deploy-cloudshell.sh: Advanced deployment with options
- COMMANDS.sh: Copy-paste command reference
- Comprehensive documentation for Cloud Shell
- Docker Compose optimized for Cloud Shell
- Architecture diagrams and troubleshooting guides"

Write-Host "✅ Committed" -ForegroundColor Green
Write-Host ""

# Push
Write-Host "🚀 Pushing to GitHub..." -ForegroundColor Yellow
git push origin main

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "✅ SUCCESS! Files pushed to GitHub" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Go to Cloud Shell" -ForegroundColor White
Write-Host "2. Run: cd ~/KECAMATAN-LAYANAN-WHATSAPP" -ForegroundColor White
Write-Host "3. Run: git pull origin main" -ForegroundColor White
Write-Host "4. Run: bash cloudshell-quickstart.sh" -ForegroundColor White
Write-Host ""
