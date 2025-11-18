#!/usr/bin/env python3
import requests
import json
import time

API_URL = "https://tekath.joormann-media.de/state/api/import"
GEOJSON_URL = "https://gist.githubusercontent.com/fegoa89/d33514a5e59eb5af812b909915bcb3da/raw/germany-states.geojson"

states = [
    {'code': 'BW', 'name': 'Baden-Württemberg', 'lat': 48.6616, 'lon': 9.3501},
    {'code': 'BY', 'name': 'Bayern', 'lat': 48.7904, 'lon': 11.4979},
    {'code': 'BE', 'name': 'Berlin', 'lat': 52.5200, 'lon': 13.4050},
    {'code': 'BB', 'name': 'Brandenburg', 'lat': 52.4125, 'lon': 12.5316},
    {'code': 'HB', 'name': 'Bremen', 'lat': 53.0793, 'lon': 8.8017},
    {'code': 'HH', 'name': 'Hamburg', 'lat': 53.5511, 'lon': 9.9937},
    {'code': 'HE', 'name': 'Hessen', 'lat': 50.6521, 'lon': 9.1624},
    {'code': 'MV', 'name': 'Mecklenburg-Vorpommern', 'lat': 53.6127, 'lon': 12.4296},
    {'code': 'NI', 'name': 'Niedersachsen', 'lat': 52.6367, 'lon': 9.8451},
    {'code': 'NW', 'name': 'Nordrhein-Westfalen', 'lat': 51.4332, 'lon': 7.6616},
    {'code': 'RP', 'name': 'Rheinland-Pfalz', 'lat': 50.1183, 'lon': 7.3087},
    {'code': 'SL', 'name': 'Saarland', 'lat': 49.3964, 'lon': 7.0220},
    {'code': 'SN', 'name': 'Sachsen', 'lat': 51.1045, 'lon': 13.2017},
    {'code': 'ST', 'name': 'Sachsen-Anhalt', 'lat': 51.9503, 'lon': 11.6923},
    {'code': 'SH', 'name': 'Schleswig-Holstein', 'lat': 54.2194, 'lon': 9.6961},
    {'code': 'TH', 'name': 'Thüringen', 'lat': 50.9011, 'lon': 11.0378},
]

print("Lade Bundesländer-GeoJSON...")
try:
    geojson = requests.get(GEOJSON_URL)
    geojson.raise_for_status()
    geojson = geojson.json()
except Exception as e:
    print("❌ Fehler beim Laden der GeoJSON-Daten:", e)
    exit(1)

bundesland_to_polygon = {}
for feature in geojson['features']:
    name = feature['properties'].get('name') or feature['properties'].get('NAME_1')
    if name:
        bundesland_to_polygon[name] = feature['geometry']

name_fallbacks = {
    "Mecklenburg-Vorpommern": ["Mecklenburg Vorpommern"],
    "Thüringen": ["Thueringen"],
    "Schleswig-Holstein": ["Schleswig Holstein"],
    "Baden-Württemberg": ["Baden Wuerttemberg", "Baden-Wuerttemberg", "Baden Württemberg"],
    "Rheinland-Pfalz": ["Rheinland Pfalz"],
    "Sachsen-Anhalt": ["Sachsen Anhalt"],
}

print("Starte Import für Bundesländer...")
for state in states:
    name = state['name']
    polygon = bundesland_to_polygon.get(name)

    # Fallbacks für Schreibweisen
    if not polygon:
        for alt in name_fallbacks.get(name, []):
            polygon = bundesland_to_polygon.get(alt)
            if polygon:
                break

    if not polygon:
        print(f"❌ Polygon NICHT gefunden für {name} – wird übersprungen!")
        continue

    state_data = dict(state)
    state_data['polygon'] = polygon

    try:
        resp = requests.post(API_URL, json=state_data, timeout=10)
        if resp.status_code in (200, 201):
            result = resp.json()
            print(f"✅ {name}: {result.get('status')} (ID={result.get('id')})")
        else:
            print(f"❌ Fehler für {name}: Status {resp.status_code} – {resp.text}")
    except Exception as e:
        print(f"❌ Fehler für {name}: {e}")

    time.sleep(0.3)  # Schon mal Server und API

print("Fertig.")
