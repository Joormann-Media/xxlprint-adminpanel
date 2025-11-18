#!/bin/bash
# Setze Pfad zum Projekt-Ordner (ggf. anpassen)
BASE_DIR="$(dirname "$0")"
cd "$BASE_DIR"

# Aktiviere venv
source venv/bin/activate

# Führe Python OCR Script aus, alle Parameter werden durchgereicht
python3 ocr_fahrzeugschein.py "$@"

# Deaktiviere venv automatisch
deactivate
