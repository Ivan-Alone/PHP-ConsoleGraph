<?php 
    if (!defined('INCLUDE_LIB')) {
        $mode = @$argv[1];
        $filename = @$argv[2];
        
        if ($mode == null) {
            die('Error: Incorrect conpic2 action!');
        }
        
        if ($filename == null) {
            die('Error: empty path!');
        }
        
        switch ($mode) {
            case 'draw':
                drawConPic2($filename);
            break;
            
            case 'convert2conpic2':
                convert2conpic2($filename);
            break;
            
            case 'convert2png':
                convert2png2($filename);
            break;
            
            default: 
                die('Error: Incorrect conpic2 action!');
        }
    }
    
    function convert2conpic2($png) {   
        if (@$png == null) {
            die('Error: empty path!');
        }
        if (!file_exists($png)) {
            die('Error: file doesn\'t exists!');
        }
        
        $modes_matrix = array('00', '01', '10', '11');
        
        $image_file = @imagecreatefrompng($png);
        $_X = @imagesx($image_file);
        $_Y = @imagesy($image_file);
        
        if ($_X > 65535 || $_Y*2 > 65535) {
            die('Error: invalid image file!');
        }
        if ($_Y % 2 != 0) $_Y++;
        
        function toMapArray($color) {
            $red = $color >> 4*4;
            $gb = $color - ($red << 4*4);
            $green = $gb >> 4*2;
            $blue = $gb - ($green << 4*2);
            return array($red, $green, $blue);
        } 

        $matrix = array();
        
        $bit_score = 0;
        $byte_score = 0;
        for ($y = 0; $y < $_Y; $y+=2) {
            $bit_score = 0;
            for ($x = 0; $x < ($y < $_Y-2 ? $_X : $_X-1); $x++) {
                $display = array(0,0);            
                $color_1 = toMapArray(@imagecolorat($image_file, $x, $y));
                $color_2 = toMapArray(@imagecolorat($image_file, $x, $y+1));
                
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
                        @$matrix[$byte_score] .= $modes_matrix[0];
                    break;
                    case array(0,1):
                        @$matrix[$byte_score] .= $modes_matrix[1];
                    break;
                    case array(1,0):
                        @$matrix[$byte_score] .= $modes_matrix[2];
                    break;
                    case array(1,1):
                        @$matrix[$byte_score] .= $modes_matrix[3];
                    break;
                }
                $bit_score++;
            }
            $byte_score++;
        }
        
        $x_ = normalize(decbin($_X), 16);
        $y_ = normalize(decbin($_Y/2), 16);
        
        
        
        $image = 'ACONPIC'.chr(bindec(substr($x_, 0, 8))).chr(bindec(substr($x_, 8, 8))).chr(bindec(substr($y_, 0, 8))).chr(bindec(substr($y_, 8, 8)));
        foreach($matrix as $block) {
            $image .= chr(bindec($block));
        }
            
        file_put_contents($png.'.conpic2', $image);
    }
    
    function convert2png2($filename) {
        if (@$filename == null) {
            die('Error: empty path!');
        }
        if (!file_exists($filename)) {
            die('Error: file doesn\'t exists!');
        }
        
        $data = file_get_contents($filename);
        
        if (substr($data, 0, 7) != 'ACONPIC') {
            return false;
        }
        
        $x_size = convert2bytes($data[7], $data[8]);
        $y_size = convert2bytes($data[9], $data[10]);
        
        $ox_test = $x_size/4;
        $ox_test_f = (int)($ox_test);
        $x_bytes = $ox_test_f + ($ox_test_f < $ox_test ? 1 : 0);
        
        if ($x_bytes*$y_size != strlen($data)-11) {
            return false;
        }
        
        $imagePng = imagecreatetruecolor($x_size, $y_size*2);
        $black = imagecolorallocate($imagePng, 0, 0, 0);
        $white = imagecolorallocate($imagePng, 255, 255, 255);
        
        $start = 11;
        $image = '';
        for ($i = 0; $i < $y_size; $i++) {
            $line = substr($data, $start, $x_bytes);
            $x = 0;
            for ($s = 0; $s < strlen($line); $s++) {
                $chain = normalize(decbin(ord($line[$s])));
                for ($px = 0; $px < 4; $px++) {
                    if ($x >= $x_size) break 2;
                    
                    switch (bindec(substr($chain, $px*2, 2))) {
                        case 0:
                            $up = $white;
                            $down = $white;
                        break;
                        case 1:
                            $up = $white;
                            $down = $black;
                        break;
                        case 2:
                            $up = $black;
                            $down = $white;
                        break;
                        case 3:
                            $up = $black;
                            $down = $black;
                        break;
                    }
                    
                    imagesetpixel($imagePng, $x, $i*2, $up);
                    imagesetpixel($imagePng, $x, $i*2+1, $down);
                    $x++;
                }
            }
            
            $image .= !(PHP_OS == 'WINNT') || getWinNT_Version() == '10.0' ? PHP_EOL : null;
            $start += $x_bytes;
        }
        imagepng($imagePng, $filename.'.png');
        
    }
    
    function drawConPic2($filename, $show_timer = 0) {
        if (@$filename == null) {
            die('Error: empty path!');
        }
        if (!file_exists($filename)) {
            die('Error: file doesn\'t exists!');
        }
        return __drawConPic2(file_get_contents($filename));
    }
    
    function __drawConPic2($data, $show_timer = 0) {
        if (substr($data, 0, 7) != 'ACONPIC') {
            return false;
        }
        
        $restore = @$args['restore']===null?true:$args['restore'];
        $clear = @$args['clear']===null?true:$args['clear'];
        
        $isWin32 = PHP_OS == 'WINNT';
        $isWin10 = getWinNT_Version() == '10.0';
        
        if (!$isWin32 || $isWin10) {
            $modes = array(' ', '▄', '▀', '█');
        } else {
            $modes = array(' ', ',', '\'', '#');
        }
        
        $x_size = convert2bytes($data[7], $data[8]);
        $y_size = convert2bytes($data[9], $data[10]);
        
        $ox_test = $x_size/4;
        $ox_test_f = (int)($ox_test);
        $x_bytes = $ox_test_f + ($ox_test_f < $ox_test ? 1 : 0);
        
        if ($x_bytes*$y_size != strlen($data)-11) {
            return false;
        }
        
        $start = 11;
        $image = '';
        for ($i = 0; $i < $y_size; $i++) {
            $line = substr($data, $start, $x_bytes);
            $x = 0;
            for ($s = 0; $s < strlen($line); $s++) {
                $chain = normalize(decbin(ord($line[$s])));
                for ($px = 0; $px < 4; $px++) {
                    if ($x >= $x_size) break 2;
                    $image .= $modes[bindec(substr($chain, $px*2, 2))];
                    $x++;
                }
            }
            $image .= !$isWin32 || $isWin10 ? PHP_EOL : null;
            $start += $x_bytes;
        }
        
        echo (substr($image, 0, !$isWin32 ? -2 : ($isWin10 ? -3 : -1)));
        
        if ($show_timer <= 0) {
            shell_exec(PHP_OS == 'WINNT' ? 'pause' : "read a");
        } else {
            sleep ($show_timer);
        }
        
        return true;
    }
    
    function getWinNT_Version() {
        if (PHP_OS != 'WINNT') {
            return false;
        }
        return php_uname('r');
    }
    
    function normalize($input, $mod = 8, $block = '0') {
        while(strlen($input) < $mod) {
            $input = $block.$input;
        }
        return $input;
    }
    
    function convert2bytes($byte1, $byte2) {
        return bindec(normalize(decbin(ord($byte1))).normalize(decbin(ord($byte2))));
    }
    