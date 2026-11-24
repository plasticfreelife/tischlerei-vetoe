# Website Tischlerei Christian Vetö – Übergabe

Diese Datei liest jede neue Claude-Sitzung zuerst. Sie hält fest, was entschieden ist, damit nichts neu verhandelt wird.
Sprache mit dem Nutzer: Deutsch. Kundinnen und Kunden werden auf der Website gesiezt.

## Ziel

Neubau der Website von tischler-vetoe.at, weg von Wix, hin zu ALL-INKL als reine HTML-Dateien plus `anfrage.php`.
Positionierung: Bespoke Furniture, Möbel nach Maß in Wien, für Kundinnen und Kunden, die maßgefertigte Lösungen fürs Zuhause suchen.
Referenzen für die Stimmung: ateliermateus.com, seliger.at/en/city-workshop, tischlereiwassak.at (Wassak ist ein Freund: nur Stimmung, nichts kopieren).

## Aufbau und Arbeitsweise

- Texte und Inhalte werden in `_vorlage/seiten/*.html` geändert, Menü und Fuß in `_vorlage/kopf.html` und `_vorlage/fuss.html`. Danach `python3 bauen.py`, das schreibt `website/`.
- Nie `website/*.html` direkt bearbeiten, sonst überschreibt der nächste Bau die Änderung.
- Push auf `main` startet `.github/workflows/hochladen.yml` (FTPS zu ALL-INKL). Die Secrets `FTP_SERVER`, `FTP_USERNAME` und `FTP_PASSWORD` trägt der Nutzer selbst in GitHub ein. Passwörter nie im Chat erfragen.
- Vorschau als Artefakt: https://claude.ai/artifact/YCWzbpV4BdW3fFmifSdqdJ (Root `website/`, mit `url` aktualisieren, nicht neu anlegen).

## Inhaltliche Entscheidungen

- Reihenfolge der Leistungen überall: Einbauschränke und Nischen, Vorzimmer und Garderoben, Innenausbau, Plattformbetten und Schlafzimmer, Badmöbel, Küchen, Wohnmöbel und Einzelstücke.
- Vorzimmer ist ein häufiger Auftrag und gehört überall dazu.
- Reparatur, Restaurierung, Altbautüren und Kastenfenster stehen nur klein unter „Auf Anfrage“. Türen, Fenster, Couchtisch, Terrasse und Stockfotos der alten Seite werden nicht gezeigt.
- Hochbetten ausschließlich über die eigene Marke Horst (horsthochbetten.at): eigener Menüpunkt, eigener Abschnitt, Hinweis im Formular.
- Kernstärke: Kanten und Ecken, millimetergenaue Arbeit. Den Absatz „Genau dort, wo man hinsieht. Gezinkte Laden statt Klammern …“ hat der Nutzer ausdrücklich gelobt, er bleibt.
- Werkstatt: Die Firma ist in der Kleinhausgasse 10/2, 1210 Wien gemeldet, dort ist aber keine Werkstatt. Gearbeitet wird derzeit eingemietet im 2. Bezirk, ein Umzug ist geplant. Deshalb nie „Floridsdorf“ und nie „eigene Werkstatt“ schreiben, nur „Werkstatt in Wien“. „Meisterbetrieb seit 2021 · Kleinhausgasse 10, 1210 Wien“ ist in Ordnung.
- Team: Christian Vetö (Tischlermeister, Inhaber) und Merle (Lehrling seit 2026). Anna ist Partnerin, keine Angestellte, und kommt NICHT auf die Website. Ebenso keine Freunde, die bei Montagen helfen. Nicht schreiben, dass immer dieselbe Person misst und montiert: der Betrieb soll wachsen.
- Das Sideboard in Nuss ist Christians Meisterstück. Es steht als „Meisterstück“ bei den Projekten, nicht als großes Bild oben auf der Startseite.
- Das dunkle Möbel mit Messingknöpfen (`kueche-dunkel.jpg`) ist eine Küche. Der Hochschrank mit gezinkten Laden steht in einem WC, gehört also zu Badmöbel.
- Plattformbett mit Stufen: echtes Projekt, Lieferung Ende November 2026. Bis die echten Fotos kommen, sind die SketchUp-Entwürfe Platzhalter.
- Kein Konfigurator und kein Preisrechner, das widerspricht Bespoke. Im Formular ist „Was soll entstehen?“ ein freies Textfeld. Der Budgetrahmen bleibt freiwillig, vorausgewählt ist „Lieber im Gespräch klären“.
- Logo: den Baum behalten, einfarbig. Unter dem Schriftzug steht „Tischlerei · Möbel nach Maß · Wien“, nicht „Dein Tischlermeister“. Instagram bleibt @dein.tischlermeister.

## Gestaltung

- Farben: Grund wie heller Putz (#efe8dd), Schrift dunkles Nuss (#2a211b), das Grün aus dem Logo als Akzent und für Farbflächen (#184420, hell #e3ecd9). Der Fuß ist nussbraun (#3a2a20), das gefällt dem Nutzer sehr. Braun zusammen mit Eichengelb als Fläche gefällt ihm NICHT. Eichengelb nur für die Textmarkierung (die mag er).
- Schriften lokal in `website/assets/fonts` (keine Google-Verbindung): derzeit Cormorant Garamond für Überschriften und Jost für Text. Schwarz-weiß wirkte ihm zu „basic“.

## Offen

1. Der Nutzer will nicht, dass die Seite nach KI aussieht. Schritt 1 ist besprochen, aber noch nicht umgesetzt: Punkte am Ende von Überschriften entfernen, das immer gleiche Muster „Überschrift links, Satz rechts“ aufbrechen, den Fünf-Schritte-Block „So entsteht Ihr Möbel“ und den Dreier-Block streichen, die Stichwort-Kästchen und Striche über Überschriften entfernen, Werbe-Merksätze („Bis in die letzte Kante.“, „Ohne Katalog und ohne Standardmaß“) durch schlichte, konkrete Sätze ersetzen. Dazu 8 bis 10 Fragen an Christian für Sprachnachrichten, damit die Texte in seiner Sprache entstehen.
2. Schritt 2, sobald Fotos und Antworten da sind: Projekte als Geschichten mit Bezirk, Holzart, Dauer und gelöster Schwierigkeit. Eingescannte Skizzen von Christian einbauen. Eventuell eine gekaufte Schrift statt Google-Schriften.
3. Videos: ja, aber nur kurze, stumme Clips in Schleife (Laden gehen auf, Fugen, Werkstatt), selbst gehostet auf ALL-INKL, kein YouTube. Je Projekt 3 bis 5 Clips zu 5 bis 10 Sekunden vom Fotografen. Die Shot-Liste für den Fotografen (Werkstatt, Portrait, je Projekt Gesamtansicht, 2 bis 3 Details, Möbel im Raum mit Licht, plus Clips) ist noch zu schreiben und mit der Liste für Horst zusammenzulegen.
4. Echte Fotos vom Plattformbett einsetzen, die SketchUp-Entwürfe ersetzen.
5. Fotos von Merle für die Team-Karte.
6. Inbetriebnahme: FTP-Zugang im KAS, PHP 8.1 oder neuer, Postfach office@tischler-vetoe.at prüfen, Secrets in GitHub, ersten Upload testen, Test-Anfrage mit Anhang, erst dann DNS von Wix umstellen.
7. Noch vom Nutzer zu bestätigen: Bildtexte (Holzarten, Details), der Text zur Küche, „Antwort innerhalb von zwei Werktagen“ und „Angebot mit fixem Preis“. Impressum und Datenschutz rechtlich prüfen lassen.
