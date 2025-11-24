<?php
/**
 * captcha.php – komplett & optimiert für 200x67px
 * größere Schrift & mehr Abstand zwischen Buchstaben
 */

declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* ===================== Konfiguration ===================== */
$width       = 200;
$height      = 67;

$length      = random_int(5, 6);
$charset     = 'ACEFGHJKLMNPQRSTUVWXYZ2345679abcdefghjkmnpqrstuvwxyz';
$fontPath    = __DIR__ . '/fonts/arial.ttf';

$fontMax     = 32; // größer
$fontMin     = 22; // nicht zu klein

$letterSpacing = 5; // extra Abstand zwischen Buchstaben

$bgColorFrom = [245, 245, 245];
$bgColorTo   = [225, 225, 225];

$lineCount   = 6;
$dotCount    = 500;
$gridNoise   = true;

$waveAmpY    = random_int(2, 4);
$wavePerY    = random_int(60, 100);
$wavePhaseY  = random_int(0, 628) / 100;

$waveAmpX    = random_int(2, 3);
$wavePerX    = random_int(60, 100);
$wavePhaseX  = random_int(0, 628) / 100;

$rotateEachChar = true;
$charRotateMin  = -8;
$charRotateMax  = 8;

/* ===================== Funktionen ===================== */
function randText(string $set, int $len): string {
    $out = '';
    $max = strlen($set) - 1;
    for ($i = 0; $i < $len; $i++) {
        $out .= $set[random_int(0, $max)];
    }
    return $out;
}

function lerp($a, $b, $t) { return $a + ($b - $a) * $t; }

function randDarkColor(): array {
    return [random_int(10, 60), random_int(10, 60), random_int(10, 60)];
}

function applyWaveDistortion(GdImage $src, int $w, int $h, int $ampX, int $perX, float $phaseX, int $ampY, int $perY, float $phaseY): GdImage {
    $dst = imagecreatetruecolor($w, $h);
    imagealphablending($dst, true);
    imagesavealpha($dst, true);
    $bg = imagecolorallocatealpha($dst, 255, 255, 255, 127);
    imagefill($dst, 0, 0, $bg);

    for ($x = 0; $x < $w; $x++) {
        for ($y = 0; $y < $h; $y++) {
            $offsetX = (int)round(sin(2 * M_PI * ($y / max(1,$perX)) + $phaseX) * $ampX);
            $offsetY = (int)round(sin(2 * M_PI * ($x / max(1,$perY)) + $phaseY) * $ampY);
            $sx = $x + $offsetX;
            $sy = $y + $offsetY;
            if ($sx >= 0 && $sx < $w && $sy >= 0 && $sy < $h) {
                imagesetpixel($dst, $x, $y, imagecolorat($src, $sx, $sy));
            }
        }
    }
    return $dst;
}

/* ===================== Captcha generieren ===================== */
$text = randText($charset, $length);
$_SESSION['captcha_text'] = $text;

/* Bild vorbereiten */
$img = imagecreatetruecolor($width, $height);
imageantialias($img, true);
imagealphablending($img, true);
imagesavealpha($img, true);

/* Hintergrundgradient */
for ($y = 0; $y < $height; $y++) {
    $t = $y / max(1, $height - 1);
    $r = (int)lerp($bgColorFrom[0], $bgColorTo[0], $t);
    $g = (int)lerp($bgColorFrom[1], $bgColorTo[1], $t);
    $b = (int)lerp($bgColorFrom[2], $bgColorTo[2], $t);
    $col = imagecolorallocate($img, $r, $g, $b);
    imageline($img, 0, $y, $width, $y, $col);
}

/* Gitterlinien */
if ($gridNoise) {
    $grid = imagecolorallocatealpha($img, 0, 0, 0, 110);
    for ($x = 0; $x < $width; $x += 12) imageline($img, $x, 0, $x, $height, $grid);
    for ($y = 0; $y < $height; $y += 12) imageline($img, 0, $y, $width, $y, $grid);
}

/* Störlinien */
for ($i = 0; $i < $lineCount; $i++) {
    $c = randDarkColor();
    $col = imagecolorallocatealpha($img, $c[0], $c[1], $c[2], random_int(70, 110));
    imagesetthickness($img, random_int(1, 3));
    imageline($img, random_int(0, $width), random_int(0, $height), random_int(0, $width), random_int(0, $height), $col);
}

/* Störpunkte */
for ($i = 0; $i < $dotCount; $i++) {
    $c = randDarkColor();
    $col = imagecolorallocatealpha($img, $c[0], $c[1], $c[2], random_int(70, 120));
    imagesetpixel($img, random_int(0, $width - 1), random_int(0, $height - 1), $col);
}

/* Dynamische Schriftgröße ermitteln */
$fontSize = $fontMax;
do {
    $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
    $textW = max($bbox[2], $bbox[4]) - min($bbox[0], $bbox[6]);
    $textH = max($bbox[1], $bbox[3]) - min($bbox[5], $bbox[7]);
    $fontSize--;
} while (($textW + $letterSpacing * strlen($text) > $width - 10 || $textH > $height - 10) && $fontSize >= $fontMin);
$fontSize++;

/* Text zentrieren */
$x = (int)(($width - $textW - $letterSpacing * (strlen($text)-1)) / 2);
$y = (int)(($height + $textH) / 2);

/* Zeichen einzeln zeichnen mit Rotation */
$cursor = $x;
for ($i = 0; $i < strlen($text); $i++) {
    $char = $text[$i];
    $angle = $rotateEachChar ? random_int($charRotateMin, $charRotateMax) : 0;
    $bbox = imagettfbbox($fontSize, $angle, $fontPath, $char);
    $charW = max($bbox[2], $bbox[4]) - min($bbox[0], $bbox[6]);
    $color = imagecolorallocate($img, 100, 0, 0); // dunkelrot
    imagettftext($img, $fontSize, $angle, (int)$cursor, $y, $color, $fontPath, $char);
    $cursor += $charW + $letterSpacing;
}

/* Bögen zur Störung */
for ($i = 0; $i < 4; $i++) {
    $c = randDarkColor();
    $col = imagecolorallocatealpha($img, $c[0], $c[1], $c[2], random_int(80, 110));
    imagearc($img, random_int(0, $width), random_int(0, $height),
        random_int(50, 150), random_int(30, 80),
        random_int(0, 360), random_int(0, 360), $col
    );
}

/* Wellenverzerrung */
$distorted = applyWaveDistortion($img, $width, $height, $waveAmpX, $wavePerX, $wavePhaseX, $waveAmpY, $wavePerY, $wavePhaseY);

/* Ausgabe */
while (ob_get_level() > 0) ob_end_clean();
header('Content-Type: image/png');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

imagepng($distorted);
imagedestroy($distorted);
imagedestroy($img);
exit;
