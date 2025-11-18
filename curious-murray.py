#!/usr/bin/env python3
import os
import sys
import argparse
from datetime import datetime
from collections import defaultdict

def get_path_from_args():
    parser = argparse.ArgumentParser(description="Curious Murray – Der Datenkrake mit Rum-Problem!")
    parser.add_argument('PATH', nargs='?', type=str, help='Pfad zum Durchforsten')
    args = parser.parse_args()
    if args.PATH:
        return args.PATH
    return input("Arrr, gib den Pfad zum Scannen ein: ").strip()

def format_size(size):
    for unit in ['B','KB','MB','GB','TB']:
        if size < 1024.0:
            return "%3.1f %s" % (size, unit)
        size /= 1024.0

def file_line_count(filepath):
    try:
        with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
            count = 0
            for _ in f:
                count += 1
            return count
    except Exception:
        # Binärdatei oder nicht lesbar
        return 0

def main():
    path = get_path_from_args()
    if not os.path.isdir(path):
        print("Murray: ARR! Der Pfad sieht nach vergammeltem Seetang aus. Versuch's nochmal.")
        sys.exit(1)

    total_dirs = 0
    total_files = 0
    ext_counter = defaultdict(int)
    oldest = None
    newest = None
    oldest_info = {}
    newest_info = {}
    total_lines = 0

    for root, dirs, files in os.walk(path):
        total_dirs += len(dirs)
        for file in files:
            total_files += 1
            fp = os.path.join(root, file)
            try:
                stat = os.stat(fp)
            except Exception:
                continue

            ext = os.path.splitext(file)[1].lower() or '[ohne]'
            ext_counter[ext] += 1

            # Achtung: st_ctime = "Change time" auf Linux, "Creation time" auf Windows!
            # Für "letzte Änderung" -> st_mtime
            creation_time = stat.st_ctime
            mod_time = stat.st_mtime

            if (oldest is None) or (creation_time < oldest):
                oldest = creation_time
                oldest_info = {
                    'name': fp,
                    'date': datetime.fromtimestamp(creation_time),
                    'size': stat.st_size
                }
            if (newest is None) or (creation_time > newest):
                newest = creation_time
                newest_info = {
                    'name': fp,
                    'date': datetime.fromtimestamp(creation_time),
                    'size': stat.st_size
                }

            lines = file_line_count(fp)
            total_lines += lines

    print(f"\n🐙 Curious Murray's Datenkraken-Report 🐙\n")
    print(f"Ordner gesamt: {total_dirs}")
    print(f"Dateien gesamt: {total_files}\n")

    print("Dateitypen (sortiert nach Anzahl):")
    for ext, count in sorted(ext_counter.items(), key=lambda x: x[1], reverse=True):
        print(f"  {ext:>8} : {count}")

    print("\nÄlteste Datei (laut st_ctime! Auf Linux = Change Time):")
    print(f"  {oldest_info.get('date')} | {format_size(oldest_info.get('size', 0))} | {oldest_info.get('name')}")

    print("Jüngste Datei (laut st_ctime!):")
    print(f"  {newest_info.get('date')} | {format_size(newest_info.get('size', 0))} | {newest_info.get('name')}")

    print(f"\nGesamtzahl aller Zeilen (soweit Murray’s Tentakel lesen konnten): {total_lines}")
    print("\nArrr, fertig! Falls du was vergessen hast, blame den Papagei, nicht mich!\n")

if __name__ == "__main__":
    main()

