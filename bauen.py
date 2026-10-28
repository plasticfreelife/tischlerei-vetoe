"""Setzt die Seiten der Website zusammen.

Jede Datei in _vorlage/seiten/ enthält oben TITLE, DESC und NAV, dann eine Zeile mit ---
und darunter den Inhalt der Seite. Kopf und Fuß kommen für alle Seiten aus _vorlage/kopf.html
und _vorlage/fuss.html. Das Ergebnis landet in website/.

Aufruf: python3 bauen.py
"""
import pathlib
import re

WURZEL = pathlib.Path(__file__).parent
VORLAGE = WURZEL / "_vorlage"
ZIEL = WURZEL / "website"

kopf = (VORLAGE / "kopf.html").read_text(encoding="utf-8")
fuss = (VORLAGE / "fuss.html").read_text(encoding="utf-8")

for quelle in sorted((VORLAGE / "seiten").glob("*.html")):
    meta, inhalt = quelle.read_text(encoding="utf-8").split("\n---\n", 1)
    angaben = dict(zeile.split(": ", 1) for zeile in meta.strip().splitlines())
    seite = re.sub(r"<title>.*?</title>", lambda _: f"<title>{angaben['TITLE']}</title>", kopf)
    seite = re.sub(r'<meta name="description" content=".*?">',
                   lambda _: f'<meta name="description" content="{angaben["DESC"]}">', seite)
    seite = seite.replace(f'<a href="{angaben["NAV"]}">', f'<a href="{angaben["NAV"]}" aria-current="page">')
    (ZIEL / quelle.name).write_text(seite + "<main>\n" + inhalt + "\n</main>" + fuss, encoding="utf-8")
    print("gebaut:", quelle.name)
