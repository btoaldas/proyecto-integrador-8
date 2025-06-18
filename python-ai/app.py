from flask import Flask, request, jsonify
from flask_cors import CORS
import whisper
import os
import tempfile
import logging
from datetime import datetime

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = Flask(__name__)
CORS(app)

# Global variables for models
whisper_model = None

def load_models():
    """Load AI models on startup"""
    global whisper_model
    try:
        logger.info("Loading Whisper model...")
        whisper_model = whisper.load_model("base")
        logger.info("Models loaded successfully")
    except Exception as e:
        logger.error(f"Error loading models: {e}")

@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    return jsonify({
        'status': 'healthy',
        'timestamp': datetime.now().isoformat(),
        'models_loaded': whisper_model is not None
    })

@app.route('/transcribe', methods=['POST'])
def transcribe_audio():
    """Transcribe audio file to text"""
    try:
        if 'audio' not in request.files:
            return jsonify({'error': 'No audio file provided'}), 400
        
        audio_file = request.files['audio']
        if audio_file.filename == '':
            return jsonify({'error': 'No file selected'}), 400
        
        # Save uploaded file temporarily
        with tempfile.NamedTemporaryFile(delete=False, suffix='.wav') as tmp_file:
            audio_file.save(tmp_file.name)
            
            # Transcribe audio
            result = whisper_model.transcribe(tmp_file.name, language='es')
            
            # Clean up temporary file
            os.unlink(tmp_file.name)
            
            return jsonify({
                'text': result['text'],
                'language': result['language'],
                'segments': result['segments']
            })
    
    except Exception as e:
        logger.error(f"Transcription error: {e}")
        return jsonify({'error': str(e)}), 500

@app.route('/generate-document', methods=['POST'])
def generate_document():
    """Generate document from transcribed text"""
    try:
        data = request.get_json()
        if not data or 'text' not in data:
            return jsonify({'error': 'No text provided'}), 400
        
        text = data['text']
        document_type = data.get('type', 'acta')
        
        # Basic document generation (can be enhanced with more AI)
        if document_type == 'acta':
            formatted_text = format_acta(text)
        else:
            formatted_text = text
        
        return jsonify({
            'formatted_text': formatted_text,
            'document_type': document_type,
            'generated_at': datetime.now().isoformat()
        })
    
    except Exception as e:
        logger.error(f"Document generation error: {e}")
        return jsonify({'error': str(e)}), 500

def format_acta(text):
    """Format text as municipal acta"""
    # Basic formatting - can be enhanced with AI
    header = f"""
ACTA DE SESIÓN
Fecha: {datetime.now().strftime('%d de %B de %Y')}
    
DESARROLLO DE LA SESIÓN:
    """
    
    # Basic text processing
    paragraphs = text.split('.')
    formatted_paragraphs = []
    
    for i, paragraph in enumerate(paragraphs, 1):
        if paragraph.strip():
            formatted_paragraphs.append(f"{i}. {paragraph.strip()}.")
    
    body = '\n'.join(formatted_paragraphs)
    
    footer = """
    
Sin más asuntos que tratar, se da por finalizada la sesión.
    """
    
    return header + body + footer

if __name__ == '__main__':
    load_models()
    app.run(host='0.0.0.0', port=8000, debug=False)