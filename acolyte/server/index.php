<?php
require "vendor/autoload.php";                  //COMPOSER
require_once 'func/db_connect.php';             //DATABASE CONNECTIONS

$app = new \Slim\Slim(); 
$app->response->headers->set('Content-Type', 'application/json');

$app->group('/content', function() use($app){
    $app->map('/get', function() use($app){
        if($app->getCookie('lan') !== null)         $lan = $app->getCookie('lan');
        else                                        $app->redirect($app->urlFor('setLanguage', array('lan' =>
                                                    substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2))));
        
        $app->redirect($app->urlFor('getModified'));       
    })->via('GET', 'PUT', 'POST')->name('getContent');
    
    $app->map('/get/finished', function() use($app){
        if($app->getCookie('lan') !== null)          $lan = $app->getCookie('lan');
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
        if($app->getCookie('lan') !== null)         $lan = $app->getCookie('lan');
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
        if($app->getCookie('lan') !== null)         $lan = $app->getCookie('lan');
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
        if($app->getCookie('lan') !== null)                     $lan = $app->getCookie('lan');
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
        if($app->getCookie('lan') !== null)                     $lan = $app->getCookie('lan');
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
        if($app->getCookie('lan') !== null)                     $lan = $app->getCookie('lan');
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
            }else throw new Exception($e);
        }catch(Exception $e){
            $app->halt(503, json_encode(['type' => 'Error',
                                         'title' => 'Oops, something went wrong!',
                                         'message' => $e->getMessage()]));
        }finally{
            $db = null;
        }
        
       $app->redirect($app->urlFor('getText', array(   'category' => $category,
                                                        'element' => $element)));
        
    })->via('PUT', 'POST')->name('addText');
    
    $app->put('/save/:category/:element', function($category, $element) use($app){
        if($app->getCookie('lan') !== null)         $lan = $app->getCookie('lan');
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
        if($app->getCookie('lan') !== null)         $lan = $app->getCookie('lan');
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
    //abcdefg
    //abcd123
});

$app->group('/content/language', function() use($app){
   $app->map('/set/:lan', function($lan) use($app){
       $app->setCookie('lan', 'en', '180 days');
       $app->redirect($app->urlFor('getContent'));
   })->via('GET', 'PUT', 'POST', 'DELETE')->name('setLanguage');
});

//---------------------------------------------------------------------
$app->notFound(function () use ($app) {
    //$app->render('404.html');
});
//---------------------------------------------------------------------

$app->run();
?>