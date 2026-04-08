"""
Gunicorn configuration for production
Run with: gunicorn --config gunicorn_config.py main:app
"""

import os
from multiprocessing import cpu_count

# Resolve paths relative to this config file so it works without root access
_BASE = os.path.dirname(os.path.abspath(__file__))
_LOGS = os.path.join(_BASE, 'logs')
os.makedirs(_LOGS, exist_ok=True)

# Server socket
bind = "127.0.0.1:8001"  # Don't expose directly - use reverse proxy
backlog = 2048

# Worker processes
workers = min((2 * cpu_count()) + 1, 4)
worker_class = "uvicorn.workers.UvicornWorker"
max_requests = 1000
max_requests_jitter = 100
timeout = 120
keepalive = 5

# Logging (write inside project directory — no root needed)
accesslog = os.path.join(_LOGS, 'access.log')
errorlog  = os.path.join(_LOGS, 'error.log')
loglevel  = "warning"
access_log_format = '%(h)s %(l)s %(u)s %(t)s "%(r)s" %(s)s %(b)s "%(f)s" "%(a)s"'

# Server mechanics
daemon = False
pidfile = os.path.join(_BASE, 'gunicorn.pid')
def on_starting(server):
    print(f"✅ CV Parsing Service starting with {workers} workers")

def on_exit(server):
    print("⛔ CV Parsing Service stopped")
