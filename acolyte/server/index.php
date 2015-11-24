<?php
require "vendor/autoload.php";                  //COMPOSER
require_once 'func/db_connect.php';             //DATABASE CONNECTIONS
require_once 'func/base64_decode.php';

$app = new \Slim\Slim(); 
$app->response->headers->set('Content-Type', 'application/json');

$app->group('/content', function() use($app){
    $app->map('/get', function() use($app){
        if($app->getCookie('aco-lan') !== null)         $lan = $app->getCookie('aco-lan');
        else                                        $app->redirect($app->urlFor('setLanguage', array('lan' =>
                                                    substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2))));      

        if($app->getCookie('aco-user') !== null)    $app->redirect($app->urlFor('getModified'));
        else                                        $app->redirect($app->urlFor('getFinished'));       
        
    })->via('GET', 'PUT', 'POST')->name('getContent');
    
    $app->map('/get/finished', function() use($app){
        if($app->getCookie('aco-lan') !== null)          $lan = $app->getCookie('aco-lan');
        try{
            if(($db = connectTo5Design()) != false){
                $query = 'SELECT category, element, text FROM TextContent WHERE lan = ?'; 
                $sql_text = $db->prepare($query);
                $sql_text->bindParam(1, $lan);
                $sql_text->execute();
                $sql_text->setFetchMode(PDO::FETCH_OBJ);

                $query = 'SELECT category, element, url FROM filecontent';
                $sql_file = $db->prepare($query);
                $sql_file->execute();
                $sql_file->setFetchMode(PDO::FETCH_OBJ);
                
                $query = 'SELECT lan, language FROM language';
                $sql_lan = $db->prepare($query);
                $sql_lan->execute();
                $sql_lan->setFetchMode(PDO::FETCH_OBJ);
                
                $textcontent = $sql_text->fetchAll();
                $filecontent = $sql_file->fetchAll();
                $language = $sql_lan->fetchAll();
            }else throw new Exception($e);
        }catch(Exception $e){
            $app->halt(503, json_encode(['type' => 'Error',
                                         'title' => 'Oops, something went wrong!',
                                         'message' => $e->getMessage()]));
        }finally{
            $db = null;
        }
        
        $app->response->status(200);
        $app->response->body(json_encode([  'lan' => $lan,
                                            'language' => $language,
                                            'textContent' => $textcontent, 
                                            'fileContent'=> $filecontent]));
        
    })->via('GET', 'PUT', 'POST')->name('getFinished');
    
    $app->map('/get/modified', function() use($app){
        if($app->getCookie('aco-lan') !== null)         $lan = $app->getCookie('aco-lan');
        try{
            if(($db = connectTo5Design()) != false){
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
                            FROM    Filecontent';
                $sql_file = $db->prepare($query);
                $sql_file->bindParam(1, $case);
                $sql_file->execute();
                $sql_file->setFetchMode(PDO::FETCH_OBJ);
                
                $query = 'SELECT lan, language FROM language';
                $sql_lan = $db->prepare($query);
                $sql_lan->execute();
                $sql_lan->setFetchMode(PDO::FETCH_OBJ);
                
                $textcontent = $sql_text->fetchAll();
                $filecontent = $sql_file->fetchAll();
                $language = $sql_lan->fetchAll();
            }else throw new Exception($e);
        }catch(Exception $e){
            $app->halt(503, json_encode(['type' => 'Error',
                                         'title' => 'Oops, something went wrong!',
                                         'message' => $e->getMessage()]));
        }finally{
            $db = null;
        }
        
        $app->response->status(200);
        $app->response->body(json_encode([  'lan' => $lan,
                                            'language' => $language,
                                            'textContent' => $textcontent, 
                                            'fileContent'=> $filecontent]));
    })->via('GET', 'PUT', 'POST')->name('getModified');
    
    $app->put('/save', function() use($app){
    });
});

$app->group('/content/text', function() use($app){
    $app->map('/get/modified/:category/:element', function($category, $element) use($app){
        if($app->getCookie('aco-lan') !== null)         $lan = $app->getCookie('aco-lan');
        try{
            if(($db = connectTo5Design()) != false){
                $query = 'SELECT category, element, tmp_text AS text FROM TextContent WHERE lan = ? AND category = ? AND element = ?'; 
                $sql_text = $db->prepare($query);
                $sql_text->bindParam(1, $lan);
                $sql_text->bindParam(2, $category);
                $sql_text->bindParam(3, $element);
                $sql_text->execute();
                $sql_text->setFetchMode(PDO::FETCH_OBJ);
                $result = $sql_text->fetch();
            }else throw new Exception($e);
        }catch(Exception $e){
            $app->halt(503, json_encode(['type' => 'Error',
                                         'title' => 'Oops, something went wrong!',
                                         'message' => $e->getMessage()]));
        }finally{
            $db = null;
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
    
        try{
            if(($db = connectTo5Design()) != false){
                $query = 'SELECT * FROM TextContent WHERE lan = ? AND category = ? AND element = ?'; 
                $sql_text = $db->prepare($query);
                $sql_text->bindParam(1, $lan);
                $sql_text->bindParam(2, $category);
                $sql_text->bindParam(3, $element);
                $sql_text->execute();
                $sql_text->setFetchMode(PDO::FETCH_OBJ);
                $result = $sql_text->fetch();
            }else throw new Exception($e);
        }catch(Exception $e){
            $app->halt(503, json_encode(['type' => 'Error',
                                         'title' => 'Oops, something went wrong!',
                                         'message' => $e->getMessage()]));
        }finally{
            $db = null;
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
        try{
            if(($db = connectTo5Design()) != false){
                $query = 'UPDATE TextContent SET tmp_text = ? WHERE category = ? AND element = ? AND lan = ?';
                $sql_text = $db->prepare($query);
                $sql_text->bindParam(1, $text);
                $sql_text->bindParam(2, $category);
                $sql_text->bindParam(3, $element);
                $sql_text->bindParam(4, $lan);
                if($sql_text->execute())    $result = 1;
                else                        $result = 0;
                //$result = $sql_text->rowCount();
            }else throw new Exception($e);
        }catch(Exception $e){
            $app->halt(503, json_encode(['type' => 'Error',
                                         'title' => 'Oops, something went wrong!',
                                         'message' => $e->getMessage()]));
        }finally{
            $db = null;
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
        if(isset($data->text) && !empty($data->text))           $text = $data->text;
        try{
            if(($db = connectTo5Design()) != false){
                $query = 'INSERT INTO TextContent(category, element, tmp_text, lan) VALUES(?,?,?,?)';
                $sql_text = $db->prepare($query);
                $sql_text->bindParam(1, $category);
                $sql_text->bindParam(2, $element);
                $sql_text->bindParam(3, $text);
                $sql_text->bindParam(4, $lan);
                $sql_text->execute();
                $result = $sql_text->rowCount();
            }else throw new Exception($e);
        }catch(Exception $e){
            $app->halt(503, json_encode(['type' => 'Error',
                                         'title' => 'Oops, something went wrong!',
                                         'message' => $e->getMessage()]));
        }finally{
            $db = null;
        }
        
        $app->response->status(400);
        $app->response->body(json_encode([  'type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => 'The text could not been updated!']));
        
        if($result === 0) $app->stop();
        
        $app->redirect($app->urlFor('getText', array(   'category' => $category,
                                                        'element' => $element)));
        
    })->via('PUT', 'POST')->name('addText');
    
    $app->put('/save/:category/:element', function($category, $element) use($app){
        if($app->getCookie('aco-lan') !== null)         $lan = $app->getCookie('aco-lan');
        try{
            if(($db = connectTo5Design()) != false){
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
            }else throw new Exception($e);
        }catch(Exception $e){
            $app->halt(503, json_encode([   'type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => $e->getMessage()]));
        }finally{
            $db = null;
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
        try{
            if(($db = connectTo5Design()) != false){
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
            }else throw new Exception($e);
        }catch(Exception $e){
            $app->halt(503, json_encode([   'type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => $e->getMessage()]));
        }finally{
            $db = null;
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
        try{
            if(($db = connectTo5Design()) != false){
                $query = 'SELECT category, element, tmp_url AS url FROM FileContent WHERE category = ? AND element = ?'; 
                $sql_file = $db->prepare($query);
                $sql_file->bindParam(1, $category);
                $sql_file->bindParam(2, $element);
                $sql_file->execute();
                $sql_file->setFetchMode(PDO::FETCH_OBJ);
                $result = $sql_file->fetch();
            }else throw new Exception($e);
        }catch(Exception $e){
            $app->halt(503, json_encode(['type' => 'Error',
                                         'title' => 'Oops, something went wrong!',
                                         'message' => $e->getMessage()]));
        }finally{
            $db = null;
        }
        
        $app->response->status(400);
        $app->response->body(json_encode([  'type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => 'The text could not be found!']));
        if(empty($result)) $app->stop();
        
        $app->response->status(200);
        $app->response->body(json_encode(['fileContent' => $result]));
    })->via('GET', 'PUT', 'POST')->name('getFile');
    
    $app->map('/edit/:category/:element', function($category, $element) use($app){
        $data = json_decode($app->request->getBody());
        if(isset($data->file) && !empty($data->file))           $file = $data->file;
        try{
            if(($db = connectTo5Design()) != false){
                $query = 'SELECT * FROM FileContent WHERE category = ? AND element = ?'; 
                $sql_file = $db->prepare($query);
                $sql_file->bindParam(1, $category);
                $sql_file->bindParam(2, $element);
                $sql_file->execute();
                $sql_file->setFetchMode(PDO::FETCH_OBJ);
                $result = $sql_file->fetch();
            }else throw new Exception($e);
        }catch(Exception $e){
            $app->halt(503, json_encode(['type' => 'Error',
                                         'title' => 'Oops, something went wrong!',
                                         'message' => $e->getMessage()]));
        }finally{
            $db = null;
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
        /*if(true){
            $file = 'data:image/png;base64,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';
        } */
        $directory = '/acolyte/acolyte/server'.'/src/';
        try{
            if($db = connectTo5Design()){
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
            }else throw new Exception($e);
        }catch(Exception $e){
            //if($file !== null) if(file_exists($file["src"])) unlink($file["src"]);
            $app->halt(503, json_encode(['type' => 'Error',
                                         'title' => 'Oops, something went wrong!',
                                         'message' => $e->getMessage()]));
        }finally{
            $db = null;
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
        /*if(true){
            $file = 'data:image/png;base64,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';
        }*/
        $directory = '/acolyte/acolyte/server'.'/src/';
        try{
            if($db = connectTo5Design()){
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
            }else throw new Exception($e);
        }catch(Exception $e){
            //if($file !== null) if(file_exists($file["src"])) unlink($file["src"]);
            $app->halt(503, json_encode(['type' => 'Error',
                                         'title' => 'Oops, something went wrong!',
                                         'message' => $e->getMessage()]));
        }finally{
            $db = null;
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

$app->group('/content/language', function() use($app){
   $app->map('/set/:lan', function($lan) use($app){
       $app->setCookie('aco-lan', 'en', '180 days');
       $app->redirect($app->urlFor('getContent'));
   })->via('GET', 'PUT', 'POST', 'DELETE')->name('setLanguage');
});





//---------------------------------------------------------------------
/*$app->notFound(function () use ($app) {
    //$app->render('404.html');
});*/
//---------------------------------------------------------------------


$app->group('/user', function() use($app){
    $app->post('/login', function() use($app){
       $app->setCookie('aco-user','1');
    });
    
    $app->get('/view', function() use($app){
        $app->response->status(200);
        $app->response->body(json_encode(['user' => $app->getCookie('aco-user')]));
    });
});

$app->run();
?>