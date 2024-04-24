<?php
session_start();

header('Content-type: image/png');

$captchaText = isset($_SESSION['captcha']) ? $_SESSION['captcha'] : '';
$captchaImage = imagecreatetruecolor(120, 40);
$backgroundColor = imagecolorallocate($captchaImage, 255, 255, 255);
$textColor = imagecolorallocate($captchaImage, 0, 0, 0);

imagefilledrectangle($captchaImage, 0, 0, 120, 40, $backgroundColor);
imagestring($captchaImage, 5, 20, 10, $captchaText, $textColor);

imagepng($captchaImage);
imagedestroy($captchaImage);
