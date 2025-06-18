#!/bin/bash

# Download required models if they don't exist
echo "Checking for required AI models..."

# Create models directory if it doesn't exist
mkdir -p /app/models

# Download Whisper model for transcription
python -c "import whisper; whisper.load_model('base')" || echo "Whisper model download failed"

# Download spaCy model for NLP
python -m spacy download es_core_news_sm || echo "SpaCy Spanish model download failed"

# Start the Flask application with Gunicorn
echo "Starting AI processing service..."
exec gunicorn --bind 0.0.0.0:8000 --workers 2 --timeout 300 --max-requests 1000 --preload app:app