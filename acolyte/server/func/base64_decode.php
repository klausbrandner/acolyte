<?php
    function base64_decode_image($file,$directory){

        /*if(strpos($file, 'data:image/png;base64,') !== false){
            $file = str_replace('data:image/png;base64,','',$file);
            $file_type = '.png';
        }else{
            $file = str_replace('data:image/jpeg;base64,','',$file);
            $file_type = '.jpeg';
        }

        $file = imagecreatefromstring(base64_decode($file));      

        if($file !== false){
            do{
                $file_name = substr(sha1(rand()), 0, 10);
            }while(file_exists($directory.$file_name.$file_type));  
            $file_url = $_SERVER["DOCUMENT_ROOT"].$directory.$file_name.$file_type;

            if(imagejpeg($file, $file_url)){
                $file_array = array(
                    "url" => 'http://'.$_SERVER["HTTP_HOST"].$directory.$file_name.$file_type,
                    "src" => $_SERVER["DOCUMENT_ROOT"].$directory.$file_name.$file_type
                );
                return $file_array;
            }else return null;
        }else return null;*/
        
        if(strpos($file, 'data:image/png;base64,') !== false){
            $file_data = substr($file, strpos($file, ",") + 1);
            $file_type = '.png';
        }
        elseif(strpos($file, 'data:image/jpeg;base64,') !== false){
            $file_data = substr($file, strpos($file, ",") + 1);
            $file_type = '.jpeg';
        }else{
            $file_data = substr($file, strpos($file, ",") + 1);
            $file_type = '.jpg';
        }

        if($image = imagecreatefromstring(base64_decode($file_data))){
            do{
                $file_name = substr(sha1(rand()), 0, 10);
            }while(file_exists($directory.$file_name.$file_type));  
            
            $url_src = array(
                    "url" => 'http://'.$_SERVER["HTTP_HOST"].$directory.$file_name.$file_type,
                    "src" => $_SERVER["DOCUMENT_ROOT"].$directory.$file_name.$file_type
            );
            
            switch($file_type){
                case ".png":
                    if(imagepng($image,"src/".$file_name.$file_type)) return $url_src;
                case ".jpeg":
                    if(imagejpeg($image,"src/".$file_name.$file_type)) return $url_src;
                default: 
                    if(imagejpeg($image,"src/".$file_name.$file_type)) return $url_src;
            }
        }
        
        return null;

        
        
        /*$format = "jpeg";
        $dataAvatar = substr($file, strpos($file, ",") + 1);
        if($sourceAvatar = imagecreatefromstring(base64_decode($dataAvatar))){
            do{
                $avatar = substr(sha1(rand()), 0, 10);
            }while(file_exists($directory.$avatar.$format));
            $avatar = $avatar.$format;
            $avatar = "avatar1.jpg";
            if(imagejpeg($sourceAvatar,$avatar)){
                $array = array(
                    "url" => 'http://'.$_SERVER["HTTP_HOST"].$directory.$avatar,
                    "src" => $_SERVER["DOCUMENT_ROOT"].$directory.$avatar
                );
            }else return null;
        }else return null;
        return null;*/
        
    }
?>