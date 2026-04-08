"""
Gunicorn configuration for production
Run with: gunicorn --config gunicorn_config.py main:app
"""

import os
from multiprocessing import cpu_count

# Server socket
bind = "127.0.0.1:8001"  # Don't expose directly - use reverse proxy
backlog = 2048

# Worker processes
# Formula: (2 x num_cores) + 1
workers = (2 * cpu_count()) + 1
worker_class = "uvicorn.workers.UvicornWorker"
max_requests = 1000
max_requests_jitter = 100
timeout = 60
keepalive = 5

# Logging
accesslog = "/var/log/cv-parsing/access.log"
errorlog = "/var/log/cv-parsing/error.log"
loglevel = "warning"
access_log_format = '%(h)s %(l)s %(u)s %(t)s "%(r)s" %(s)s %(b)s "%(f)s" "%(a)s"'

# Server mechanics
daemon = False
pidfile = "/var/run/cv-parsing/gunicorn.pid"
umask = 0o022
user = None  # Set to 'www-data' if running as www-data
group = None

# SSL (if using SSL directly with Gunicorn)
# keyfile = "/etc/ssl/private/key.pem"
# certfile = "/etc/ssl/certs/cert.pem"
# ssl_version = "TLSv1_2"

# Server hooks
def on_starting(server):
    print(f"✅ CV Parsing Service starting with {workers} workers")

def on_exit(server):
    print("⛔ CV Parsing Service stopped")
