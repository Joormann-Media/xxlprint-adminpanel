#!/usr/bin/env python3
import os
import sys
import shutil
import datetime
import subprocess

# Self-check Funktionen ---------------------
# (wie gehabt, keine Änderung...)

def check_python_version():
    if sys.version_info < (3, 7):
        print("❌ Python 3.7 oder höher wird benötigt. Script beendet.")
        sys.exit(1)

def check_inquirerpy():
    try:
        import InquirerPy
        return True
    except ImportError:
        return False

def check_php():
    try:
        result = subprocess.run(["php", "-v"], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        return result.returncode == 0
    except Exception:
        return False

def pip_install(pkg):
    print(f"Installiere {pkg} ...")
    result = subprocess.call([sys.executable, "-m", "pip", "install", pkg])
    return result == 0

def self_restart():
    print("Starte Script neu ...\n")
    python = sys.executable
    os.execl(python, python, *sys.argv)

def self_check():
    check_python_version()
    must_restart = False

    if not check_inquirerpy():
        print("⚠️  Für Cursormenüs wird das Package 'InquirerPy' benötigt.")
        res = input("Jetzt installieren? [j/N/X]: ").strip().lower()
        if res == "j":
            pip_install("InquirerPy")
            must_restart = True
        elif res == "x":
            print("Abgebrochen.")
            sys.exit(0)
        else:
            print("Weiter ohne Cursor-Menü.")

    if not check_php():
        print("❌ PHP ist nicht installiert oder nicht im Pfad.")
        print("Dieses Script benötigt PHP für die Migrationen.")
        print("Installiere PHP bitte manuell und starte das Script erneut.")
        sys.exit(1)

    if must_restart:
        self_restart()

# Projektwahl-Funktion ----------------------

def choose_project_root():
    ROOT = "/var/www"
    if not os.path.exists(ROOT):
        print(f"❌ Basisverzeichnis {ROOT} existiert nicht!")
        sys.exit(1)
    proj_dirs = [f for f in os.listdir(ROOT) if os.path.isdir(os.path.join(ROOT, f))]
    if not proj_dirs:
        print("❌ Keine Projekte in /var/www gefunden!")
        sys.exit(1)
    try:
        from InquirerPy import inquirer
        CURSOR_MENU = True
    except ImportError:
        CURSOR_MENU = False
    if CURSOR_MENU:
        choices = [{"name": d, "value": d} for d in proj_dirs]
        choices.append({"name": "❌ Abbrechen", "value": "X"})
        selected = inquirer.select(
            message="Projekt auswählen (/var/www/*):",
            choices=choices
        ).execute()
        if selected == "X":
            print("Abgebrochen.")
            sys.exit(0)
    else:
        print("\nVerfügbare Projekte in /var/www:")
        for idx, proj in enumerate(proj_dirs):
            print(f"{idx+1}: {proj}")
        print("X: Abbrechen")
        n = input("Nummer eingeben: ")
        if n.strip().lower() == "x":
            print("Abgebrochen.")
            sys.exit(0)
        try:
            selected = proj_dirs[int(n)-1]
        except:
            print("Ungültige Eingabe. Abbruch.")
            sys.exit(1)
    return os.path.join(ROOT, selected)

# Script-Konstanten (werden zur Laufzeit gesetzt!) -----

BASE_DIR = None
SRC_ENTITY = None
BACKUP_ROOT = None

def find_entities():
    entities = []
    for file in os.listdir(SRC_ENTITY):
        if file.endswith(".php") and not file.startswith('.'):
            entities.append(file.replace(".php", ""))
    return sorted(entities)

def select_entity(entities):
    try:
        from InquirerPy import inquirer
        CURSOR_MENU = True
    except ImportError:
        CURSOR_MENU = False
    if CURSOR_MENU:
        choices = [{"name": e, "value": e} for e in entities]
        choices.append({"name": "❌ Abbrechen", "value": "X"})
        entity = inquirer.select(
            message="Wähle die zu löschende Entity:",
            choices=choices
        ).execute()
        if entity == "X":
            print("Abgebrochen.")
            sys.exit(0)
    else:
        print("\nVerfügbare Entities:")
        for idx, entity in enumerate(entities):
            print(f"{idx+1}: {entity}")
        print("X: Abbrechen")
        n = input("Nummer eingeben: ")
        if n.strip().lower() == "x":
            print("Abgebrochen.")
            sys.exit(0)
        try:
            entity = entities[int(n)-1]
        except:
            print("Ungültige Eingabe. Abbruch.")
            sys.exit(1)
    return entity

def collect_paths(entity):
    paths = [
        f"src/Entity/{entity}.php",
        f"src/Repository/{entity}Repository.php",
        f"src/Controller/{entity}Controller.php",
        f"src/Form/{entity}Type.php",
        f"templates/{entity.lower()}",
    ]
    found = []
    for rel in paths:
        full = os.path.join(BASE_DIR, rel)
        if os.path.exists(full):
            found.append((rel, full, os.path.isdir(full)))
    return found

def show_found(found):
    print("\nGefundene Dateien/Ordner:")
    for rel, full, is_dir in found:
        print(f"{'[📁]' if is_dir else '[📄]'} {rel}")
    if not found:
        print("Keine Dateien gefunden!")

def confirm_move():
    res = input("Verschieben und sichern? [j/N/X]: ").strip().lower()
    if res == "x":
        print("Abgebrochen.")
        sys.exit(0)
    return res == "j"

def backup_and_move(entity, found):
    timestamp = datetime.datetime.now().strftime('%Y%m%d_%H%M%S')
    backup_dir = os.path.join(BACKUP_ROOT, entity, timestamp)
    os.makedirs(backup_dir, exist_ok=True)
    for rel, full, is_dir in found:
        dest = os.path.join(backup_dir, os.path.basename(full))
        shutil.move(full, dest)
    print(f"\n✅ Alles verschoben nach: {backup_dir}\n")
    return backup_dir

def run_migrations():
    print("\nMigration erstellen? (php bin/console make:migration)")
    res = input("[j/N/X]: ").strip().lower()
    if res == "x":
        print("Abgebrochen.")
        sys.exit(0)
    if res == "j":
        subprocess.call(["php", "bin/console", "make:migration"], cwd=BASE_DIR)
    print("\nMigration ausführen? (php bin/console doctrine:migrations:migrate)")
    res = input("[j/N/X]: ").strip().lower()
    if res == "x":
        print("Abgebrochen.")
        sys.exit(0)
    if res == "j":
        subprocess.call(["php", "bin/console", "doctrine:migrations:migrate", "--no-interaction"], cwd=BASE_DIR)

def clear_cache():
    print("\nCache leeren ... (php bin/console cache:clear)")
    subprocess.call(["php", "bin/console", "cache:clear"], cwd=BASE_DIR)

def main():
    print("🔪 Entity-Cleaner (Python Edition, Multi-Projekt)\n")
    self_check()
    global BASE_DIR, SRC_ENTITY, BACKUP_ROOT
    BASE_DIR = choose_project_root()
    SRC_ENTITY = os.path.join(BASE_DIR, "src/Entity")
    BACKUP_ROOT = os.path.join(BASE_DIR, "cleanerbackup")

    if not os.path.isdir(SRC_ENTITY):
        print(f"❌ src/Entity wurde im Projekt {BASE_DIR} nicht gefunden.")
        sys.exit(1)

    entities = find_entities()
    if not entities:
        print("Keine Entities gefunden.")
        return
    entity = select_entity(entities)
    found = collect_paths(entity)
    show_found(found)
    if not found:
        print("Nichts zu tun.")
        return
    if not confirm_move():
        print("Abgebrochen.")
        return
    backup_dir = backup_and_move(entity, found)
    run_migrations()
    clear_cache()
    print("\nFertig. Backup liegt unter:", backup_dir)

if __name__ == "__main__":
    main()
