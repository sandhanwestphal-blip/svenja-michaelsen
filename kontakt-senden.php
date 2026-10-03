<?php
declare(strict_types=1);

const EMPFAENGER = 'info@svenja-michaelsen.de';
const MAX_NACHRICHTENLAENGE = 5000;

function antwort(string $titel, string $text, bool $erfolg, int $status = 200): never
{
    http_response_code($status);
    $farbe = $erfolg ? '#2f628a' : '#8a3340';
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!doctype html><html lang="de"><head><meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>' . htmlspecialchars($titel, ENT_QUOTES, 'UTF-8') . ' | Naturheilpraxis Svenja Michaelsen</title>';
    echo '<link rel="stylesheet" href="css/style.css?v=2"></head><body>';
    echo '<main id="inhalt"><section><div class="wrap eng" style="padding-top:4rem">';
    echo '<a class="marke" href="index.html"><img src="img/logo.svg" alt="" width="44" height="44"><span><b>Svenja Michaelsen</b><span>Naturheilpraxis</span></span></a>';
    echo '<div class="hinweis" style="margin-top:3rem;border-left-color:' . $farbe . '">';
    echo '<h1>' . htmlspecialchars($titel, ENT_QUOTES, 'UTF-8') . '</h1>';
    echo '<p>' . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '</p></div>';
    echo '<p><a class="knopf" href="kontakt.html">Zurück zur Kontaktseite</a></p>';
    echo '</div></section></main></body></html>';
    exit;
}

if (in_array(($_SERVER['REQUEST_METHOD'] ?? ''), ['GET', 'HEAD'], true)) {
    header('Content-Type: text/html; charset=UTF-8');
    readfile(__DIR__ . DIRECTORY_SEPARATOR . 'index.html');
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    antwort('Ungültiger Aufruf', 'Bitte verwenden Sie das Kontaktformular auf der Kontaktseite.', false, 405);
}

if (trim((string)($_POST['website'] ?? '')) !== '') {
    antwort('Vielen Dank', 'Ihre Nachricht wurde entgegengenommen.', true);
}

$ip = (string)($_SERVER['REMOTE_ADDR'] ?? 'unbekannt');
$rateFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'svenja-form-' . hash('sha256', $ip);
if (is_file($rateFile) && (time() - (int)filemtime($rateFile)) < 45) {
    antwort('Bitte kurz warten', 'Es wurde gerade eine Nachricht gesendet. Bitte versuchen Sie es in einer Minute erneut.', false, 429);
}

$name = trim((string)($_POST['name'] ?? ''));
$telefon = trim((string)($_POST['telefon'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$betreff = trim((string)($_POST['betreff'] ?? ''));
$nachricht = trim((string)($_POST['nachricht'] ?? ''));
$einwilligung = (string)($_POST['datenschutz_einwilligung'] ?? '');

foreach (['name' => &$name, 'telefon' => &$telefon, 'betreff' => &$betreff] as &$wert) {
    $wert = str_replace(["\r", "\n"], ' ', $wert);
}
unset($wert);

if ($name === '' || mb_strlen($name) > 100 ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150 ||
    $betreff === '' || mb_strlen($betreff) > 150 ||
    $nachricht === '' || mb_strlen($nachricht) > MAX_NACHRICHTENLAENGE ||
    mb_strlen($telefon) > 50 || $einwilligung !== 'ja') {
    antwort('Angaben prüfen', 'Bitte füllen Sie alle Pflichtfelder korrekt aus und bestätigen Sie die Datenschutzerklärung.', false, 400);
}

$mailBetreff = 'Website-Anfrage: ' . $betreff;
$mailText = "Neue Nachricht über svenja-michaelsen.de\n\n"
    . "Name: {$name}\n"
    . "E-Mail: {$email}\n"
    . "Telefon: " . ($telefon !== '' ? $telefon : 'nicht angegeben') . "\n"
    . "Betreff: {$betreff}\n\n"
    . "Nachricht:\n{$nachricht}\n\n"
    . "Datenschutzeinwilligung: erteilt\n";

$headers = [
    'From: Website Svenja Michaelsen <info@svenja-michaelsen.de>',
    'Reply-To: ' . $email,
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'Content-Transfer-Encoding: 8bit',
];

if (!mail(EMPFAENGER, $mailBetreff, $mailText, implode("\r\n", $headers))) {
    antwort('Versand nicht möglich', 'Die Nachricht konnte technisch nicht versendet werden. Bitte kontaktieren Sie die Praxis telefonisch oder per E-Mail.', false, 500);
}

@touch($rateFile);
antwort('Nachricht gesendet', 'Vielen Dank für Ihre Nachricht. Die Praxis meldet sich so bald wie möglich bei Ihnen.', true);
