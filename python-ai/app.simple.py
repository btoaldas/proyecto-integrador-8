from flask import Flask, request, jsonify
from flask_cors import CORS
import logging
from datetime import datetime

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = Flask(__name__)
CORS(app)

@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    return jsonify({
        'status': 'healthy',
        'timestamp': datetime.now().isoformat(),
        'service': 'python-ai-simple'
    })

@app.route('/transcribe', methods=['POST'])
def transcribe_audio():
    """Mock transcription service"""
    try:
        if 'audio' not in request.files:
            return jsonify({'error': 'No audio file provided'}), 400
        
        audio_file = request.files['audio']
        if audio_file.filename == '':
            return jsonify({'error': 'No file selected'}), 400
        
        # Mock transcription result
        return jsonify({
            'text': 'Transcripción simulada del archivo de audio',
            'language': 'es',
            'confidence': 0.95,
            'duration': 30.5
        })
    
    except Exception as e:
        logger.error(f"Transcription error: {e}")
        return jsonify({'error': str(e)}), 500

@app.route('/generate-document', methods=['POST'])
def generate_document():
    """Generate document from text"""
    try:
        data = request.get_json()
        if not data or 'text' not in data:
            return jsonify({'error': 'No text provided'}), 400
        
        text = data['text']
        document_type = data.get('type', 'acta')
        
        # Basic document formatting
        formatted_text = format_document(text, document_type)
        
        return jsonify({
            'formatted_text': formatted_text,
            'document_type': document_type,
            'generated_at': datetime.now().isoformat()
        })
    
    except Exception as e:
        logger.error(f"Document generation error: {e}")
        return jsonify({'error': str(e)}), 500

def format_document(text, doc_type):
    """Format text as document"""
    if doc_type == 'acta':
        header = f"""
ACTA DE SESIÓN MUNICIPAL
Fecha: {datetime.now().strftime('%d de %B de %Y')}
Hora: {datetime.now().strftime('%H:%M')}

DESARROLLO DE LA SESIÓN:
        """
        
        body = f"1. {text}"
        
        footer = """

Sin más asuntos que tratar, se da por finalizada la sesión.

FIRMAS:
_____________________
Secretario Municipal
        """
        
        return header + body + footer
    
    return text

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8000, debug=True)