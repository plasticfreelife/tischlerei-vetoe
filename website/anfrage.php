<?php
/*
 * Tischlerei Christian Vetö – Anfrageformular
 * Nimmt das Formular von anfrage.html entgegen und schickt es samt Anhängen per E-Mail.
 * Läuft auf dem Webspace von ALL-INKL (PHP mit mail()). Es wird nichts auf dem Server gespeichert.
 */

declare(strict_types=1);

// ---- Einstellungen -------------------------------------------------------
const EMPFAENGER = 'office@tischler-vetoe.at';
const ABSENDER = 'office@tischler-vetoe.at';   // muss ein Postfach dieser Domain bei ALL-INKL sein
const MAX_DATEIEN = 5;
const MAX_DATEI_BYTES = 10 * 1024 * 1024;      // 10 MB je Datei
const MAX_GESAMT_BYTES = 20 * 1024 * 1024;     // 20 MB zusammen
const ERLAUBTE_ENDUNGEN = ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'heic', 'dwg', 'dxf', 'docx', 'xlsx'];

// ---- Antwort -------------------------------------------------------------
$willJson = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');

function antworten(bool $ok, string $text, int $status = 200): never
{
    global $willJson;
    http_response_code($status);
    if ($willJson) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => $ok, 'text' => $text], JSON_UNESCAPED_UNICODE);
    } else {
        header('Location: anfrage.html' . ($ok ? '?gesendet=1' : '?fehler=1'), true, 303);
    }
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: anfrage.html', true, 303);
    exit;
}

// Leere POST-Daten trotz Methode POST: die Anfrage war größer als post_max_size
if (empty($_POST) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    antworten(false, 'Die Dateien sind zusammen zu groß. Bitte höchstens 20 MB anhängen.', 413);
}

// Spamfalle: dieses Feld ist für Menschen unsichtbar und bleibt leer
if (trim((string)($_POST['website'] ?? '')) !== '') {
    antworten(true, 'Danke.');
}

// ---- Felder --------------------------------------------------------------
function feld(string $name, int $max): string
{
    $wert = trim((string)($_POST[$name] ?? ''));
    $wert = str_replace("\0", '', $wert);
    return mb_substr($wert, 0, $max);
}
function einzeilig(string $wert): string
{
    return trim(preg_replace('/[\r\n\t]+/', ' ', $wert) ?? '');
}

$vorhaben  = einzeilig(feld('vorhaben', 300));
$masse     = einzeilig(feld('masse', 300));
$ort       = einzeilig(feld('ort', 120));
$zeitraum  = einzeilig(feld('zeitraum', 80));
$budget    = einzeilig(feld('budget', 80));
$nachricht = feld('nachricht', 6000);
$name      = einzeilig(feld('name', 120));
$telefon   = einzeilig(feld('telefon', 60));
$email     = einzeilig(feld('email', 200));

if ($vorhaben === '' || $nachricht === '' || $name === '') {
    antworten(false, 'Bitte füllen Sie „Was soll entstehen?“, Ihre Nachricht und Ihren Namen aus.', 422);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    antworten(false, 'Bitte prüfen Sie Ihre E-Mail-Adresse.', 422);
}

// ---- Anhänge -------------------------------------------------------------
$anhaenge = [];
if (isset($_FILES['dateien']) && is_array($_FILES['dateien']['name'])) {
    $gesamt = 0;
    $anzahl = count($_FILES['dateien']['name']);
    for ($i = 0; $i < $anzahl; $i++) {
        $fehler = $_FILES['dateien']['error'][$i];
        if ($fehler === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        if ($fehler === UPLOAD_ERR_INI_SIZE || $fehler === UPLOAD_ERR_FORM_SIZE) {
            antworten(false, 'Eine Datei ist zu groß. Bitte höchstens 10 MB je Datei.', 413);
        }
        if ($fehler !== UPLOAD_ERR_OK) {
            antworten(false, 'Eine Datei konnte nicht hochgeladen werden. Bitte versuchen Sie es noch einmal.', 400);
        }
        if (count($anhaenge) >= MAX_DATEIEN) {
            antworten(false, 'Bitte höchstens ' . MAX_DATEIEN . ' Dateien anhängen.', 422);
        }
        $tmp = $_FILES['dateien']['tmp_name'][$i];
        $groesse = (int)$_FILES['dateien']['size'][$i];
        $original = basename((string)$_FILES['dateien']['name'][$i]);
        $endung = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        if (!is_uploaded_file($tmp)) {
            antworten(false, 'Eine Datei konnte nicht gelesen werden.', 400);
        }
        if (!in_array($endung, ERLAUBTE_ENDUNGEN, true)) {
            antworten(false, 'Dateityp nicht erlaubt: ' . $original . '. Erlaubt sind PDF, Bilder, DWG, DXF, Word und Excel.', 422);
        }
        if ($groesse > MAX_DATEI_BYTES) {
            antworten(false, 'Die Datei ' . $original . ' ist größer als 10 MB.', 413);
        }
        $gesamt += $groesse;
        if ($gesamt > MAX_GESAMT_BYTES) {
            antworten(false, 'Die Dateien sind zusammen größer als 20 MB.', 413);
        }
        // Dateinamen für den Mail-Header entschärfen
        $sauber = preg_replace('/[^A-Za-z0-9._ -]/u', '_', $original) ?: ('anhang.' . $endung);
        $anhaenge[] = ['name' => $sauber, 'daten' => (string)file_get_contents($tmp)];
    }
}

// ---- E-Mail bauen --------------------------------------------------------
$zeilen = [
    'Neue Projektanfrage über tischler-vetoe.at',
    '',
    'Was soll entstehen: ' . $vorhaben,
    'Maße: ' . ($masse !== '' ? $masse : 'noch offen'),
    'Bezirk oder Ort: ' . ($ort !== '' ? $ort : '-'),
    'Zeitraum: ' . ($zeitraum !== '' ? $zeitraum : 'noch offen'),
    'Rahmen: ' . ($budget !== '' ? $budget : 'im Gespräch klären'),
    '',
    'Nachricht:',
    $nachricht,
    '',
    'Name: ' . $name,
    'Telefon: ' . ($telefon !== '' ? $telefon : '-'),
    'E-Mail: ' . $email,
    'Anhänge: ' . (count($anhaenge) ? implode(', ', array_column($anhaenge, 'name')) : 'keine'),
];
$text = implode("\r\n", $zeilen);

$betreff = 'Projektanfrage: ' . mb_substr($vorhaben, 0, 80) . ($ort !== '' ? ', ' . $ort : '');
$betreffKodiert = '=?UTF-8?B?' . base64_encode($betreff) . '?=';
$nameKodiert = '=?UTF-8?B?' . base64_encode($name) . '?=';
$grenze = 'vetoe-' . bin2hex(random_bytes(12));

$kopf = [
    'From: =?UTF-8?B?' . base64_encode('Website Tischlerei Vetö') . '?= <' . ABSENDER . '>',
    'Reply-To: ' . $nameKodiert . ' <' . $email . '>',
    'MIME-Version: 1.0',
    'Content-Type: multipart/mixed; boundary="' . $grenze . '"',
];

$rumpf = '--' . $grenze . "\r\n"
    . "Content-Type: text/plain; charset=UTF-8\r\n"
    . "Content-Transfer-Encoding: base64\r\n\r\n"
    . chunk_split(base64_encode($text)) . "\r\n";
foreach ($anhaenge as $anhang) {
    $rumpf .= '--' . $grenze . "\r\n"
        . 'Content-Type: application/octet-stream; name="' . $anhang['name'] . "\"\r\n"
        . "Content-Transfer-Encoding: base64\r\n"
        . 'Content-Disposition: attachment; filename="' . $anhang['name'] . "\"\r\n\r\n"
        . chunk_split(base64_encode($anhang['daten'])) . "\r\n";
}
$rumpf .= '--' . $grenze . "--\r\n";

$gesendet = mail(EMPFAENGER, $betreffKodiert, $rumpf, implode("\r\n", $kopf), '-f' . ABSENDER);

if (!$gesendet) {
    antworten(false, 'Die Anfrage konnte gerade nicht gesendet werden. Bitte schreiben Sie uns direkt an ' . EMPFAENGER . '.', 500);
}
antworten(true, 'Danke für Ihre Anfrage. Wir melden uns innerhalb von zwei Werktagen.');
