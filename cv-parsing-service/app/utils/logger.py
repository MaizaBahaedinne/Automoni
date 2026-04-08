"""
Logging configuration
"""
import os
import logging
import config

def setup_logger():
    """Configure application logging"""
    
    # Create logger
    logger = logging.getLogger()
    logger.setLevel(getattr(logging, config.LOG_LEVEL))
    
    # Create formatters
    formatter = logging.Formatter(
        '%(asctime)s - %(name)s - %(levelname)s - %(message)s'
    )
    
    # File handler — skip gracefully if the path isn't writable
    try:
        log_dir = os.path.dirname(config.LOG_FILE)
        if log_dir:
            os.makedirs(log_dir, exist_ok=True)
        file_handler = logging.FileHandler(config.LOG_FILE)
        file_handler.setFormatter(formatter)
        logger.addHandler(file_handler)
    except (PermissionError, OSError) as e:
        print(f'[logger] Cannot write log file ({config.LOG_FILE}): {e} — logging to stdout only')

    # Console handler
    console_handler = logging.StreamHandler()
    console_handler.setFormatter(formatter)
    logger.addHandler(console_handler)
    
    return logger
