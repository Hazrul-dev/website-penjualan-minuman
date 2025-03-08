<?php
session_start();

// Buat gambar captcha
$width = 120;
$height = 40;
$image = imagecreate($width, $height);

// Warna background
$background_color = imagecolorallocate($image, 255, 255, 255);

// Warna teks
$text_color = imagecolorallocate($image, 0, 0, 0);

// Generate random string untuk captcha
$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
$captcha_text = '';
for ($i = 0; $i < 6; $i++) {
    $captcha_text .= $characters[rand(0, strlen($characters) - 1)];
}

// Simpan captcha text ke session
$_SESSION['captcha'] = $captcha_text;

// Tambahkan teks ke gambar menggunakan font bawaan GD
imagestring($image, 5, 20, 10, $captcha_text, $text_color);


// Output gambar sebagai PNG
header('Content-Type: image/png');
imagepng($image);

// Hapus gambar dari memori
imagedestroy($image);
?>