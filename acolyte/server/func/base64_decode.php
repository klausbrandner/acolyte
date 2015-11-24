<?php
function base64_decode_image($file,$directory){
    
    if(strpos($file, 'data:image/png;base64,') !== false){
        $file = str_replace('data:image/png;base64,','',$file);
        $file_type = '.png';
    }elseif(strpos($file, 'data:image/gif;base64,') !== false){
        $file = str_replace('data:image/gif;base64,','',$file);
        $file_type = '.gif';
    }elseif(strpos($file, 'data:image/bmp;base64,') !== false){
        $file = str_replace('data:image/bmp;base64,','',$file);
        $file_type = '.bmp';
    }else{
        $file = str_replace('data:image/jpeg;base64,','',$file);
        $file_type = '.jpeg';
    }
    
    $file = base64_decode($file);
   
    
    if($file !== false){
        do{
            $file_name = substr(sha1(rand()), 0, 10);
        }while(file_exists($directory.$file_name.$file_type));  
        $file_url = $_SERVER["DOCUMENT_ROOT"].$directory.$file_name.$file_type;

        if(file_put_contents($file_url, $file)){
            $file_array = array(
                "url" => 'http://'.$_SERVER["HTTP_HOST"].$directory.$file_name.$file_type,
                "src" => $_SERVER["DOCUMENT_ROOT"].$directory.$file_name.$file_type
            );
            return $file_array;
        }else return null;
    }else return null;
}