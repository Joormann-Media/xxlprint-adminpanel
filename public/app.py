from flask import Flask, request, jsonify
from PIL import Image
import pytesseract
import io
import base64
from pdf2image import convert_from_bytes

app = Flask(__name__)

@app.route('/ocr', methods=['POST'])
def ocr_area():
    # Receive base64 image and coordinates
    data = request.json
    img_data = data['image'].split(',')[1]  # base64 after comma
    x, y, w, h = data['rect']

    img_bytes = base64.b64decode(img_data)
    img = Image.open(io.BytesIO(img_bytes))

    # Crop to the selection
    crop = img.crop((x, y, x+w, y+h))
    text = pytesseract.image_to_string(crop, lang='deu+eng')

    return jsonify({'text': text.strip()})

@app.route('/pdf2img', methods=['POST'])
def pdf_to_img():
    file = request.files['file']
    pages = convert_from_bytes(file.read())
    # Take first page for preview
    buffered = io.BytesIO()
    pages[0].save(buffered, format="PNG")
    img_str = base64.b64encode(buffered.getvalue()).decode()
    return jsonify({'image': f'data:image/png;base64,{img_str}'})

if __name__ == "__main__":
    app.run(port=8888, debug=True)

