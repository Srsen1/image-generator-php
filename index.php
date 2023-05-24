<?php

function text_to_image($text, $image_width, $colour = array(0,244,34), $background = array(255,255,255))
{
    $font = 4;
    $line_height = 15;
    $padding = 2;
    $text = wordwrap($text, ($image_width/10));
    $lines = explode("\n", $text);
    $height = (count($lines) * $line_height) + ($padding * 2);
    $image = imagecreate($image_width,$height);
    $background = imagecolorallocate($image, $background[0], $background[1], $background[2]);
    $colour = imagecolorallocate($image,$colour[0],$colour[1],$colour[2]);
    imagefill($image, 0, 0, $background);
    $i = $padding;
    foreach($lines as $line){
        imagestring($image, $font, $padding, $i, trim($line), $colour);
        $i += $line_height;
    }
    return array($image, $height);
    imagedestroy($image);
    exit;
}
# nastaveni
$header = "Hlavni Napis";
$text = array('Krok 1 je: umyj si ruce', "Krok 2: tady j", "Ukazka", 'Krok 1 je: umyj si ruce', "Ukazka", 'Krok 1 je: umyj si ruce', "Ukazka", 'Krok 1 je: umyj si ruce');
$img = array("placeholder.png", "placeholder.png", "placeholder.png", "placeholder.png", "placeholder.png", "placeholder.png", "placeholder.png", "placeholder.png");
$img_count = count($img);

$main_size = array(500, 700);
$background = array(0, 0, 0);
$text_color = array(255, 255, 255);
$header_color_ar = array(200, 200, 200);
$top_margin = ($img_y/1.5);
$side_margin = $main_size[0]/16;
$img_margin_top_ratio = 1.5; #pomer mezi velikosti obrazku a vrchni mezerou
# konec nastaveni

$main_canvas = imagecreatetruecolor($main_size[0], $main_size[1]);
$header_color = imagecolorallocate($main_canvas, $header_color_ar[0], $header_color_ar[1], $header_color_ar[2]);
$main_canvas = imagecreatetruecolor($main_size[0], $main_size[1]);
imagefill($main_canvas, 0, 0, imagecolorallocate($main_canvas, $background[0], $background[1], $background[2]));

imagestring($main_canvas, 10, ($main_size[0]-strlen($header)*10)/2, 20,  $header, $header_color);

$row_height = array();
foreach($text as $i_text => $v_text){
    $text_to_image = text_to_image($text[$i_text], $main_size[0]*0.25, $text_color, $background);
    if(!isset($row_height[($i_text-$i_text%3)/3]) or $row_height[($i_text-$i_text%3)/3] < $text_to_image[1]){
        $row_height[($i_text-$i_text%3)/3] = $text_to_image[1];
    }
}

foreach($img as $i => $img_for_resize){
    $size_img = getimagesize($img_for_resize);
    $ratio = $size_img[0]/$size_img[1];
    $img_y = (($main_size[0]*0.25)*$ratio);
    $pre_dest = imagecreatetruecolor($main_size[0]*0.25, $img_y);
    $src = imagecreatefrompng($img_for_resize);
    if($img_count-$i > 2 or $img_count%3 == 0){
        $this_row = 3;
    }elseif($img_count%3 == 1 and $img_count-$i == 2){
        $this_row = 3;
    }elseif($img_count%3 == 2){
        $this_row = 2;
    }else{
        $this_row = 1;
    }
    $top_margin = ($img_y/$img_margin_top_ratio);
    $x_position = (($main_size[0] - (($main_size[0]*0.25)*$this_row+$side_margin*($this_row-1))) /2) + (($i%3)*$main_size[0]*0.25+($i%3)*$side_margin);
    $y_position = $top_margin*(($i-$i%3)/3)+($img_y*(($i-$i%3)/3)+$img_y/1.3)+$row_height[($i-$i%3)/3];
    $y_position_text = $top_margin*(($i-$i%3)/3)+$img_y*(($i-$i%3)/3)+$img_y/1.3-10;

    imagecopyresized($pre_dest, $src, 0, 0, 0, 0, $main_size[0]*0.25, ($main_size[0]*0.25)*$ratio, $size_img[0], $size_img[1]);
    if(strlen($text[$i]) > $main_size[0]*0.25/10){
        $text_in_image = text_to_image($text[$i], $main_size[0]*0.25, $text_color, $background);
        imagecopymerge($main_canvas, $text_in_image[0], $x_position, $y_position_text, 0, 0, 100, $text_in_image[1], 100);
    }else{
        imagestring($main_canvas, 4,  $x_position,  $y_position_text, $text[$i], imagecolorallocate($main_canvas, $text_color[0], $text_color[1], $text_color[2]));
    }
    imagecopymerge($main_canvas, $pre_dest, $x_position, $y_position, 0, 0, $main_size[0]*0.25, ($main_size[0]*0.25)*$ratio, 100);
}

header('Content-Type: image/jpeg');
imagejpeg($main_canvas);