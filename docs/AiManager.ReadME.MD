AiManager – Die KI-Zentrale für dein Adminpanel
Mit dieser Entity verwaltest du jede Art von KI-Modell oder AI-Service in deinem System – egal ob LLM, TTS, Bild-KI, Trainer oder sonstwas.
Hier kannst du alle Infos, den Status, Healthchecks und Rollen zentral ablegen und später super flexibel auswerten.

Felder im Überblick
Feld	Typ	Beschreibung
id	int (auto)	Primärschlüssel
aiName	string (64)	Anzeigename der KI (z.B. "Jarvis", "VoodooVox")
aiModel	string (128), optional	Modell/Typ (z.B. "mistral", "xtts", "llama3")
aiSetup	text, optional	Setup-Infos: Python-Version, Requirements, Hardware etc.
aiPath	string (255), optional	Installationspfad, Start- oder Datenpfad
aiHost	string (128), optional	Hostname oder IP (wo läuft das Ding?)
aiDescription	text, optional	Freitextbeschreibung, Notizen etc.
aiCreated	datetime_immutable	Wann wurde die KI angelegt/registriert?
aiMaintainer	User (ManyToOne)	Zuständiger Admin/User für diese KI
aiMinrole	UserRole (ManyToOne)	Minimale User-Rolle für den Zugriff
aiAvatar	string (255), optional	Pfad zum Avatar-Icon der KI
aiStatus	string (32), Default active	Zustand: active, maintenance, offline, error
aiCategory	string (32)	Kategorie: z.B. llm, tts, trainer, image, ...
aiHealthUrl	string (255), optional	URL für Healthcheck (API oder Statusseite)
aiLastCheckedAt	datetime_immutable, optional	Letzter Healthcheck
aiApiToken	string (64), optional	API-Key/Token falls Zugriff geschützt ist
aiLastResponseMs	int, optional	Antwortzeit beim letzten Check (ms)
aiLastError	string (255), optional	Letzte Fehlernachricht (Healthcheck/API)

Einsatzmöglichkeiten
Live-Monitoring: Healthcheck-URL kann von einem Cronjob oder Service geprüft werden. Fehlertext und Latenz werden protokolliert.

Zugriffssteuerung: Per aiMinrole bestimmst du, wer welche KI sehen/benutzen darf.

Doku & Automatisierung: Pfade, Setups, Rollen und Tokens zentral pflegen – kein Zettelwirtschaft mehr.

Filter/Sortiermöglichkeiten: Nach Status, Kategorie, Maintainer sortieren oder suchen.

Beispiel: Neue KI eintragen
Im Admin-Panel auf „Neue KI anlegen“

Infos ausfüllen, Status und Kategorie wählen (z.B. „TTS“, „offline“)

Healthcheck-URL setzen, Token eintragen, Avatar auswählen

Maintainer & Minimalrolle zuweisen

Speichern – und du hast die KI komplett im System!

Noch mehr Möglichkeiten
Automatischer Healthcheck: Baue einen Symfony-Command, der alle Healthcheck-URLs abruft und die Felder updatet

Zukunftssicher: Mehr Felder? Kein Problem – du kannst beliebig erweitern

API-Integration: Die Tokens und URLs kannst du für automatisierte API-Calls nutzen

Kurze Zusammenfassung:
Mit AiManager baust du dir eine KI-Zentrale für alles was du in Zukunft brauchst. Egal ob du 3, 10 oder 100 Modelle hast:
Mit einer Tabelle, einem CRUD und einem kleinen Service hast du maximale Kontrolle und Übersicht.