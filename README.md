# Website Tischlerei Christian Vetö

Die Website von [tischler-vetoe.at](https://www.tischler-vetoe.at): reine HTML-Dateien, ein CSS, ein kleines JavaScript und ein PHP-Skript für das Anfrageformular. Kein Baukasten, keine Datenbank.

## Ordner

| Ordner / Datei | Inhalt |
| --- | --- |
| `website/` | Alles, was auf den Server kommt. Nur dieser Ordner wird hochgeladen. |
| `website/assets/` | Stylesheet, Skript, Schriften (lokal, ohne Google), Logo und Fotos |
| `website/anfrage.php` | Schickt das Anfrageformular samt Anhängen an office@tischler-vetoe.at |
| `_vorlage/seiten/` | Der Inhalt jeder Seite. Hier werden Texte geändert. |
| `_vorlage/kopf.html`, `_vorlage/fuss.html` | Kopf mit Menü und Fuß, für alle Seiten gleich |
| `bauen.py` | Setzt aus `_vorlage/` die fertigen Seiten in `website/` zusammen |

## Texte oder Seiten ändern

1. Die Seite in `_vorlage/seiten/` ändern (bei Menü oder Fuß: `_vorlage/kopf.html` bzw. `fuss.html`).
2. `python3 bauen.py` ausführen.
3. Änderungen committen und auf `main` pushen. Der Upload zu ALL-INKL läuft dann von selbst.

Fotos liegen in `website/assets/img/`. Neue Fotos dort ablegen und in der Seite verlinken, am besten als JPG mit höchstens 1800 Pixel an der langen Seite.

## Automatischer Upload zu ALL-INKL einrichten (einmalig)

1. Im KAS von ALL-INKL einen FTP-Zugang anlegen, der auf den Ordner der Domain tischler-vetoe.at zeigt.
2. Im GitHub-Repository unter **Settings → Secrets and variables → Actions** drei Secrets anlegen:
   - `FTP_SERVER`: der FTP-Server aus dem KAS (z. B. `w0123456.kasserver.com`)
   - `FTP_USERNAME`: der FTP-Benutzer
   - `FTP_PASSWORD`: das Passwort
3. Falls der FTP-Zugang nicht direkt im Domain-Ordner landet: unter **Variables** `FTP_ZIELORDNER` anlegen, z. B. `/tischler-vetoe.at/`.
4. Unter **Actions** den Ablauf „Website zu ALL-INKL hochladen“ einmal von Hand starten.

Die Zugangsdaten gehören nur in die GitHub-Secrets, nie in eine Datei im Repository.

## Anfrageformular

`anfrage.php` braucht PHP 8.1 oder neuer (im KAS unter den Domain-Einstellungen wählbar). Die Absender-Adresse `office@tischler-vetoe.at` muss als Postfach bei ALL-INKL bestehen. Erlaubt sind bis zu 5 Dateien mit je 10 MB (PDF, Bilder, DWG, DXF, Word, Excel). Auf dem Server wird nichts gespeichert. Ohne PHP, etwa in einer Vorschau, bereitet das Formular stattdessen eine E-Mail vor.

## Umzug von Wix

Die Domain zeigt erst auf ALL-INKL, wenn die DNS-Einträge bei Wix bzw. beim Domain-Anbieter umgestellt sind. Vorher die Website über die vorläufige ALL-INKL-Adresse prüfen.
