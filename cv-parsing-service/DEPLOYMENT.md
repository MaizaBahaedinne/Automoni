# CV Parsing Service - Production Deployment Guide

## 🚀 Production Checklist

- [ ] Server has Python 3.8+
- [ ] Server has Tesseract OCR installed
- [ ] .env.production created with real values
- [ ] SSL certificate obtained (Let's Encrypt)
- [ ] Nginx/Apache installed for reverse proxy
- [ ] Gunicorn installed
- [ ] Upload/logs directories created with proper permissions
- [ ] Firewall rules configured
- [ ] Monitoring/alerting set up
- [ ] Backup strategy defined

---

## 📦 Installation Steps

### 1. Server Preparation

```bash
# Ubuntu/Debian
sudo apt-get update && apt-get upgrade -y
sudo apt-get install -y python3.10 python3-pip python3-venv
sudo apt-get install -y nginx
sudo apt-get install -y tesseract-ocr
sudo apt-get install -y supervisor

# Create service user
sudo useradd -r -s /bin/bash cv-parsing

# Create directories
sudo mkdir -p /var/uploads/cv-parsing
sudo mkdir -p /var/log/cv-parsing
sudo mkdir -p /var/run/cv-parsing
sudo chown -R cv-parsing:cv-parsing /var/uploads/cv-parsing
sudo chown -R cv-parsing:cv-parsing /var/log/cv-parsing
sudo chown -R cv-parsing:cv-parsing /var/run/cv-parsing
```

### 2. Clone & Setup

```bash
cd /opt
sudo git clone <repo> cv-parsing-service
cd cv-parsing-service
sudo chown -R cv-parsing:cv-parsing .

# Create virtual environment
sudo -u cv-parsing python3 -m venv venv

# Activate and install
sudo -u cv-parsing venv/bin/pip install -r requirements.txt
sudo -u cv-parsing venv/bin/pip install -r requirements-prod.txt
```

### 3. Configuration

```bash
# Copy production config
sudo cp .env.production .env

# Edit with real values
sudo nano .env
# Set:
# - API_KEY to strong random string
# - OLLAMA_BASE_URL to production instance
# - CORS_ORIGINS to your CI4 domain
```

### 4. SSL Certificate (Let's Encrypt)

```bash
sudo apt-get install -y certbot python3-certbot-nginx

sudo certbot certonly --nginx \
  -d your-domain.com \
  -d www.your-domain.com \
  --non-interactive \
  --agree-tos \
  -m admin@your-domain.com
```

### 5. Nginx Configuration

```bash
# Copy nginx config
sudo cp nginx.conf /etc/nginx/sites-available/cv-parsing.conf

# Edit domain name
sudo sed -i 's/your-domain.com/YOUR_DOMAIN/g' /etc/nginx/sites-available/cv-parsing.conf

# Enable site
sudo ln -s /etc/nginx/sites-available/cv-parsing.conf /etc/nginx/sites-enabled/

# Test config
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

### 6. Supervisor Configuration

Create `/etc/supervisor/conf.d/cv-parsing.conf`:

```ini
[program:cv-parsing]
command=/opt/cv-parsing-service/venv/bin/gunicorn \
    --config /opt/cv-parsing-service/gunicorn_config.py \
    main:app
directory=/opt/cv-parsing-service
user=cv-parsing
environment=PATH="/opt/cv-parsing-service/venv/bin"
stopasgroup=true
stdout_logfile=/var/log/cv-parsing/supervisor.log
stderr_logfile=/var/log/cv-parsing/supervisor-error.log
autostart=true
autorestart=true
startsecs=10
```

Then:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start cv-parsing
```

### 7. Verify Deployment

```bash
# Check service
sudo supervisorctl status cv-parsing

# Check logs
sudo tail -f /var/log/cv-parsing/error.log
sudo tail -f /var/log/cv-parsing/access.log

# Test health
curl https://your-domain.com/health

# Test with curl
curl -X POST \
  -H "X-API-Key: YOUR_API_KEY" \
  -F "file=@test.pdf" \
  https://your-domain.com/api/parse-cv
```

---

## 🔒 Security Hardening

### 1. Firewall

```bash
sudo ufw allow 22/tcp   # SSH
sudo ufw allow 80/tcp   # HTTP (redirect only)
sudo ufw allow 443/tcp  # HTTPS
sudo ufw allow 8001/tcp # Gunicorn (local only, via Nginx)
sudo ufw enable
```

### 2. API Key Rotation

```bash
# Generate new key
python3 -c "import secrets; print(secrets.token_urlsafe(32))"

# Update .env and restart
sudo nano .env
sudo supervisorctl restart cv-parsing
```

### 3. Log Rotation

Create `/etc/logrotate.d/cv-parsing`:

```
/var/log/cv-parsing/*.log {
    daily
    rotate 14
    compress
    delaycompress
    missingok
    notifempty
    create 0640 cv-parsing cv-parsing
    sharedscripts
}
```

### 4. Monitor Disk Space

```bash
# CV uploads can grow large
df -h /var/uploads/cv-parsing

# Set up alert if approaching limit
# Add to crontab: 0 * * * * df /var/uploads | mail -s "Storage Alert" admin@domain.com
```

---

## 📊 Monitoring & Maintenance

### Health Check Script

```bash
#!/bin/bash
# /opt/cv-parsing-service/health-check.sh

API_URL="https://your-domain.com/health"
API_KEY="YOUR_API_KEY"

response=$(curl -s -H "X-API-Key: $API_KEY" "$API_URL")

if echo "$response" | grep -q "ok"; then
    echo "✅ CV Parsing Service is healthy"
    exit 0
else
    echo "❌ CV Parsing Service is DOWN"
    # Send alert email
    exit 1
fi
```

Add to crontab:
```bash
*/5 * * * * /opt/cv-parsing-service/health-check.sh || mail -s "CV Parsing Service Down" admin@domain.com
```

### View Logs

```bash
# Real-time logs
sudo tail -f /var/log/cv-parsing/error.log

# Access logs
sudo tail -f /var/log/cv-parsing/access.log

# Search for errors
sudo grep -i error /var/log/cv-parsing/cv_parsing.log | tail -20
```

### Restart Service

```bash
sudo supervisorctl restart cv-parsing
```

---

## 🚨 Troubleshooting Production

### Service won't start

```bash
# Check supervisor status
sudo supervisorctl status cv-parsing

# View real-time output
sudo supervisorctl tail -f cv-parsing stderr

# Check Python errors
sudo -u cv-parsing /opt/cv-parsing-service/venv/bin/python main.py
```

### High memory usage

```bash
# Reduce workers in gunicorn_config.py
workers = 2  # Instead of (2 * cpu_count()) + 1

# Restart service
sudo supervisorctl restart cv-parsing
```

### Strange parsing results

```bash
# Enable debug logging
sudo nano .env
# Set: LOG_LEVEL=DEBUG

# Restart
sudo supervisorctl restart cv-parsing

# Check logs
sudo tail -f /var/log/cv-parsing/cv_parsing.log
```

---

## 📈 Performance Tuning

### 1. Increase File Size Limit

```bash
# In .env: MAX_FILE_SIZE_MB=50
# In nginx.conf: client_max_body_size 50M;
```

### 2. Tune Gunicorn Workers

```python
# In gunicorn_config.py
# Start with default, observe CPU/memory, then adjust
# Rule: (2 x num_cores) + 1 is usually good
```

### 3. Cache Upload Dir

Move uploads to fast storage (SSD):
```bash
sudo mkdir -p /mnt/ssd/cv-uploads
sudo chown cv-parsing:cv-parsing /mnt/ssd/cv-uploads

# Update .env
UPLOAD_DIR=/mnt/ssd/cv-uploads
```

---

## 🔄 Updates & Maintenance

### Update Python Dependencies

```bash
cd /opt/cv-parsing-service

# Check for updates
sudo -u cv-parsing venv/bin/pip list --outdated

# Update safely
sudo -u cv-parsing venv/bin/pip install --upgrade pip
sudo -u cv-parsing venv/bin/pip install -r requirements.txt --upgrade

# Restart
sudo supervisorctl restart cv-parsing
```

### Backup

```bash
# Backup uploaded CVs
sudo tar -czf /backup/cv-uploads-$(date +%Y%m%d).tar.gz /var/uploads/cv-parsing

# Backup configuration
sudo tar -czf /backup/cv-parsing-config-$(date +%Y%m%d).tar.gz /opt/cv-parsing-service/.env
```

---

## 📞 Support

**Issues?** Check:
1. `sudo supervisorctl status cv-parsing`
2. `sudo tail -f /var/log/cv-parsing/error.log`
3. `curl -v https://your-domain.com/health`
4. Nginx config: `sudo nginx -t`

---

**Last updated:** 2026 | Version 1.0
