"""
File validators
"""
import os
import logging
import config

logger = logging.getLogger(__name__)


def validate_file(file_path: str) -> bool:
    """
    Validate uploaded file
    
    Args:
        file_path: Path to file
        
    Returns:
        True if valid, raises exception otherwise
    """
    # Check file exists
    if not os.path.exists(file_path):
        raise FileNotFoundError(f"File not found: {file_path}")
    
    # Check file size
    file_size = os.path.getsize(file_path)
    if file_size > config.MAX_FILE_SIZE_BYTES:
        raise ValueError(f"File too large: {file_size} > {config.MAX_FILE_SIZE_BYTES}")
    
    # Check extension
    _, ext = os.path.splitext(file_path)
    ext = ext.lstrip('.').lower()
    
    if ext not in config.ALLOWED_EXTENSIONS:
        raise ValueError(f"File type not allowed: {ext}")
    
    logger.info(f"File validation passed: {file_path} ({file_size} bytes)")
    return True
