import sys
import pytesseract
from PIL import Image
import os

def pdf_to_images(pdf_path):
    from pdf2image import convert_from_path
    return convert_from_path(pdf_path)

def ocr_image(image):
    return pytesseract.image_to_string(image, lang='deu')

def main(file_path):
    ext = os.path.splitext(file_path)[1].lower()
    images = []
    if ext == '.pdf':
        images = pdf_to_images(file_path)
    elif ext in ['.jpg', '.jpeg', '.png']:
        images = [Image.open(file_path)]
    else:
        print('Nicht unterstütztes Format:', ext)
        return

    text_result = ''
    for i, img in enumerate(images):
        text = ocr_image(img)
        text_result += text + '\n'
        print(f"--- Seite {i+1} ---\n{text}")

    # Text auch als Datei speichern
    out_file = file_path + ".txt"
    with open(out_file, "w", encoding="utf-8") as f:
        f.write(text_result)
    print(f"\nFertig! Ergebnis als Text in: {out_file}")

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Aufruf: python3 ocr_fahrzeugschein.py <datei.pdf|jpg|png>")
    else:
        main(sys.argv[1])
