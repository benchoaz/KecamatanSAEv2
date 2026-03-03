#!/bin/bash
# Push all deployment files to GitHub

echo "📤 Pushing deployment files to GitHub..."
echo ""

cd "d:\Projectku\dashboard-kecamatan"

# Add all deployment files
git add \
  cloudshell-quickstart.sh \
  deploy-cloudshell.sh \
  COMMANDS.sh \
  00_START_HERE.md \
  CLOUDSHELL_QUICKREF.md \
  CLOUDSHELL_README.md \
  CLOUD_SHELL_DEPLOY.md \
  CLOUD_SHELL_DEPLOY_README.md \
  CLOUD_SHELL_GUIDE.md \
  ARCHITECTURE.md \
  DEPLOYMENT_COMPLETE.md \
  docker-compose.cloudshell.yml

echo "✅ Files staged"
echo ""

# Commit
git commit -m "Add Google Cloud Shell deployment automation

- cloudshell-quickstart.sh: One-command deployment
- deploy-cloudshell.sh: Advanced deployment script  
- COMMANDS.sh: Copy-paste command reference
- Comprehensive documentation for Cloud Shell deployment
- Docker Compose optimized for Cloud Shell
- Architecture diagrams and troubleshooting guides"

echo "✅ Committed"
echo ""

# Push to GitHub
git push origin main

echo "✅ Pushed to GitHub!"
echo ""
echo "Now in Cloud Shell, run:"
echo "  cd ~/KECAMATAN-LAYANAN-WHATSAPP"
echo "  git pull"
echo "  bash cloudshell-quickstart.sh"
