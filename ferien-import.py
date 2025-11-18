#!/usr/bin/env python3
import csv
import requests

# URL der Ferien-CSV (2025)
CSV_URL = "https://www.schulferien.org/download/deutschland/ferien_deutschland_2025.csv"

# Dein Import-API-Endpunkt
API_URL = "https://tekath.joormann-media.de/holiday/api/import"

# Timeout für Requests (Sekunden)
TIMEOUT = 10

def holiday_exists(state_code, name, start_date, end_date):
    """
    Prüft, ob ein Ferien-Eintrag schon existiert.
    Diese Funktion erwartet, dass deine API eine GET-Route hat, 
    die man mit Query-Params anfragen kann (muss ggf. in Symfony gebaut werden).
    Alternativ: Einfach alle laden und im Script filtern (nicht optimal).
    """
    params = {
        'state': state_code,
        'name': name,
        'startDate': start_date,
        'endDate': end_date,
    }
    try:
        resp = requests.get(API_URL, params=params, timeout=TIMEOUT)
        if resp.status_code == 200:
            data = resp.json()
            return len(data) > 0  # Rückgabe je nachdem wie API antwortet
        else:
            print(f"Warnung: Prüfen auf Existenz fehlgeschlagen: {resp.status_code} {resp.text}")
            return False
    except Exception as e:
        print(f"Fehler bei Existenz-Check: {e}")
        return False

def import_holiday(state_code, name, start_date, end_date, holiday_type='school_vacation', comment='Autoimport CSV'):
    """
    Führt den Import per POST an deiner API durch.
    """
    payload = {
        'state': state_code,
        'name': name,
        'startDate': start_date,
        'endDate': end_date,
        'type': holiday_type,
        'comment': comment,
    }
    try:
        resp = requests.post(API_URL, json=payload, timeout=TIMEOUT)
        if resp.status_code in (200, 201):
            print(f"✅ Importiert: {name} ({state_code}) {start_date} - {end_date}")
            return True
        else:
            print(f"❌ Fehler beim Import von {name}: {resp.status_code} {resp.text}")
            return False
    except Exception as e:
        print(f"Fehler beim Import von {name}: {e}")
        return False

def main():
    print("Lade CSV Schulferien...")
    try:
        response = requests.get(CSV_URL, timeout=TIMEOUT)
        response.raise_for_status()
    except Exception as e:
        print(f"Fehler beim Laden der CSV: {e}")
        return

    lines = response.text.splitlines()
    reader = csv.DictReader(lines, delimiter=';')

    imported = 0
    skipped = 0
    errors = 0

    for row in reader:
        state = row.get("Bundesland", "").strip()
        name = row.get("Ferienart", "").strip()
        start = row.get("Start", "").strip()
        end = row.get("Ende", "").strip()

        if not (state and name and start and end):
            print(f"Überspringe unvollständige Zeile: {row}")
            errors += 1
            continue

        # Check doubletten via API
        if holiday_exists(state, name, start, end):
            print(f"⚠️ Übersprungen (existiert): {name} ({state}) {start} - {end}")
            skipped += 1
            continue

        if import_holiday(state, name, start, end):
            imported += 1
        else:
            errors += 1

    print(f"\nFertig! Importiert: {imported}, Übersprungen: {skipped}, Fehler: {errors}")

if __name__ == "__main__":
    main()
