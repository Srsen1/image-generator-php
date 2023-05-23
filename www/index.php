<?php

$header = "Hlavni Nadpis";
$text = array("Ukazka");
$number_of_text = count($text);

$img = array('placeholder.png');
$number_of_img = count($img);

if ($number_of_img != $number_of_text){
    echo "Error";
}

$main_size = array(300, 300);

$main_canvas = imagecreatetruecolor($main_size[0], $main_size[1]);

$text_color = imagecolorallocate($main_canvas, 255, 255, 255);
$header_color = imagecolorallocate($main_canvas, 200, 200, 200);

imagestring($main_canvas, 4, 8, 40,  $text[0], $text_color);
imagestring($main_canvas, 10, 80, 10,  $header, $header_color);

$dest = imagecreatetruecolor(300, 300);
$pre_dest = imagecreatetruecolor(100, 100);

$i = 0;
foreach($img as $img_for_resize){
    $size_img = getimagesize($img_for_resize);
    $src = imagecreatefrompng($img_for_resize);
    imagecopyresized($pre_dest, $src, 0, 0, 0, 0, $main_size[0]*0.25, $main_size[1]*0.25, $size_img[0], $size_img[1]);
    imagecopymerge($main_canvas, $pre_dest, $i*$main_size[0]*0.25+$i*5+10, 60, 0, 0, 100, 100, 100);
    $i++;
}


header('Content-Type: image/jpeg');
imagejpeg($main_canvas);

imagedestroy($dest);