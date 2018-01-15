<?php
    $modes = array('█', '▄', '▀', ' ');
    
    if (@$argv[1] == null) {
        die('Error: empty path!');
    }
    if (!file_exists($argv[1])) {
        die('Error: efile doesn\'t exists!');
    }
    $image_file = @imagecreatefrompng($argv[1]);
    $_X = @imagesx($image_file);
    $_Y = @imagesy($image_file);
    
    if ($_X != 120 || $_Y != 60) {
        die('Error: invalid image file!');
    }
    
    $image = '';
    for ($y = 0; $y < 60; $y+=2) {
        for ($x = 0; $x < ($y < 58 ? 120 : 119); $x++) {
            $display = array(0,0);            
            $color_1 = toMapArray(imagecolorat($image_file, $x, $y));
            $color_2 = toMapArray(imagecolorat($image_file, $x, $y+1));
            
            if ($color_1[0]==0&&$color_1[1]==0&&$color_1[2]==0) {
                $display[0] = 1;
            }
            if ($color_2[0]==0&&$color_2[1]==0&&$color_2[2]==0) {
                $display[1] = 1;
            }
            
            switch($display) {
                case array(0,0):
                    $image .= $modes[3];
                break;
                case array(0,1):
                    $image .= $modes[1];
                break;
                case array(1,0):
                    $image .= $modes[2];
                break;
                case array(1,1):
                    $image .= $modes[0];
                break;
            }
        }
        $image .= $y < 58 ? PHP_EOL : null;
    }
    file_put_contents($argv[1].'.conpic', $image);
    
    function toMapArray($color) {
        $red = $color >> 4*4;
        $gb = $color - ($red << 4*4);
        $green = $gb >> 4*2;
        $blue = $gb - ($green << 4*2);
        return array($red, $green, $blue);
    }
