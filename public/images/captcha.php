<?php
/**
 * captcha.php
 * Grafisches CAPTCHA mit großem Text, leichter Verfremdung, zentriertem Text
 * und dunkelroter Schriftfarbe
 */

declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* ===================== Konfiguration ===================== */
$width       = 900;
$height      = 300;
$length      = random_int(5, 6);
$charset     = 'ACEFGHJKLMNPQRSTUVWXYZ2345679abcdefghjkmnpqrstuvwxyz';
$fontPath    = __DIR__ . '/fonts/arial.ttf';

$fontMin     = 100;
$fontMax     = 110;

$bgColorFrom = [245, 245, 245];
$bgColorTo   = [225, 225, 225];

$lineCount   = 10;
$dotCount    = 3000;
$gridNoise   = true;

/* Wellen-Einstellungen (milder) */
$waveAmpY    = random_int(3, 6);
$wavePerY    = random_int(140, 200);
$wavePhaseY  = random_int(0, 314) / 100;

$waveAmpX    = random_int(2, 4);
$wavePerX    = random_int(120, 200);
$wavePhaseX  = random_int(0, 314) / 100;

/* Rotation pro Zeichen */
$rotateEachChar = true;
$charRotateMin  = -5;
$charRotateMax  = 5;

/* ===================== Hilfsfunktionen ===================== */
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
                $color = imagecolorat($src, $sx, $sy);
                imagesetpixel($dst, $x, $y, $color);
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

/* Hintergrund mit Verlauf */
for ($y = 0; $y < $height; $y++) {
    $t = $y / max(1, $height - 1);
    $r = (int)lerp($bgColorFrom[0], $bgColorTo[0], $t);
    $g = (int)lerp($bgColorFrom[1], $bgColorTo[1], $t);
    $b = (int)lerp($bgColorFrom[2], $bgColorTo[2], $t);
    $col = imagecolorallocate($img, $r, $g, $b);
    imageline($img, 0, $y, $width, $y, $col);
}

/* Gitter-Rauschen */
if ($gridNoise) {
    $grid = imagecolorallocatealpha($img, 0, 0, 0, 110);
    for ($x = 0; $x < $width; $x += 20) {
        imageline($img, $x, 0, $x, $height, $grid);
    }
    for ($y = 0; $y < $height; $y += 20) {
        imageline($img, 0, $y, $width, $y, $grid);
    }
}

/* Störlinien */
for ($i = 0; $i < $lineCount; $i++) {
    $c = randDarkColor();
    $col = imagecolorallocatealpha($img, $c[0], $c[1], $c[2], random_int(80, 115));
    imagesetthickness($img, random_int(3, 6));
    imageline($img,
        random_int(0, (int)($width * 0.3)), random_int(0, $height),
        random_int((int)($width * 0.7), $width), random_int(0, $height),
        $col
    );
}

/* Störpunkte */
for ($i = 0; $i < $dotCount; $i++) {
    $c = randDarkColor();
    $col = imagecolorallocatealpha($img, $c[0], $c[1], $c[2], random_int(80, 120));
    imagesetpixel($img, random_int(0, $width - 1), random_int(0, $height - 1), $col);
}

/* Textfarbe (dunkelrot) */
$textColor = [139, 0, 0]; // Dark Red RGB

/* ===================== Text zeichnen (zentriert) ===================== */
$useTTF = is_readable($fontPath);
if ($useTTF) {
    // Gesamtbreite ermitteln
    $totalWidth = 0;
    $charBoxes = [];
    for ($i = 0; $i < strlen($text); $i++) {
        $char = $text[$i];
        $size = $fontMin;
        $angle = $rotateEachChar ? random_int($charRotateMin, $charRotateMax) : 0;
        $bbox = imagettfbbox($size, $angle, $fontPath, $char);
        $charW = max($bbox[2], $bbox[4]) - min($bbox[0], $bbox[6]);
        $charBoxes[] = [$char, $size, $angle, $charW];
        $totalWidth += $charW + 30; // fester Abstand
    }
    $x = (int)(($width - $totalWidth) / 2);

    foreach ($charBoxes as [$char, $size, $angle, $charW]) {
        $bbox = imagettfbbox($size, $angle, $fontPath, $char);
        $charH = max($bbox[1], $bbox[3]) - min($bbox[5], $bbox[7]);
        $y = (int)(($height + $charH) / 2);

        for ($dx = -2; $dx <= 2; $dx++) {
            for ($dy = -2; $dy <= 2; $dy++) {
                $col = imagecolorallocate($img, $textColor[0], $textColor[1], $textColor[2]);
                imagettftext($img, $size, $angle, $x + $dx, $y + $dy, $col, $fontPath, $char);
            }
        }
        $x += (int)($charW + 30);
    }
} else {
    $font = 5;
    $boxW = imagefontwidth($font) * strlen($text);
    $x = (int)(($width - $boxW) / 2);
    $y = (int)(($height - imagefontheight($font)) / 2);
    imagestring($img, $font, $x, $y, $text, imagecolorallocate($img, $textColor[0], $textColor[1], $textColor[2]));
}

/* Extra Linien */
for ($i = 0; $i < 4; $i++) {
    $c = randDarkColor();
    $col = imagecolorallocatealpha($img, $c[0], $c[1], $c[2], random_int(80, 110));
    imagearc($img, random_int(0, $width), random_int(0, $height),
        random_int(150, 500), random_int(80, 200),
        random_int(0, 360), random_int(0, 360), $col
    );
}

/* Wellen-Verzerrung anwenden */
$distorted = applyWaveDistortion($img, $width, $height, $waveAmpX, $wavePerX, $wavePhaseX, $waveAmpY, $wavePerY, $wavePhaseY);

/* ===================== Ausgabe ===================== */
if (function_exists('ob_get_level')) {
    while (ob_get_level() > 0) ob_end_clean();
}
header('Content-Type: image/png');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

imagepng($distorted);
imagedestroy($distorted);
imagedestroy($img);
exit;
