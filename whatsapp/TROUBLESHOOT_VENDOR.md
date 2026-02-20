# Troubleshooting Missing Vendor

If you see `require(.../vendor/autoload.php): Failed to open stream`, run:

```bash
cd d:\Projectku\whatsapp
docker exec whatsapp-api-gateway composer install
```

This installs PHP dependencies inside the container.
