<?php
    if (!defined('INCLUDE_LIB')) {
        $mode = @$argv[1];
        $filename = @$argv[2];
        
        if ($mode == null) {
            die('Error: Incorrect conpic action!');
        }
        
        if ($filename == null) {
            die('Error: empty path!');
        }
        
        switch ($mode) {
            case 'draw':
                drawConPic($filename);
            break;
            
            case 'convert2conpic':
                convert2conpic($filename);
            break;
            
            case 'convert2png':
                convert2png($filename);
            break;
            
            default: 
                die('Error: Incorrect conpic action!');
        }
    }
    
    function drawConPic($filename, $show_timer = 0) {
        echo file_get_contents($filename);
        
        if ($show_timer <= 0) {
            shell_exec(PHP_OS == 'WINNT' ? 'pause' : "read a");
        } else {
            sleep ($show_timer);
        }
    }

    function convert2conpic($png) {  
        if (@$png == null) {
            die('Error: empty path!');
        }
        if (!file_exists($png)) {
            die('Error: file doesn\'t exists!');
        }
        
        $modes = array(' ', '▄', '▀', '█');
        
        $image_file = @imagecreatefrompng($png);
        $_X = @imagesx($image_file);
        $_Y = @imagesy($image_file);
        
        /*
        if ($_X != 120 || $_Y != 60) {
            die('Error: invalid image file!');
        }*/
        
        if ($_Y % 2 != 0) $_Y++;
        
        function toMapArray($color) {
            $red = $color >> 4*4;
            $gb = $color - ($red << 4*4);
            $green = $gb >> 4*2;
            $blue = $gb - ($green << 4*2);
            return array($red, $green, $blue);
        } 
        
        $image = '';
        
        $bit_score = 0;
        $byte_score = 0;
        for ($y = 0; $y < $_Y; $y+=2) {
            $bit_score = 0;
            for ($x = 0; $x < ($y < $_Y-2 ? $_X : $_X-1); $x++) {
                $display = array(0,0);            
                $color_1 = toMapArray(imagecolorat($image_file, $x, $y));
                $color_2 = toMapArray(imagecolorat($image_file, $x, $y+1));
                
                if ($color_1[0]==0&&$color_1[1]==0&&$color_1[2]==0) {
                    $display[0] = 1;
                }
                if ($color_2[0]==0&&$color_2[1]==0&&$color_2[2]==0) {
                    $display[1] = 1;
                }
                
                if ($bit_score*2 >= 8) {
                    $bit_score = 0;
                    $byte_score++;
                }
                switch($display) {
                    case array(0,0):
                        $image .= $modes[0];
                    break;
                    case array(0,1):
                        $image .= $modes[1];
                    break;
                    case array(1,0):
                        $image .= $modes[2];
                    break;
                    case array(1,1):
                        $image .= $modes[3];
                    break;
                }
                $bit_score++;
            }
            $byte_score++;
            $image .= $y < $_Y-2 ? PHP_EOL : null;
        }
        
        file_put_contents($png.'.conpic', $image);
    }
    
    function convert2png($filename) {
        $modes = array(' ', '▄', '▀', '█');
        
        if (@$filename == null) {
            die('Error: empty path!');
        }
        if (!file_exists($filename)) {
            die('Error: file doesn\'t exists!');
        }
        
        $image = file_get_contents($filename);
        $image = explode("\n", $image);
        
        $x = -1;
        $y = count($image)*2;
        
        $png = null;
        $black;
        $white;
        
        foreach ($image as $id => $line) {
            if (substr($line, -1, 1) == "\r") $line = substr($line, 0, -1);
            $line_len = mb_strlen($line);
            if ($png === null || $x == -1) {
                $x = $line_len;
                $png = imagecreatetruecolor($x, $y);
                $black = imagecolorallocate($png, 0, 0, 0);
                $white = imagecolorallocate($png, 255, 255, 255);
            }
            for ($i = 0; $i < $line_len; $i++) {
                switch (@mb_substr($line, $i, 1)) {
                    case $modes[0]: 
                        imagesetpixel($png, $i, $id*2, $white);
                        imagesetpixel($png, $i, $id*2+1, $white);
                    break;
                    case $modes[1]: 
                        imagesetpixel($png, $i, $id*2, $white);
                        imagesetpixel($png, $i, $id*2+1, $black);
                    break;
                    case $modes[2]: 
                        imagesetpixel($png, $i, $id*2, $black);
                        imagesetpixel($png, $i, $id*2+1, $white);
                    break;
                    case $modes[3]: 
                        imagesetpixel($png, $i, $id*2, $black);
                        imagesetpixel($png, $i, $id*2+1, $black);
                    break;
                    default:
                        imagesetpixel($png, $i, $id*2, $white);
                        imagesetpixel($png, $i, $id*2+1, $white);
                }
            }
            if ($line_len < $x) {
                for ($i = 0; $i < $x-$line_len; $i++) {
                    imagesetpixel($png, $line_len+$i, $id*2, imagecolorat($png, $line_len-1, $id*2));
                    imagesetpixel($png, $line_len+$i, $id*2+1, imagecolorat($png, $line_len-1, $id*2+1));
                }
            }
        }
        
        imagepng($png, $filename.'.png');
    }