<?php

function text_to_image($text, $image_width, $colour = array(0,244,34), $background = array(255,255,255))
{
    $font = 4;
    $line_height = 15;
    $padding = 0;
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
$text = array('Krok 1 je: umyj si ruce', "Krok 2: tady", "Ukazka", 'Krok 1 je: umyj si ruce');
$img = array("placeholder.png", "placeholder.png", "placeholder.png", "placeholder.png");
$img_count = count($img);

$main_size = array(400, 500);
$background = array(0, 0, 0);
$text_color = array(255, 255, 255);
$header_color_ar = array(200, 200, 200);
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
    $pre_dest = imagecreatetruecolor(100, 100);
    $size_img = getimagesize($img_for_resize);
    $src = imagecreatefrompng($img_for_resize);
    $top_margin = $main_size[1]/6;
    if($img_count-$i > 2 or $img_count%3 == 0){
        $this_row = 3;
    }elseif($img_count%3 == 1 and $img_count-$i == 2){
        $this_row = 3;
    }elseif($img_count%3 == 2){
        $this_row = 1;
    }else{
        $this_row = -1;
    }
    $margin = 10;
    imagecopyresized($pre_dest, $src, 0, 0, 0, 0, $main_size[0]*0.25, $main_size[1]*0.25, $size_img[0], $size_img[1]);
    $x_position = (($main_size[1] - (100+$margin)*$this_row) /4) + (($i%3)*$main_size[0]*0.25+($i%3)*$margin);
    if(strlen($text[$i]) > $main_size[0]*0.25/10){
        $text_in_image = text_to_image($text[$i], $main_size[0]*0.25, $text_color, $background);
        imagecopymerge($main_canvas, $text_in_image[0], $x_position, $top_margin*($i-$i%3)+$top_margin, 0, 0, 100, $text_in_image[1], 100);
    }else{
        imagestring($main_canvas, 4,  $x_position,  $top_margin*($i-$i%3)+$top_margin, $text[$i], imagecolorallocate($main_canvas, $text_color[0], $text_color[1], $text_color[2]));
    }
    imagecopymerge($main_canvas, $pre_dest, $x_position, ($top_margin*($i-$i%3)+$main_size[1]/5)+$row_height[($i-$i%3)/3], 0, 0, 100, 100, 100);
}

header('Content-Type: image/jpeg');
imagejpeg($main_canvas);