<?php
// Avvia la sessione se non è già attiva
if (session_status() === PHP_SESSION_NONE) { session_start(); }

header('Content-Type: image/png');

// Genera due numeri casuali
$n1 = rand(1, 9);
$n2 = rand(1, 9);

// Salva il risultato corretto in sessione
$_SESSION['captcha_result'] = $n1 + $n2;

// Crea l'immagine (stile neon-dark)
$image = imagecreatetruecolor(80, 30);
$bg = imagecolorallocate($image, 0, 0, 0); // Sfondo nero
$text_color = imagecolorallocate($image, 0, 255, 255); // Ciano neon

imagefill($image, 0, 0, $bg);
imagestring($image, 5, 10, 7, "$n1 + $n2 = ?", $text_color);

// Aggiunge un po' di disturbo (linee) per i bot
for($i=0; $i<3; $i++) {
    imageline($image, rand(0,80), rand(0,30), rand(0,80), rand(0,30), $text_color);
}

imagepng($image);
imagedestroy($image);
?>