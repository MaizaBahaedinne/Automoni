#!/bin/bash
# Quick start script for CV Parsing Service

set -e

echo "🚀 CV Parsing Service - Quick Start"
echo "===================================="
echo ""

# Check Python
echo "1️⃣  Checking Python..."
if ! command -v python3 &> /dev/null; then
    echo "❌ Python3 not found. Please install Python 3.8+"
    exit 1
fi
python3 --version

# Check .env file
echo ""
echo "2️⃣  Checking environment..."
if [ ! -f .env ]; then
    echo "⚠️  No .env file found. Creating from template..."
    cp .env.example .env
    echo "✅ Created .env file (update API_KEY if needed)"
fi

# Install dependencies
echo ""
echo "3️⃣  Installing Python dependencies..."
pip3 install -r requirements.txt > /dev/null
echo "✅ Dependencies installed"

# Check Tesseract
echo ""
echo "4️⃣  Checking Tesseract OCR..."
if command -v tesseract &> /dev/null; then
    tesseract --version | head -1
    echo "✅ Tesseract found"
else
    echo "⚠️  Tesseract not found (needed for image parsing):"
    echo "   macOS: brew install tesseract"
    echo "   Ubuntu: sudo apt-get install tesseract-ocr"
fi

# Ready to start
echo ""
echo "===================================="
echo "✅ Setup complete!"
echo ""
echo "To start the service, run:"
echo "  python3 main.py"
echo ""
echo "Service will run on: http://localhost:8001"
echo "API Key needed: See .env file"
echo ""
