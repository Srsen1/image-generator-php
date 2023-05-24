<?php

function text_to_image($text, $image_width, $colour = array(0,244,34), $background = array(255,255,255), $font, $font_size_text)
{
    $font_size = $font_size_text;
    $line_height = $font_size*1.5;
    $padding = 0;
    $text = wordwrap($text, ($image_width/10));
    $lines = explode("\n", $text);
    $height = (count($lines) * $line_height) + ($padding * 2);
    $image = imagecreate($image_width,$height);
    $background = imagecolorallocate($image, $background[0], $background[1], $background[2]);
    $colour = imagecolorallocate($image,$colour[0],$colour[1],$colour[2]);
    imagefill($image, 0, 0, $background);
    $i = $padding+$font_size;
    foreach($lines as $line){
        imagettftext($image, $font_size, 0, $padding, $i, $colour, $font, trim($line));
        $i += $line_height;
    }
    return array($image, $height);
    imagedestroy($image);
    exit;
}
function img_round($pre_dest)
{
    $width = imagesx ($pre_dest);
    $height = imagesy($pre_dest);

    $image_rounded = imagecreatetruecolor($width, $height);
    imagealphablending ($image_rounded, true);
    imagecopyresampled($image_rounded, $pre_dest, 0, 0, 0, 0, $width, $height, $width, $height);

    $mask = imagecreatetruecolor($width, $height);

    $transparent = imagecolorallocate ($mask, 255, 0, 0);
    imagecolortransparent ($mask, $transparent);

    imagefilledellipse($mask, $width/2, $height/2, $width, $height, $transparent);
    $red = imagecolorallocate ($mask, 0, 0, 0);
    imagecopymerge ($image_rounded, $mask, 0, 0, 0, 0, $width, $height, 100);

    imagecolortransparent ($image_rounded, $red);
    imagefill($image_rounded, 0, 0, $red);
    return $image_rounded;
}


# nastaveni
$header = "Hlavni Napis";
$text = array('Krok 1 je: umyj si ruce Krok 1 je: umyj si ruce', "Krok 2: tady j", "Ukazka", 'Krok 1 je: umyj si ruce', "Ukazka", 'Krok 1 je: umyj si ruce', "Ukazka", 'Krok 1 je: umyj si ruce');
$img = array("placeholder.png", "placeholder.png", "placeholder.png", "placeholder.png", "placeholder.png", "placeholder.png", "placeholder.png", "placeholder.png");
$img_count = count($img);

$font_size_text = 15;
$font_size = 30;
$round = TRUE;
$main_size = array(600, 900);
$background = array(168, 227, 223);
$text_color = array(1, 44, 95);
$header_color_ar = array(1, 44, 95);
$side_margin = $main_size[0]/16;
$img_margin_top_ratio = 2.5; #pomer mezi velikosti obrazku a vrchni mezerou
$font = 'Roboto-Medium';
# konec nastaveni

$main_canvas = imagecreatetruecolor($main_size[0], $main_size[1]);
$header_color = imagecolorallocate($main_canvas, $header_color_ar[0], $header_color_ar[1], $header_color_ar[2]);
imagefill($main_canvas, 0, 0, imagecolorallocate($main_canvas, $background[0], $background[1], $background[2]));

putenv('GDFONTPATH=' . realpath('.'));
$bbox = imagettfbbox($font_size, 0, $font, $header);
$center1 = (imagesx($main_canvas) / 2) - (($bbox[2] - $bbox[0]) / 2);
imagettftext($main_canvas, $font_size, 0, $center1, $font_size+30, $header_color, $font, $header);

$row_height = array();
foreach($text as $i_text => $v_text){
    $text_to_image = text_to_image($text[$i_text], $main_size[0]*0.25, $text_color, $background, $font, $font_size_text);
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
    $row_h = 0;
    $row_h_text = 0;
    foreach($row_height as $r_i => $row){
        if($r_i < (($i-$i%3)/3)){
            $row_h_text += $row;
        }
        if($r_i == (($i-$i%3)/3)){
            $row_h = $row_h_text+$row;
        }
    }
    $y_position = $top_margin*(($i-$i%3)/3)+($img_y*(($i-$i%3)/3)+$img_y/1.3)+$row_h;
    $y_position_text = $top_margin*(($i-$i%3)/3)+$img_y*(($i-$i%3)/3)+$img_y/1.3-10+$row_h_text;

    imagecopyresized($pre_dest, $src, 0, 0, 0, 0, $main_size[0]*0.25, ($main_size[0]*0.25)*$ratio, $size_img[0], $size_img[1]);
    if(strlen($text[$i]) > $main_size[0]*0.25/10){
        $text_in_image = text_to_image($text[$i], $main_size[0]*0.25, $text_color, $background, $font, $font_size_text);
        imagecopymerge($main_canvas, $text_in_image[0], $x_position, $y_position_text, 0, 0, $main_size[0]*0.25, $text_in_image[1], 100);
    }else{
        imagettftext($main_canvas, $font_size_text, 0,  $x_position,  $y_position_text+$font_size/2, imagecolorallocate($main_canvas, $text_color[0], $text_color[1], $text_color[2]), $font, $text[$i]);

    }
    if($round){
        $pre_dest = img_round($pre_dest);
    }
    imagecopymerge($main_canvas, $pre_dest, $x_position, $y_position, 0, 0, $main_size[0]*0.25, ($main_size[0]*0.25)*$ratio, 100);
}

header('Content-Type: image/jpeg');
imagejpeg($main_canvas);