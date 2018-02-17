<?php
    define('INCLUDE_LIB', true);
    require_once('conpic1.php');
    require_once('conpic2.php');
    
    $file_1 = @$argv[1];
    $file_2 = @$argv[2];
    
    if ($file_1 == null) die ('Error: first file can\'t be empty!');
    if (!file_exists($file_1)) die ('Error: first file doesn\'t exists!');
    
    $types1 = array('png' => 0b0001, 'conpic' => 0b0010, 'conpic2' => 0b0011);
    $types2 = array('png' => 0b0100, 'conpic' => 0b1000, 'conpic2' => 0b1100, '' => 0b0000);
    
    $bfile_1 = basename($file_1);
    
    @mkdir('temp');
    switch (@$types1[@pathinfo($file_1)['extension']] | @$types2[@pathinfo($file_2)['extension']]) {
        case 0b0101: 
        case 0b1010: 
        case 0b1111: 
            copy($file_1, $file_2);
        break;
        
        case 0b1001:  // png => conpic
            @mkdir('temp');
            copy($file_1, 'temp/'.$bfile_1);
            convert2conpic('temp/'.$bfile_1);
            unlink('temp/'.$bfile_1);
            rename('temp/'.$bfile_1.'.conpic', $file_2);
            @rmdir('temp');
        break;
        case 0b1101:  // png => conpic2
            @mkdir('temp');
            copy($file_1, 'temp/'.$bfile_1);
            convert2conpic2('temp/'.$bfile_1);
            unlink('temp/'.$bfile_1);
            rename('temp/'.$bfile_1.'.conpic2', $file_2);
            @rmdir('temp');
        break;
        
        
        case 0b0110:  // conpic => png
            @mkdir('temp');
            copy($file_1, 'temp/'.$bfile_1);
            convert2png('temp/'.$bfile_1);
            unlink('temp/'.$bfile_1);
            rename('temp/'.$bfile_1.'.png', $file_2);
            @rmdir('temp');
        break;
        case 0b1110:  // conpic => conpic2
            @mkdir('temp');
            copy($file_1, 'temp/'.$bfile_1);
            convert2png('temp/'.$bfile_1);
            unlink('temp/'.$bfile_1);
            convert2conpic2('temp/'.$bfile_1.'.png');
            unlink('temp/'.$bfile_1.'.png');
            rename('temp/'.$bfile_1.'.png.conpic2', $file_2);
            @rmdir('temp');
        break;
        
        
        case 0b0111:  // conpic2 => png
            @mkdir('temp');
            copy($file_1, 'temp/'.$bfile_1);
            convert2png2('temp/'.$bfile_1);
            unlink('temp/'.$bfile_1);
            rename('temp/'.$bfile_1.'.png', $file_2);
            @rmdir('temp');
        break;
        case 0b1011:  // conpic2 => conpic
            @mkdir('temp');
            copy($file_1, 'temp/'.$bfile_1);
            convert2png2('temp/'.$bfile_1);
            unlink('temp/'.$bfile_1);
            convert2conpic('temp/'.$bfile_1.'.png');
            unlink('temp/'.$bfile_1.'.png');
            rename('temp/'.$bfile_1.'.png.conpic', $file_2);
            @rmdir('temp');
        break;
        
        
        case 0b0001: 
            @mkdir('temp');
            copy($file_1, 'temp/'.$bfile_1);
            convert2conpic2('temp/'.$bfile_1);
            unlink('temp/'.$bfile_1);
            drawConPic2('temp/'.$bfile_1.'.conpic2');
            unlink('temp/'.$bfile_1.'.conpic2');
            @rmdir('temp');
        
        break;
        case 0b0010: 
            drawConPic($file_1);
        break;
        case 0b0011: 
            drawConPic2($file_1);
        break;
        
        default:
            echo 'Error: incorrect parameters extension!';
    }