"""
CV Parsing Service
FastAPI application for parsing CVs with text extraction
"""
import os
import logging
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
import config
from app.utils.logger import setup_logger
from app.routes import cv

# Setup logging
setup_logger()
logger = logging.getLogger(__name__)

# Create FastAPI app
app = FastAPI(
    title="CV Parsing Service",
    description="Service for parsing CVs and extracting structured data",
    version="1.0.0"
)

# CORS middleware - restrict to configured origins
cors_origins = os.getenv('CORS_ORIGINS', 'http://localhost:8000,http://localhost:3000').split(',')
cors_origins = [origin.strip() for origin in cors_origins]  # Clean whitespace

app.add_middleware(
    CORSMiddleware,
    allow_origins=cors_origins,
    allow_credentials=True,
    allow_methods=["GET", "POST", "OPTIONS"],
    allow_headers=["*"],
)

# Include routes
app.include_router(cv.router, tags=["CV Parsing"])


@app.on_event("startup")
async def startup_event():
    """Initialize on startup"""
    logger.info("=== CV Parsing Service Starting ===")
    
    # Validate API Key in production
    if config.ENV == "production" and config.API_KEY == 'your-secret-key-here':
        logger.error("❌ CRITICAL: API_KEY not configured for production!")
        logger.error("Set API_KEY in .env.production before deploying")
        raise RuntimeError("API_KEY must be configured for production")
    
    logger.info(f"API Key configured: {config.API_KEY != 'your-secret-key-here'}")
    logger.info(f"Max file size: {config.MAX_FILE_SIZE_MB}MB")
    logger.info(f"Ollama: {config.OLLAMA_BASE_URL}")
    logger.info(f"Model: {config.OLLAMA_MODEL}")
    logger.info(f"CORS Origins: {cors_origins_URL}")
    logger.info(f"Model: {config.OLLAMA_MODEL}")


@app.on_event("shutdown")
async def shutdown_event():
    """Cleanup on shutdown"""
    logger.info("CV Parsing Service shutting down...")


@app.get("/")
async def root():
    """Root endpoint"""
    return {
        "service": "CV Parsing Service",
        "version": "1.0.0",
        "endpoints": {
            "health": "/health",
            "parse": "/api/parse-cv"
        }
    }


if __name__ == "__main__":
    import uvicorn
    
    logger.info(f"Starting server on {config.API_HOST}:{config.API_PORT}")
    
    uvicorn.run(
        "main:app",
        host=config.API_HOST,
        port=config.API_PORT,
        reload=(config.ENV == "development"),
        log_level=config.LOG_LEVEL.lower()
    )
