<?php
require "vendor/autoload.php";                  //COMPOSER
require_once 'func/db_connect.php';             //DATABASE CONNECTIONS
require_once 'func/base64_decode.php';          //BASE 64 IMAGE UPLOAD
require_once 'func/security_csrf.php';

$app = new \Slim\Slim(); 
$app->response->headers->set('Content-Type', 'application/json');

$app = new \Slim\Slim(array(
    'cookies.encrypt' => true,
    'cookies.secret_key' => 'acolyte-secret-key',
    'cookies.cipher' => MCRYPT_RIJNDAEL_256,
    'cookies.cipher_mode' => MCRYPT_MODE_CBC
));

$app->group('/content', function() use($app){
    $app->map('/get', function() use($app){
        if($app->getCookie('aco-lan') !== null)     $lan = $app->getCookie('aco-lan');
        else                                        $app->redirect($app->urlFor('setLanguage', array('lan' =>
                                                    substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)))); 
        

        if($app->getCookie('aco-user') !== null)    $app->redirect($app->urlFor('getModified'));
        else                                        $app->redirect($app->urlFor('getFinished'));       
        
    })->via('GET', 'PUT', 'POST', 'DELETE')->name('getContent');
    
    $app->map('/get/finished', function() use($app){
        if($app->getCookie('aco-lan') !== null)          $lan = $app->getCookie('aco-lan');
        
            if(($db = connectToMySql()) !== false){
                try{
                    $query = 'SELECT category, element, text FROM TextContent WHERE lan = ?'; 
                    $sql_text = $db->prepare($query);
                    $sql_text->bindParam(1, $lan);
                    $sql_text->execute();
                    $sql_text->setFetchMode(PDO::FETCH_OBJ);

                    $query = 'SELECT category, element, url FROM FileContent';
                    $sql_file = $db->prepare($query);
                    $sql_file->execute();
                    $sql_file->setFetchMode(PDO::FETCH_OBJ);
                    
                    /*$query = 'SELECT lan, language FROM Language WHERE toggle != 0 AND toggle IS NOT NULL';
                    $sql_lan = $db->prepare($query);
                    $sql_lan->execute();
                    $sql_lan->setFetchMode(PDO::FETCH_OBJ);*/

                    //$language = $sql_lan->fetchAll();
                    $textcontent = $sql_text->fetchAll();
                    $filecontent = $sql_file->fetchAll();
                }catch(Exception $e){
                    setupMySql($db);
                    $app->redirect($app->urlFor('getContent'));
                    $app->halt(503, json_encode([   'type' => 'Error',
                                                    'title' => 'Oops, something went wrong!',
                                                    'message' => $e->getMessage()]));
                }finally {$db = null;}
            }else{
                $app->halt(503, json_encode([ 'type' => 'Error',
                                                'title' => 'Oops, sadsomething went wrong!',
                                                'message' => 'No database connection']));
            }
        
        $app->response->status(200);
        $app->response->body(json_encode([  'lan' => $lan,
                                            //'language'  => $language,
                                            'textContent' => $textcontent, 
                                            'fileContent'=> $filecontent]));
        
    })->via('GET', 'PUT', 'POST')->name('getFinished');
    
    $app->map('/get/modified', function() use($app){
        if($app->getCookie('aco-lan') !== null)         $lan = $app->getCookie('aco-lan');
        if(($db = connectToMySql()) != false){
            try{
                $case = '';
                
                $query = 'SELECT    category, element, 
                            CASE    WHEN 	tmp_text       IS NULL      	THEN text  
                                    WHEN	tmp_text       = ?  			THEN text
                            ELSE    tmp_text
       	                    END AS  text
                            FROM    TextContent            WHERE lan = ?'; 
                $sql_text = $db->prepare($query);
                $sql_text->bindParam(1, $case);
                $sql_text->bindParam(2, $lan);
                $sql_text->execute();
                $sql_text->setFetchMode(PDO::FETCH_OBJ);

                $query = 'SELECT    category, element, 
                            CASE    WHEN 	tmp_url       IS NULL 	       THEN url
		                            WHEN	tmp_url       = ?    	       THEN url
                            ELSE    tmp_url
       	                    END AS 	url
                            FROM    FileContent';
                $sql_file = $db->prepare($query);
                $sql_file->bindParam(1, $case);
                $sql_file->execute();
                $sql_file->setFetchMode(PDO::FETCH_OBJ);
                
                /*$query = 'SELECT lan, language, toggle, preset FROM Language';
                $sql_lan = $db->prepare($query);
                $sql_lan->execute();
                $sql_lan->setFetchMode(PDO::FETCH_OBJ);*/

                //$language = $sql_lan->fetchAll();  
                $textcontent = $sql_text->fetchAll();
                $filecontent = $sql_file->fetchAll();
            }catch(Exception $e){
                setupMySql($db); 
                $app->redirect($app->urlFor('getContent'));
                $app->halt(503, json_encode(['type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => $e->getMessage()]));
            }finally {$db = null;}
        }else{
                $app->halt(503, json_encode([   'type' => 'Error',
                                                'title' => 'Oops, sadsomething went wrong!',
                                                'message' => 'No database connection']));
        }
        
        $app->response->status(200);
        $app->response->body(json_encode([  'lan' => $lan,
                                            //'language'  => $language,
                                            'textContent' => $textcontent, 
                                            'fileContent'=> $filecontent]));
    })->via('GET', 'PUT', 'POST')->name('getModified');
    
    $app->put('/save/lan', function() use($app){
        if($app->getCookie('aco-lan') !== null)         $lan = $app->getCookie('aco-lan');
        
        if(($db = connectToMySql()) != false){
            try{
                $case = '';
                
                $query = 'UPDATE TextContent t SET t.text = t.tmp_text, t.tmp_text = NULL 
                WHERE t.tmp_text IS NOT NULL AND t.tmp_text != ? AND t.lan = ?';
                $sql_text = $db->prepare($query);
                $sql_text->bindParam(1,$case);
                $sql_text->bindParam(2,$lan);
                $sql_text->execute();
                
                $query = 'UPDATE FileContent f SET f.url = f.tmp_url, f.src = f.tmp_src, 
                f.tmp_url = NULL, f.tmp_src = NULL 
                WHERE f.tmp_url IS NOT NULL AND f.tmp_src IS NOT NULL 
                AND f.tmp_url != ? AND f.tmp_src != ?';
                $sql_file = $db->prepare($query);
                $sql_file->bindParam(1,$case);
                $sql_file->bindParam(2,$case);
                $sql_file->execute();
            }catch(Exception $e){
                $app->halt(503, json_encode(['type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => $e->getMessage()]));
            }finally{ $db = null;}
        }else{
            $app->halt(503, json_encode([ 'type' => 'Error',
                                         'title' => 'Oops, sadsomething went wrong!',
                                         'message' => 'No database connection']));
        }
    });
    
    $app->put('/save/all', function() use($app){
        if(($db = connectToMySql()) != false){
            try{
                $case = '';
                
                $query = 'UPDATE TextContent t SET t.text = t.tmp_text, t.tmp_text = NULL 
                WHERE t.tmp_text IS NOT NULL AND t.tmp_text != ?';
                $sql_text = $db->prepare($query);
                $sql_text->bindParam(1,$case);
                //$sql_text->bindParam(2,$lan);
                $sql_text->execute();
                
                $query = 'UPDATE FileContent f SET f.url = f.tmp_url, f.src = f.tmp_src, 
                f.tmp_url = NULL, f.tmp_src = NULL 
                WHERE f.tmp_url IS NOT NULL AND f.tmp_src IS NOT NULL 
                AND f.tmp_url != ? AND f.tmp_src != ?';
                $sql_file = $db->prepare($query);
                $sql_file->bindParam(1,$case);
                $sql_file->bindParam(2,$case);
                $sql_file->execute();
            }catch(Exception $e){
                $app->halt(503, json_encode(['type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => $e->getMessage()]));
            }finally{ $db = null;}
        }else{
            $app->halt(503, json_encode([ 'type' => 'Error',
                                         'title' => 'Oops, sadsomething went wrong!',
                                         'message' => 'No database connection']));
        }
    });
});

$app->group('/content/text', function() use($app){
    $app->map('/get/modified/:category/:element', function($category, $element) use($app){
        if($app->getCookie('aco-lan') !== null)         $lan = $app->getCookie('aco-lan');
       
        if(($db = connectToMySql()) != false){
             try{
                $query = 'SELECT category, element, tmp_text AS text FROM TextContent WHERE lan = ? AND category = ? AND element = ?'; 
                $sql_text = $db->prepare($query);
                $sql_text->bindParam(1, $lan);
                $sql_text->bindParam(2, $category);
                $sql_text->bindParam(3, $element);
                $sql_text->execute();
                $sql_text->setFetchMode(PDO::FETCH_OBJ);
                $result = $sql_text->fetch();
            }catch(Exception $e){
                $app->halt(503, json_encode(['type' => 'Error',
                                         'title' => 'Oops, something went wrong!',
                                         'message' => $e->getMessage()]));
            }finally{ $db = null;}
        }else{
                $app->halt(503, json_encode([   'type' => 'Error',
                                                'title' => 'Oops, sadsomething went wrong!',
                                                'message' => 'No database connection']));
        }
        
        $app->response->status(400);
        $app->response->body(json_encode([  'type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => 'The text could not be found!']));
        if(empty($result)) $app->stop();
        
        $app->response->status(200);
        $app->response->body(json_encode(['textContent' => $result]));
        
    })->via('GET', 'PUT', 'POST')->name('getText');
    
    $app->map('/edit/:category/:element', function($category, $element) use($app){
        $data = json_decode($app->request->getBody());
        if($app->getCookie('aco-lan') !== null)                     $lan = $app->getCookie('aco-lan');
        if(isset($data->text) && !empty($data->text))           $text = $data->text;
         
        if(($db = connectToMySql()) != false){
            try{
                $query = 'SELECT * FROM TextContent WHERE lan = ? AND category = ? AND element = ?'; 
                $sql_text = $db->prepare($query);
                $sql_text->bindParam(1, $lan);
                $sql_text->bindParam(2, $category);
                $sql_text->bindParam(3, $element);
                $sql_text->execute();
                $sql_text->setFetchMode(PDO::FETCH_OBJ);
                $result = $sql_text->fetch();
            }catch(Exception $e){
                $app->halt(503, json_encode(['type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => $e->getMessage()]));
            }finally{ $db = null; }
        }else{
            $app->halt(503, json_encode([   'type' => 'Error',
                                            'title' => 'Oops, sadsomething went wrong!',
                                            'message' => 'No database connection']));
        }
        

        if(!empty($result)) $app->redirect($app->urlFor('setText', array(   'category' => $category,
                                                                            'element' => $element)));
        else                $app->redirect($app->urlFor('addText', array(   'category' => $category,
                                                                            'element' => $element)));
    })->via('PUT', 'POST')->name('editText');
    
    $app->map('/set/modified/:category/:element', function($category, $element) use($app){
        $data = json_decode($app->request->getBody());
        if($app->getCookie('aco-lan') !== null)                     $lan = $app->getCookie('aco-lan');
        if(isset($data->text))                                  $text = $data->text;

        if(($db = connectToMySql()) != false){
            try{
                $query = 'UPDATE TextContent SET tmp_text = ? WHERE category = ? AND element = ? AND lan = ?';
                $sql_text = $db->prepare($query);
                $sql_text->bindParam(1, $text);
                $sql_text->bindParam(2, $category);
                $sql_text->bindParam(3, $element);
                $sql_text->bindParam(4, $lan);
                if($sql_text->execute())    $result = 1;
                else                        $result = 0;
                //$result = $sql_text->rowCount();
            }catch(Exception $e){
                $app->halt(503, json_encode(['type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => $e->getMessage()]));
            }finally{ $db = null; }
        }else{
            $app->halt(503, json_encode([   'type' => 'Error',
                                            'title' => 'Oops, sadsomething went wrong!',
                                            'message' => 'No database connection']));
        }
            
        
        $app->response->status(400);
        $app->response->body(json_encode([  'type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => 'The text could not been updated!']));
        
        if($result === 0) $app->stop();
        
        $app->redirect($app->urlFor('getText', array(   'category' => $category,
                                                        'element' => $element)));
    })->via('PUT', 'POST')->name('setText');
    
    
    $app->map('/add/modified/:category/:element', function($category, $element) use($app){
        $data = json_decode($app->request->getBody());
        if($app->getCookie('aco-lan') !== null)                     $lan = $app->getCookie('aco-lan');
        if(isset($data->text))                  $text = $data->text;

        if(($db = connectToMySql()) != false){
            try{
                $query = 'INSERT INTO TextContent(category, element, tmp_text, lan) VALUES(?,?,?,?)';
                $sql_text = $db->prepare($query);
                $sql_text->bindParam(1, $category);
                $sql_text->bindParam(2, $element);
                $sql_text->bindParam(3, $text);
                $sql_text->bindParam(4, $lan);
                $sql_text->execute();
                $result = $sql_text->rowCount();
            }catch(Exception $e){
                $app->halt(503, json_encode(['type' => 'Error',
                                             'title' => 'Oops, something went wrong!',
                                             'message' => $e->getMessage()]));
            }finally{ $db = null; }
        }else{
            $app->halt(503, json_encode([   'type' => 'Error',
                                            'title' => 'Oops, sadsomething went wrong!',
                                            'message' => 'No database connection']));
        }
        
        $app->response->status(400);
        $app->response->body(json_encode([  'type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => 'The text could not been inserted!']));
        
        if($result === 0) $app->stop();
        
        $app->redirect($app->urlFor('getText', array(   'category' => $category,
                                                        'element' => $element)));
        
    })->via('PUT', 'POST')->name('addText');
    
    $app->put('/save/:category/:element', function($category, $element) use($app){
        if($app->getCookie('aco-lan') !== null)         $lan = $app->getCookie('aco-lan');

        if(($db = connectToMySql()) != false){
            try{
                $query = 'SELECT * FROM TextContent WHERE lan = ? AND category = ? AND element = ?'; 
                $sql_text = $db->prepare($query);
                $sql_text->bindParam(1, $lan);
                $sql_text->bindParam(2, $category);
                $sql_text->bindParam(3, $element);
                $sql_text->execute();
                $sql_text->setFetchMode(PDO::FETCH_OBJ);
                if($result = $sql_text->fetch()){
                    $query = 'UPDATE TextContent SET text = ? WHERE lan = ? AND category = ? AND element = ? AND tmp_text = ?'; 
                    $sql_text = $db->prepare($query);
                    $sql_text->bindParam(1, $result->tmp_text);
                    $sql_text->bindParam(2, $lan);
                    $sql_text->bindParam(3, $category);
                    $sql_text->bindParam(4, $element);
                    $sql_text->bindParam(5, '');
                    if($sql_text->execute())    $result = 1;
                    else                        $result = 0;
                    //$result = $sql_text->rowCount();
                }
            }catch(Exception $e){
                $app->halt(503, json_encode([   'type' => 'Error',
                                             'title' => 'Oops, something went wrong!',
                                             'message' => $e->getMessage()]));
            }finally{ $db = null;}
        }else{
                $app->halt(503, json_encode([   'type' => 'Error',
                                                'title' => 'Oops, sadsomething went wrong!',
                                                'message' => 'No database connection']));
        }    
        
        $app->response->status(400);
        $app->response->body(json_encode([  'type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => 'The text could not been saved!']));
        
        if($result === 0 || empty($result)) $app->stop();
        
        $app->redirect($app->urlFor('getText', array(   'category' => $category,
                                                        'element' => $element)));
    });
    
    
    $app->put('/undo/:category/:element', function($category, $element) use($app){
        if($app->getCookie('aco-lan') !== null)         $lan = $app->getCookie('aco-lan');

        if(($db = connectToMySql()) != false){
            try{
                $query = 'SELECT * FROM TextContent WHERE lan = ? AND category = ? AND element = ?'; 
                $sql_text = $db->prepare($query);
                $sql_text->bindParam(1, $lan);
                $sql_text->bindParam(2, $category);
                $sql_text->bindParam(3, $element);
                $sql_text->execute();
                $sql_text->setFetchMode(PDO::FETCH_OBJ);
                if($result = $sql_text->fetch()){
                    $query = 'UPDATE TextContent SET tmp_text = ? WHERE lan = ? AND category = ? AND element = ?'; 
                    $sql_text = $db->prepare($query);
                    $sql_text->bindParam(1, $result->text);
                    $sql_text->bindParam(2, $lan);
                    $sql_text->bindParam(3, $category);
                    $sql_text->bindParam(4, $element);
                    if($sql_text->execute())    $result = 1;
                    else                        $result = 0;
                    //$result = $sql_text->rowCount();
                }
            }catch(Exception $e){
                $app->halt(503, json_encode([   'type' => 'Error',
                                             'title' => 'Oops, something went wrong!',
                                             'message' => $e->getMessage()]));
            }finally{ $db = null;}
        }else{
            $app->halt(503, json_encode([   'type' => 'Error',
                                         'title' => 'Oops, sadsomething went wrong!',
                                         'message' => 'No database connection']));
        }
        
        $app->response->status(400);
        $app->response->body(json_encode([  'type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => 'The text could not been undone!']));
        
        if($result === 0 || empty($result)) $app->stop();
        
        $app->redirect($app->urlFor('getText', array(   'category' => $category,
                                                        'element' => $element)));
    });
});

$app->group('/content/file', function() use($app){
    $app->map('/get/modified/:category/:element', function($category, $element) use($app){
        
        if(($db = connectToMySql()) != false){
            try{
                $query = 'SELECT category, element, tmp_url AS url FROM FileContent WHERE category = ? AND element = ?'; 
                $sql_file = $db->prepare($query);
                $sql_file->bindParam(1, $category);
                $sql_file->bindParam(2, $element);
                $sql_file->execute();
                $sql_file->setFetchMode(PDO::FETCH_OBJ);
                $result = $sql_file->fetch();
            }catch(Exception $e){
                $app->halt(503, json_encode(['type' => 'Error',
                                             'title' => 'Oops, something went wrong!',
                                             'message' => $e->getMessage()]));
            }finally{$db = null;}
        }else{
            $app->halt(503, json_encode([   'type' => 'Error',
                                         'title' => 'Oops, sadsomething went wrong!',
                                         'message' => 'No database connection']));
        }
        
        $app->response->status(400);
        $app->response->body(json_encode([  'type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => 'The File could not be found!']));
        if(empty($result)) $app->stop();
        
        $app->response->status(200);
        $app->response->body(json_encode(['fileContent' => $result]));
    })->via('GET', 'PUT', 'POST')->name('getFile');
    
    $app->map('/edit/:category/:element', function($category, $element) use($app){
        $data = json_decode($app->request->getBody());
        if(isset($data->file) && !empty($data->file))           $file = $data->file;
        
        if(($db = connectToMySql()) != false){
            try{
                $query = 'SELECT * FROM FileContent WHERE category = ? AND element = ?'; 
                $sql_file = $db->prepare($query);
                $sql_file->bindParam(1, $category);
                $sql_file->bindParam(2, $element);
                $sql_file->execute();
                $sql_file->setFetchMode(PDO::FETCH_OBJ);
                $result = $sql_file->fetch();
            }catch(Exception $e){
                $app->halt(503, json_encode(['type' => 'Error',
                                             'title' => 'Oops, something went wrong!',
                                             'message' => $e->getMessage()]));
            }finally{$db = null;}  
        }else{
            $app->halt(503, json_encode([   'type' => 'Error',
                                         'title' => 'Oops, sadsomething went wrong!',
                                         'message' => 'No database connection']));
        }

        if(!empty($result)){ 
            if(file_exists($result->tmp_src)) unlink($result->tmp_src);   
                    $app->redirect($app->urlFor('setFile', array(   'category' => $category,
                                                                    'element' => $element)));
        }else{    
            $app->redirect($app->urlFor('addFile', array(   'category' => $category,
                                                            'element' => $element)));
        }
        
    })->via('PUT', 'POST')->name('editFile');
    
    $app->map('/set/modified/:category/:element', function($category, $element) use($app){
        $data = json_decode($app->request->getBody());
        if(isset($data->file) && !empty($data->file))           $file = $data->file;
        $directory = '/acolyte/acolyte/server'.'/src/';
        
        if($db = connectToMySql()){
            try{
                if(($file = base64_decode_image($file,$directory)) !== null){
                    $query = 'UPDATE FileContent SET tmp_url = ?, tmp_src = ? WHERE category = ? AND element = ?';
                    $sql_file = $db->prepare($query);
                    $sql_file->bindParam(1,$file["url"]);
                    $sql_file->bindParam(2,$file["src"]);
                    $sql_file->bindParam(3,$category);
                    $sql_file->bindParam(4,$element);
                    $sql_file->execute();
                    $result = $sql_file->rowCount();
                }else throw new Exception($e);
            }catch(Exception $e){
                //if($file !== null) if(file_exists($file["src"])) unlink($file["src"]);
                $app->halt(503, json_encode(['type' => 'Error',
                                             'title' => 'Oops, something went wrong!',
                                             'message' => $e->getMessage()]));
            }finally{$db = null;}
        }else{
            $app->halt(503, json_encode([   'type' => 'Error',
                                         'title' => 'Oops, sadsomething went wrong!',
                                         'message' => 'No database connection']));
        }
        
        $app->response->status(400);
        $app->response->body(json_encode([  'type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => 'The File could not been updated!']));
        
        if($result === 0) {if(file_exists($file["src"])) unlink($file["src"]);$app->stop();}
        
        $app->redirect($app->urlFor('getFile', array(   'category' => $category,
                                                        'element' => $element)));
        
    })->via('PUT', 'POST')->name('setFile');
                      
    $app->map('/add/modified/:category/:element', function($category, $element) use($app){
        $data = json_decode($app->request->getBody());
        if(isset($data->file) && !empty($data->file))           $file = $data->file;
        $directory = '/acolyte/acolyte/server'.'/src/';

        if($db = connectToMySql()){
            try{
                $query = "test";
                if(($file = base64_decode_image($file,$directory)) !== null){
                    $query = 'INSERT INTO FileContent(tmp_url, tmp_src, category, element) VALUES(?,?,?,?)';
                    $sql_file = $db->prepare($query);
                    $sql_file->bindParam(1,$file["url"]);
                    $sql_file->bindParam(2,$file["src"]);
                    $sql_file->bindParam(3,$category);
                    $sql_file->bindParam(4,$element);
                    $sql_file->execute();
                    $result = $sql_file->rowCount();
                }else throw new Exception($e);
            }catch(Exception $e){
                //if($file !== null) if(file_exists($file["src"])) unlink($file["src"]);
                $app->halt(503, json_encode(['type' => 'Error',
                                             'title' => 'Oops, something went wrong! catch',
                                             'message' => $query]));
            }finally{$db = null;}
        }else{
            $app->halt(503, json_encode([   'type' => 'Error',
                                             'title' => 'Oops, sadsomething went wrong!',
                                             'message' => 'No database connection']));
        }
        
        $app->response->status(400);
        $app->response->body(json_encode([  'type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => 'The File could not been inserted!']));
        
        if($result === 0) {if(file_exists($file["src"])) unlink($file["src"]);$app->stop();}
        
        $app->redirect($app->urlFor('getFile', array(   'category' => $category,
                                                        'element' => $element)));
        
        
    })->via('PUT', 'POST')->name('addFile');
});

$app->group('/language', function() use($app){
    
    $app->map('/get', function() use($app){
        $lan = $app->getCookie('aco-lan');
        if(($db = connectToMySql()) !== false){
                try{
                    $query = 'SELECT * FROM Language';
                    $sql_lan = $db->prepare($query);
                    $sql_lan->execute();
                    $sql_lan->setFetchMode(PDO::FETCH_OBJ);
                    $language = $sql_lan->fetchAll();
                    
                    $query = 'SELECT * FROM Languages';
                    $sql_lans = $db->prepare($query);
                    $sql_lans->execute();
                    $sql_lans->setFetchMode(PDO::FETCH_OBJ);
                    $languages = $sql_lans->fetchAll();
                }catch(Exception $e){ 
                    setupMySql($db);
                    $app->redirect($app->urlFor('getLanguage'));
                    $app->halt(503, json_encode(['type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => $e->getMessage()]));
            }finally {$db = null;}
        }else{
                $app->halt(503, json_encode([   'type' => 'Error',
                                                'title' => 'Oops, sadsomething went wrong!',
                                                'message' => 'No database connection']));
        }
        $app->response->status(200);
        $app->response->body(json_encode([  'lan' => $lan,
                                            'language'  => $language,
                                            'languages' => $languages]));
    })->via('GET', 'PUT')->name('getLanguage');
    
    $app->map('/set/:lan', function($lan) use($app){
        if(($db = connectToMySql()) !== false){
                try{
                    $query = 'SELECT lan FROM Language WHERE lan = ?';
                    $sql_lan = $db->prepare($query);
                    $sql_lan->bindParam(1, $lan);
                    $sql_lan->execute();
                    $sql_lan->setFetchMode(PDO::FETCH_OBJ);
                    $result = $sql_lan->fetch();
                    
                    $query = 'SELECT lan FROM language WHERE preset != 0 AND preset IS NOT NULL'; 
                    $sql_lan = $db->prepare($query);
                    $sql_lan->execute();
                    $sql_lan->setFetchMode(PDO::FETCH_OBJ);
                    $presult = $sql_lan->fetch();
                }catch(Exception $e){ 
                $app->halt(503, json_encode(['type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => $e->getMessage()]));
            }finally {$db = null;}
        }else{
                $app->halt(503, json_encode([   'type' => 'Error',
                                                'title' => 'Oops, sadsomething went wrong!',
                                                'message' => 'No database connection']));
        }
        if(empty($result)) $app->setCookie('aco-lan', $presult->lan, '180 days');
        else $app->setCookie('aco-lan', $result->lan, '180 days');
        $app->redirect($app->urlFor('getContent'));
    })->via('GET', 'PUT')->name('setLanguage');
    
    $app->put('/set/toggle/:lan', function($lan) use($app){
        $data = json_decode($app->request->getBody());
        if(isset($data->toggle) && !empty($data->toggle))           $toggle = $data->toggle;
        if(($db = connectToMySql()) !== false){
                try{
                    $query = 'UPDATE Language SET toggle = ? WHERE lan = ?';
                    $sql_lan = $db->prepare($query);
                    $sql_lan->bindParam(1, $toggle);
                    $sql_lan->bindParam(2, $lan);
                    if($sql_lan->execute())     $result = 1;
                    else                        $result = 0;
                }catch(Exception $e){ 
                $app->halt(503, json_encode(['type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => $e->getMessage()]));
            }finally {$db = null;}
        }else{
                $app->halt(503, json_encode([   'type' => 'Error',
                                                'title' => 'Oops, sadsomething went wrong!',
                                                'message' => 'No database connection']));
        } 
        $app->response->status(400);
        $app->response->body(json_encode([  'type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => 'The text could not been saved!']));
        
        if($result === 0 || empty($result)) $app->stop();
        
        $app->redirect($app->urlFor('getLanguage'));    
    });
    
    $app->delete('/remove/all', function() use ($app){
        $lan = $app->getCookie('aco-lan');
        if(($db = connectToMySql()) !== false){
                try{
                    $query = 'DELETE FROM Language WHERE lan =?';
                    $sql_lan = $db->prepare($query);
                    $sql_lan->bindParam(1, $lan);
                    $sql_lan->execute();
                    
                    $query = 'DELETE FROM TextContent WHERE lan = ?';
                    $sql_text = $db->prepare($query);
                    $sql_text->bindParam(1, $lan);
                    $sql_text->execute();
                }catch(Exception $e){ 
                $app->halt(503, json_encode(['type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => $e->getMessage()]));
            }finally {$db = null;}
        }else{
                $app->halt(503, json_encode([   'type' => 'Error',
                                                'title' => 'Oops, sadsomething went wrong!',
                                                'message' => 'No database connection']));
        } 
        $app->deleteCookie('aco-lan');
        $app->redirect($app->urlFor('getContent'));
    });
    
    $app->delete('/remove/lan', function() use ($app){
        $lan = $app->getCookie('aco-lan');
        if(($db = connectToMySql()) !== false){
                try{
                    $query = 'DELETE FROM Language WHERE lan =?';
                    $sql_lan = $db->prepare($query);
                    $sql_lan->bindParam(1, $lan);
                    $sql_lan->execute();
                }catch(Exception $e){ 
                $app->halt(503, json_encode(['type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => $e->getMessage()]));
            }finally {$db = null;}
        }else{
                $app->halt(503, json_encode([   'type' => 'Error',
                                                'title' => 'Oops, sadsomething went wrong!',
                                                'message' => 'No database connection']));
        } 
        $app->deleteCookie('aco-lan');
        $app->redirect($app->urlFor('getContent'));
    });  
    
    $app->post('/add', function() use($app){
        $data = json_decode($app->request->getBody());
        if(isset($data->lan) && !empty($data->lan))                     $lan = $data->lan;
        if(isset($data->language) && !empty($data->language))           $language = $data->language;
        
        if(($db = connectToMySql()) !== false){
                try{
                    $query = 'INSERT INTO Language(lan, language, toggle, preset) VALUES (?, ?, 0, 0)';
                    $sql_lan = $db->prepare($query);
                    $sql_lan->bindParam(1, $lan);
                    $sql_lan->bindParam(2, $language);
                    $sql_lan->execute();
                    $result = $sql_lan->rowCount();
                }catch(Exception $e){ 
                $app->halt(503, json_encode(['type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => $e->getMessage()]));
            }finally {$db = null;}
        }else{
                $app->halt(503, json_encode([   'type' => 'Error',
                                                'title' => 'Oops, sadsomething went wrong!',
                                                'message' => 'No database connection']));
        } 
        $app->response->status(400);
        $app->response->body(json_encode([  'type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => 'The language could not been inserted!']));
        
        if($result === 0) $app->stop();
        
        $app->redirect($app->urlFor('getLanguage'));
    });
    
});





//---------------------------------------------------------------------
/*$app->notFound(function () use ($app) {
    //$app->render('404.html');
});*/
//---------------------------------------------------------------------


$app->group('/user', function() use($app){
    $app->post('/login', function() use($app){
        $data = json_decode($app->request->getBody());
        if(isset($data->username) && !empty($data->username))           $user = $data->username;
        if(isset($data->password) && !empty($data->password))           $password = $data->password;
        
        if(security_login($user, $password)){
            $app->setCookie('aco-user','acodmin');
            $app->redirect($app->urlFor('getContent'));
        }
        else{
            $app->response->status(400);
            $app->response->body(json_encode([  'type' => 'Error',
                                                'title' => 'Oops, something went wrong!',
                                                'message' => 'The Login coulld not been proceeded!']));
        }
    });
    
    $app->put('/logout', function() use($app){
        $app->deleteCookie('aco-user');
        $app->redirect($app->urlFor('getContent'));
    });
    
    $app->get('/view', function() use($app){
        $app->response->status(200);
        $app->response->body(json_encode(['user' => $app->getCookie('aco-user')]));
    });
});

$app->group('/test', function() use($app){
   $app->get('/function', function() use($app){
       $app->setCookie('test','abc', null, null, null, 'abc');
       print_r($app->getCookie('test'));
       //$path = realpath(__DIR__.'/src');
       //chmod($path, 0755);
       //print_r($path);
   }); 
});

$app->run();
?>