from flask import Flask, request, jsonify
from flask_cors import CORS
from PIL import Image, ImageEnhance, ImageFilter, ImageOps
import io, base64
from pdf2image import convert_from_bytes
import pytesseract
import re

app = Flask(__name__)
CORS(app, origins=[
    "https://tekath.joormann-media.de",
    "http://localhost:8888",
    "http://127.0.0.1:8888"
])

def apply_preprocess(img):
    # 1. Auf Zielgröße bringen (z.B. 2x größer)
    new_size = (img.width * 2, img.height * 2)
    img = img.resize(new_size, Image.LANCZOS)
    # 2. Graustufen
    img = img.convert('L')
    # 3. Schärfen und Kontrast
    img = ImageOps.autocontrast(img)
    img = img.filter(ImageFilter.SHARPEN)
    img = ImageEnhance.Contrast(img).enhance(2.5)
    # 4. Binarisierung (Threshold)
    img = img.point(lambda x: 0 if x < 160 else 255, '1')
    return img

def auto_fix_text(text):
    """Auto-Korrektur für typische OCR-Fehler (deutsche Bürokratie-Edition)."""
    rules = [
        (r'\b0([A-Z])', r'O\1'),
        (r'\bO(\d)', r'0\1'),
        (r'\b1([A-Z])', r'I\1'),
        (r'\bI(\d)', r'1\1'),
        (r'\bB(\d)', r'8\1'),
        (r'\b8([A-Z])', r'B\1'),
    ]
    for pattern, repl in rules:
        text = re.sub(pattern, repl, text)
    return text

@app.route('/pdf2img', methods=['POST'])
def pdf2img():
    file = request.files.get('file')
    if not file:
        return jsonify({'error': 'No file uploaded'}), 400
    try:
        images = convert_from_bytes(file.read(), first_page=1, last_page=1, dpi=200)
        if not images:
            return jsonify({'error': 'Keine Bilder extrahierbar'}), 400
        buf = io.BytesIO()
        images[0].save(buf, format='PNG')
        img_base64 = "data:image/png;base64," + base64.b64encode(buf.getvalue()).decode('utf-8')
        return jsonify({'image': img_base64})
    except Exception as ex:
        return jsonify({'error': f"PDF Fehler: {ex}"}), 500

@app.route('/ocr', methods=['POST'])
def ocr():
    data = request.get_json()
    if not data or 'image' not in data or 'rect' not in data:
        return jsonify({'error': 'Fehlende Daten'}), 400
    try:
        imgdata = data['image'].split(',', 1)[-1]
        img = Image.open(io.BytesIO(base64.b64decode(imgdata)))
        x, y, w, h = map(int, data['rect'])
        crop = img.crop((x, y, x+w, y+h))
        crop = apply_preprocess(crop)
        # psm mitgeben, fallback auf 3
        psm = int(data.get('psm', 3))
        config = f'--psm {psm}'
        text = pytesseract.image_to_string(crop, lang='deu+eng', config=config)
        # Optional: Auto-Fix noch hier (Frontend macht das auch, aber doppelt hält besser)
        # autoFix kommt als String, Bool oder gar nicht, alles akzeptieren
        auto_fix = data.get('autoFix', True)
        if isinstance(auto_fix, str):
            auto_fix = auto_fix.lower() not in ['false', '0', 'no']
        if auto_fix:
            text = auto_fix_text(text)
        return jsonify({'text': text})
    except Exception as ex:
        return jsonify({'error': f"OCR Fehler: {ex}"}), 500

@app.route('/ocr/preview', methods=['POST'])
def ocr_preview():
    """Gibt das Preprocess-Bild zurück, wie Tesseract es sieht."""
    data = request.get_json()
    if not data or 'image' not in data or 'rect' not in data:
        return jsonify({'error': 'Fehlende Daten'}), 400
    try:
        imgdata = data['image'].split(',', 1)[-1]
        img = Image.open(io.BytesIO(base64.b64decode(imgdata)))
        x, y, w, h = map(int, data['rect'])
        crop = img.crop((x, y, x+w, y+h))
        crop = apply_preprocess(crop)
        buf = io.BytesIO()
        crop.save(buf, format='PNG')
        img_base64 = "data:image/png;base64," + base64.b64encode(buf.getvalue()).decode('utf-8')
        return jsonify({'preview': img_base64})
    except Exception as ex:
        return jsonify({'error': f"Preview Fehler: {ex}"}), 500

@app.route('/')
def index():
    return "OCR-API läuft!"

if __name__ == "__main__":
    app.run("0.0.0.0", 8888, debug=True)
