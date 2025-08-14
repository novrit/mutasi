<?php
// barcode.php?text=... -> PNG image
$text = $_GET['text'] ?? '000000';
$w = 2; $h = 50;
$img_w = max(120, strlen($text) * 10 * $w);
$img = imagecreatetruecolor($img_w, $h);
$white = imagecolorallocate($img,255,255,255);
$black = imagecolorallocate($img,0,0,0);
imagefilledrectangle($img,0,0,$img_w,$h,$white);
$x = 0;
for ($i=0;$i<strlen($text);$i++){
  $c = ord($text[$i]);
  $pattern = ($c % 6) + 1;
  for ($p=0;$p<$pattern;$p++){
    imagefilledrectangle($img,$x,0,$x+$w-1,$h,$black);
    $x += $w*2;
  }
  $x += $w;
}
header('Content-Type: image/png');
imagepng($img);
imagedestroy($img);
phpinfo();
?>