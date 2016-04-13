<?php
require "vendor/autoload.php";                  //COMPOSER
require_once 'func/db_connect.php';             //DATABASE CONNECTIONS
require_once 'func/base64_decode.php';          //BASE 64 IMAGE UPLOAD
require_once 'func/security_csrf.php';          //SECURITY
require_once 'settings.php';                    //SETTINGS

$app = new \Slim\Slim(array(
    'cookies.encrypt' => COOKIECRYPT,
    'cookies.secret_key' => COOKIEKEY,
    'cookies.cipher' => MCRYPT_RIJNDAEL_256,
    'cookies.cipher_mode' => MCRYPT_MODE_CBC
));

$app->response->headers->set('Content-Type', 'application/json');

$app->group('/content', function() use($app){
    $app->response->headers->set('Content-Type', 'application/json');

    $app->map('/get', function() use($app){
        //if(isset($data->token) && security_token($token)){
        //if(security_token($token)){
            if($app->getCookie('aco-lan') !== null)     $lan = $app->getCookie('aco-lan');
            else                                        $app->redirect($app->urlFor('setLanguage', array('lan' =>
                                                        substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2))));

            if($app->getCookie('aco-user') !== null)    $app->redirect($app->urlFor('getModified'));
            else                                        $app->redirect($app->urlFor('getFinished'));
        /*}else{
            $app->halt(403, json_encode([   'type' => 'error',
                                            'title' => 'Forbidden Request',
                                            'message' => 'You do not have the permission to call this request.']));
        }*/

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

                    $query = 'SELECT category, element, url FROM FileContent WHERE lan = ?';
                    $sql_file = $db->prepare($query);
                    $sql_file->bindParam(1, $lan);
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

    })->via('GET', 'PUT', 'POST', 'DELETE')->name('getFinished');

    $app->map('/get/modified', function() use($app){
        if($app->getCookie('aco-lan') !== null)         $lan = $app->getCookie('aco-lan');
        if(($db = connectToMySql()) !== false){
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
                            FROM    FileContent           WHERE lan = ?';
                $sql_file = $db->prepare($query);
                $sql_file->bindParam(1, $case);
                $sql_file->bindParam(2, $lan);
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
    })->via('GET', 'PUT', 'POST', 'DELETE')->name('getModified');

    $app->put('/save/lan', function() use($app){
        if($app->getCookie('aco-lan') !== null)         $lan = $app->getCookie('aco-lan');

        if(isset($data->token) && security_token($data->token)){
            if(($db = connectToMySql()) !== false){
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
                    AND f.tmp_url != ? AND f.tmp_src != ? AND f.lan = ?';
                    $sql_file = $db->prepare($query);
                    $sql_file->bindParam(1,$case);
                    $sql_file->bindParam(2,$case);
                    $sql_file->bindParam(3,$lan);
                    $sql_file->execute();
                }catch(Exception $e){
                    $app->halt(503, json_encode(['type' => 'error',
                                                'title' => 'Oops, something went wrong!',
                                                'message' => $e->getMessage()]));
                }finally{ $db = null;}
            }else{
                $app->halt(503, json_encode([ 'type' => 'error',
                                             'title' => 'Oops, something went wrong!',
                                             'message' => 'No database connection']));
            }
        }else{
            $app->halt(403, json_encode([   'type' => 'error',
                                            'title' => 'Forbidden Request',
                                            'message' => 'You do not have the permission to call this request.']));
        }
    });

    $app->put('/save/all', function() use($app){
        if(isset($data->token) && security_token($data->token)){
            if(($db = connectToMySql()) !== false){
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
                    $app->halt(503, json_encode(['type' => 'error',
                                                'title' => 'Oops, something went wrong!',
                                                'message' => $e->getMessage()]));
                }finally{ $db = null;}
            }else{
                $app->halt(503, json_encode([ 'type' => 'error',
                                             'title' => 'Oops, something went wrong!',
                                             'message' => 'No database connection']));
            }
        }else{
            $app->halt(403, json_encode([   'type' => 'error',
                                            'title' => 'Forbidden Request',
                                            'message' => 'You do not have the permission to call this request.']));
        }
    });
});

$app->group('/content/text', function() use($app){
    $app->response->headers->set('Content-Type', 'application/json');

    $app->map('/get/modified/:category/:element', function($category, $element) use($app){
        if($app->getCookie('aco-lan') !== null)         $lan = $app->getCookie('aco-lan');

        if(($db = connectToMySql()) !== false){
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
                                                'title' => 'Oops, something went wrong!',
                                                'message' => 'No database connection']));
        }

        $app->response->status(400);
        $app->response->body(json_encode([  'type' => 'Error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => 'We could not get the text content!']));
        if(empty($result)) $app->stop();

        $app->response->status(200);
        $app->response->body(json_encode(['textContent' => $result]));

    })->via('GET', 'PUT', 'POST')->name('getText');

    $app->map('/edit/:category/:element', function($category, $element) use($app){
        $data = json_decode($app->request->getBody());
        if($app->getCookie('aco-lan') !== null)                     $lan = $app->getCookie('aco-lan');
        if(isset($data->text))                                      $text = $data->text;
        if(isset($data->token) && security_token($data->token)){
            if(($db = connectToMySql()) !== false){
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
                                                'title' => 'Oops, something went wrong!',
                                                'message' => 'No database connection']));
            }


            if(!empty($result)) $app->redirect($app->urlFor('setText', array(   'category' => $category,
                                                                                'element' => $element)));
            else                $app->redirect($app->urlFor('addText', array(   'category' => $category,
                                                                                'element' => $element)));
        }else{
            $app->halt(403, json_encode([   'type' => 'error',
                                            'title' => 'Forbidden Request',
                                            'message' => 'You do not have the permission to call this request.']));
        }
    })->via('PUT', 'POST')->name('editText');

    $app->map('/set/modified/:category/:element', function($category, $element) use($app){
        $data = json_decode($app->request->getBody());
        if($app->getCookie('aco-lan') !== null)                     $lan = $app->getCookie('aco-lan');
        if(isset($data->text))                                      $text = $data->text;
        
        if(isset($data->token) && security_token($data->token)){
            if(($db = connectToMySql()) !== false){
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
                                                'title' => 'Oops, something went wrong!',
                                                'message' => 'No database connection']));
            }


            $app->response->status(400);
            $app->response->body(json_encode([  'type' => 'Error',
                                                'title' => 'Oops, something went wrong!',
                                                'message' => 'The text could not be updated!']));

            if($result === 0) $app->stop();

            $app->redirect($app->urlFor('getText', array(   'category' => $category,
                                                            'element' => $element)));
        }else{
            $app->halt(403, json_encode([   'type' => 'error',
                                            'title' => 'Forbidden Request',
                                            'message' => 'You do not have the permission to call this request.']));
        }
    })->via('PUT', 'POST')->name('setText');

    $app->map('/add/modified/:category/:element', function($category, $element) use($app){
        $data = json_decode($app->request->getBody());
        if($app->getCookie('aco-lan') !== null)                     $lan = $app->getCookie('aco-lan');
        if(isset($data->text))                                      $text = $data->text;

         if(isset($data->token) && security_token($data->token)){
            if(($db = connectToMySql()) !== false){
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
                                                'title' => 'Oops, something went wrong!',
                                                'message' => 'No database connection']));
            }

            $app->response->status(400);
            $app->response->body(json_encode([  'type' => 'Error',
                                                'title' => 'Oops, something went wrong!',
                                                'message' => 'The text could not be inserted!']));

            if($result === 0) $app->stop();

            $app->redirect($app->urlFor('getText', array(   'category' => $category,
                                                            'element' => $element)));
        }else{
            $app->halt(403, json_encode([   'type' => 'error',
                                            'title' => 'Forbidden Request',
                                            'message' => 'You do not have the permission to call this request.']));
        }

    })->via('PUT', 'POST')->name('addText');
});

$app->group('/content/file', function() use($app){
    $app->response->headers->set('Content-Type', 'application/json');

    $app->map('/get/modified/:category/:element', function($category, $element) use($app){
        if($app->getCookie('aco-lan') !== null)         $lan = $app->getCookie('aco-lan');

        if(($db = connectToMySql()) !== false){
            try{
                $query = 'SELECT category, element, tmp_url AS url FROM FileContent WHERE category = ? AND element = ? AND lan = ?';
                $sql_file = $db->prepare($query);
                $sql_file->bindParam(1, $category);
                $sql_file->bindParam(2, $element);
                $sql_file->bindParam(3, $lan);
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
                                         'title' => 'Oops, something went wrong!',
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
        if($app->getCookie('aco-lan') !== null) $lan = $app->getCookie('aco-lan');
        if(!empty($app->request()->post('token')) && security_csrf($app->request()->post('token'))){
            if(isset($_FILES['image'])){
                if(($db = connectToMySql()) !== false){

                    // create image variables
                    $dirname = dirname(__FILE__);
                    $server = $_SERVER["DOCUMENT_ROOT"];
                    $directory = str_replace($server,'',$dirname).'/src/';
                    $image_name = date('dmy-his', time()).'_'.substr(sha1(rand()), 0, 5).str_replace(' ','',$_FILES['image']['name']);
                    $image_path = $_SERVER["DOCUMENT_ROOT"].$directory.$image_name;
                    $image_url = PROTOCOL.'://'.$_SERVER["HTTP_HOST"].$directory.$image_name;

                    // Upload Image
                    if(move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)){

                        /*
                            Check if already exists
                        */
                        try{
                            $query = 'SELECT * FROM FileContent WHERE category = ? AND element = ? AND lan = ?';
                            $sql_file = $db->prepare($query);
                            $sql_file->bindParam(1, $category);
                            $sql_file->bindParam(2, $element);
                            $sql_file->bindParam(3, $lan);
                            $sql_file->execute();
                            $sql_file->setFetchMode(PDO::FETCH_OBJ);
                            $result = $sql_file->fetch();
                        }catch(Exception $e){
                            $app->halt(503, json_encode(['type' => 'Error',
                                                         'title' => 'An error occured',
                                                         'message' => $e->getMessage()]));
                        }

                        // If result is not empty -> UPDATE, else INSERT
                        if(!empty($result)){
                            /*
                                UPDATE IMAGE and delete old image from server
                            */
                            try{
                                if(file_exists($result->tmp_src)) unlink($result->tmp_src);

                                $query = 'UPDATE FileContent SET tmp_url = ?, tmp_src = ? WHERE category = ? AND element = ?';
                                $sql_file = $db->prepare($query);
                                $sql_file->bindParam(1,$image_url);
                                $sql_file->bindParam(2,$image_path);
                                $sql_file->bindParam(3,$category);
                                $sql_file->bindParam(4,$element);
                                $sql_file->execute();
                                $result = $sql_file->rowCount();
                            }catch(Exception $e){
                                $app->halt(503, json_encode(['type' => 'Error',
                                                             'title' => 'An error occured!',
                                                             'message' => $e->getMessage()]));
                            }

                        }else{
                            /*
                                INSERT IMAGE
                            */
                            try{
                                $query = 'INSERT INTO FileContent(tmp_url, tmp_src, category, element, lan) VALUES(?,?,?,?,?)';
                                $sql_file = $db->prepare($query);
                                $sql_file->bindParam(1,$image_url);
                                $sql_file->bindParam(2,$image_path);
                                $sql_file->bindParam(3,$category);
                                $sql_file->bindParam(4,$element);
                                $sql_file->bindParam(5,$lan);
                                $sql_file->execute();
                                $result = $sql_file->rowCount();
                            }catch(Exception $e){
                                $app->halt(503, json_encode(['type' => 'Error',
                                                             'title' => 'An error occured!',
                                                             'message' => $e->getMessage()]));
                            }
                        }
                    }


                }else{
                    $app->halt(503, json_encode([   'type' => 'Error',
                                                 'title' => 'Oops, something went wrong!',
                                                 'message' => 'No database connection']));
                }
            }else{
                $app->halt(503, json_encode(['type' => 'Error',
                                             'title' => 'No Image',
                                             'message' => 'This request does not contain an image to upload.']));
            }

            // If everything succcessful -> redirect to get the image
            $app->redirect($app->urlFor('getFile', array('category' => $category,'element' => $element)));
        }else{
            $app->halt(403, json_encode([   'type' => 'error',
                                            'title' => 'Forbidden Request',
                                            'message' => 'You do not have the permission to call this request.']));
        }

    })->via('PUT', 'POST')->name('editFile');
});

$app->group('/language', function() use($app){
    $app->response->headers->set('Content-Type', 'application/json');

    $app->map('/get', function() use($app){
        if($app->getCookie('aco-lan') !== null)         $lan = $app->getCookie('aco-lan');
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
                    $app->halt(503, json_encode(['type' => 'error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => $e->getMessage()]));
            }finally {$db = null;}
        }else{
                $app->halt(503, json_encode([   'type' => 'error',
                                                'title' => 'Oops, something went wrong!',
                                                'message' => 'No database connection']));
        }
        $app->response->status(200);
        $app->response->body(json_encode([  'lan' => $lan,
                                            'language'  => $language,
                                            'languages' => $languages]));
    })->via('GET', 'PUT', 'DELETE')->name('getLanguage');

    $app->map('/set/:lan', function($lan) use($app){
        if(($db = connectToMySql()) !== false){
                try{
                    $query = 'SELECT lan FROM Language WHERE lan = ?';
                    $sql_lan = $db->prepare($query);
                    $sql_lan->bindParam(1, $lan);
                    $sql_lan->execute();
                    $sql_lan->setFetchMode(PDO::FETCH_OBJ);
                    $result = $sql_lan->fetch();

                    $query = 'SELECT lan FROM Language WHERE preset != 0 AND preset IS NOT NULL';
                    $sql_lan = $db->prepare($query);
                    $sql_lan->execute();
                    $sql_lan->setFetchMode(PDO::FETCH_OBJ);
                    $presult = $sql_lan->fetch();
                }catch(Exception $e){
                $app->halt(503, json_encode(['type' => 'error',
                                            'title' => 'Oops, something went wrong!',
                                            'message' => $e->getMessage()]));
            }finally {$db = null;}
        }else{
                $app->halt(503, json_encode([   'type' => 'error',
                                                'title' => 'Oops, something went wrong!',
                                                'message' => 'No database connection']));
        }
        if(empty($result)) $app->setCookie('aco-lan', $presult->lan, '180 days');
        else $app->setCookie('aco-lan', $result->lan, '180 days');
        $app->redirect($app->urlFor('getContent'));
    })->via('GET', 'PUT', 'DELETE')->name('setLanguage');

    $app->put('/set/toggle/:lan', function($lan) use($app){
        $data = json_decode($app->request->getBody());
        if(isset($data->toggle))           $toggle = $data->toggle;
        if(isset($data->token) && security_token($data->token)){
            if(($db = connectToMySql()) !== false){
                    try{
                        $query = 'UPDATE Language SET toggle = ? WHERE lan = ?';
                        $sql_lan = $db->prepare($query);
                        $sql_lan->bindParam(1, $toggle);
                        $sql_lan->bindParam(2, $lan);
                        if($sql_lan->execute())     $result = 1;
                        else                        $result = 0;
                    }catch(Exception $e){
                    $app->halt(503, json_encode(['type' => 'error',
                                                'title' => 'Oops, something went wrong!',
                                                'message' => $e->getMessage()]));
                }finally {$db = null;}
            }else{
                    $app->halt(503, json_encode([   'type' => 'error',
                                                    'title' => 'Oops, sadsomething went wrong!',
                                                    'message' => 'No database connection']));
            }
            $app->response->status(400);
            $app->response->body(json_encode([  'type' => 'error',
                                                'title' => 'Oops, something went wrong!',
                                                'message' => 'Language could not be activated!']));

            if($result === 0 || empty($result)) $app->stop();

            $app->redirect($app->urlFor('getLanguage'));
        }else{
            $app->halt(403, json_encode([   'type' => 'error',
                                            'title' => 'Forbidden Request',
                                            'message' => 'You do not have the permission to call this request.']));
        }
    });

    $app->delete('/remove/all/:lan', function($lan) use ($app){
        if(isset($data->token) && security_token($data->token)){
            if(($db = connectToMySql()) !== false){
                    try{
                        $query = 'DELETE FROM FileContent WHERE lan = ?';
                        $sql_file = $db->prepare($query);
                        $sql_file->bindParam(1, $lan);
                        $sql_file->execute();

                        $query = 'DELETE FROM TextContent WHERE lan = ?';
                        $sql_text = $db->prepare($query);
                        $sql_text->bindParam(1, $lan);
                        $sql_text->execute();

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
                                                    'title' => 'Oops, something went wrong!',
                                                    'message' => 'No database connection']));
            }
            if($app->getCookie('aco-lan') === $lan){
                $app->deleteCookie('aco-lan');
                $app->redirect($app->urlFor('getContent'));
            }else{
                $app->redirect($app->urlFor('getContent'));
            }
        }else{
            $app->halt(403, json_encode([   'type' => 'error',
                                            'title' => 'Forbidden Request',
                                            'message' => 'You do not have the permission to call this request.']));
        }
    });

    $app->delete('/remove/lan/:lan', function($lan) use ($app){
         if(isset($data->token) && security_token($data->token)){
            if(($db = connectToMySql()) !== false){
                    try{
                        $query = 'DELETE FROM Language WHERE lan = ?';
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
                                                    'title' => 'Oops, something went wrong!',
                                                    'message' => 'No database connection']));
            }
            if($app->getCookie('aco-lan') === $lan){
                $app->deleteCookie('aco-lan');
                $app->redirect($app->urlFor('getContent'));
            }else{
                $app->redirect($app->urlFor('getContent'));
            }
         }else{
            $app->halt(403, json_encode([   'type' => 'error',
                                            'title' => 'Forbidden Request',
                                            'message' => 'You do not have the permission to call this request.']));
        }
    });

    $app->post('/add', function() use($app){
        $data = json_decode($app->request->getBody());
        if(isset($data->lan) && !empty($data->lan))                     $lan = $data->lan;
        if(isset($data->language) && !empty($data->language))           $language = $data->language;

        if(isset($data->token) && security_token($data->token)){
            if(($db = connectToMySql()) !== false){
                    try{
                        $query = 'INSERT INTO Language(lan, language, toggle, preset) VALUES (?, ?, 0, 0)';
                        $sql_lan = $db->prepare($query);
                        $sql_lan->bindParam(1, $lan);
                        $sql_lan->bindParam(2, $language);
                        $sql_lan->execute();
                        $result = $sql_lan->rowCount();

                        $query = 'SELECT * FROM Language WHERE preset = 1';
                        $sql_preset = $db->prepare($query);
                        $sql_preset->execute();
                        $sql_preset->setFetchMode(PDO::FETCH_OBJ);
                        $preset = $sql_preset->fetch()->lan;

                        $query = 'SELECT * FROM TextContent WHERE lan = ?';
                        $sql_text = $db->prepare($query);
                        $sql_text->bindParam(1, $preset);
                        $sql_text->execute();
                        $sql_text->setFetchMode(PDO::FETCH_OBJ);
                        $texts = $sql_text->fetchAll();

                        $query = 'SELECT * FROM FileContent WHERE lan = ?';
                        $sql_file = $db->prepare($query);
                        $sql_file->bindParam(1, $preset);
                        $sql_file->execute();
                        $sql_file->setFetchMode(PDO::FETCH_OBJ);
                        $files = $sql_file->fetchAll();

                        foreach($texts as $text){
                            $query = 'SELECT * FROM TextContent WHERE category = ? AND element = ? AND lan =?';
                            $sql_select_text = $db->prepare($query);
                            $sql_select_text->bindParam(1,$text->category);
                            $sql_select_text->bindParam(2,$text->element);
                            $sql_select_text->bindParam(3,$lan);
                            $sql_select_text->execute();
                            $sql_select_text->setFetchMode(PDO::FETCH_OBJ);
                            $selectedText = $sql_select_text->fetchAll();

                            if(count($selectedText) === 0){
                                $query = 'INSERT INTO TextContent(category, element, text, lan, tmp_text) VALUES (?,?,?,?,?)';
                                $sql_insert_text = $db->prepare($query);
                                $sql_insert_text->bindParam(1,$text->category);
                                $sql_insert_text->bindParam(2,$text->element);
                                $sql_insert_text->bindParam(3,$text->text);
                                $sql_insert_text->bindParam(4,$lan);
                                $sql_insert_text->bindParam(5,$text->tmp_text);
                                $sql_insert_text->execute();
                            }
                        }

                        foreach($files as $file){
                            $query = 'SELECT * FROM FileContent WHERE category = ? AND element = ? AND lan =?';
                            $sql_select_file = $db->prepare($query);
                            $sql_select_file->bindParam(1,$file->category);
                            $sql_select_file->bindParam(2,$file->element);
                            $sql_select_file->bindParam(3,$lan);
                            $sql_select_file->execute();
                            $sql_select_file->setFetchMode(PDO::FETCH_OBJ);
                            $selectedFile = $sql_select_file->fetchAll();

                            if(count($selectedFile) === 0){
                                $query = 'INSERT INTO FileContent(category, element, url, src, width, height, lan, tmp_url, tmp_src) VALUES
                                (?,?,?,?,?,?,?,?,?)';
                                $sql_insert_file = $db->prepare($query);
                                $sql_insert_file->bindParam(1,$file->category);
                                $sql_insert_file->bindParam(2,$file->element);
                                $sql_insert_file->bindParam(3,$file->url);
                                $sql_insert_file->bindParam(4,$file->src);
                                $sql_insert_file->bindParam(5,$file->width);
                                $sql_insert_file->bindParam(6,$file->height);
                                $sql_insert_file->bindParam(7,$lan);
                                $sql_insert_file->bindParam(8,$file->tmp_url);
                                $sql_insert_file->bindParam(9,$file->tmp_src);
                                $sql_insert_file->execute();
                            }
                        }
                    }catch(Exception $e){
                    $app->halt(503, json_encode(['type' => 'Error',
                                                'title' => 'Oops, something went wrong!',
                                                'message' => $e->getMessage()]));
                }finally {$db = null;}
            }else{
                    $app->halt(503, json_encode([   'type' => 'Error',
                                                    'title' => 'Oops, something went wrong!',
                                                    'message' => 'No database connection']));
            }
            $app->response->status(400);
            $app->response->body(json_encode([  'type' => 'Error',
                                                'title' => 'Oops, something went wrong!',
                                                'message' => 'The language could not be inserted!']));

            if($result === 0 ) $app->stop();

            $app->redirect($app->urlFor('getLanguage'));
        }else{
            $app->halt(403, json_encode([   'type' => 'error',
                                            'title' => 'Forbidden Request',
                                            'message' => 'You do not have the permission to call this request.']));
        }
        
    });

});

$app->group('/user', function() use($app){
    $app->response->headers->set('Content-Type', 'application/json');

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
                                                'title' => 'Could not log you in!',
                                                'message' => 'Please check your login data']));
        }
    });

    $app->put('/logout', function() use($app){
        if($app->getCookie('aco-lan') !== null)          $lan = $app->getCookie('aco-lan');
         if(($db = connectToMySql()) !== false){
            try{
                $query = 'SELECT * FROM Language WHERE lan = ?';
                $sql_user = $db->prepare($query);
                $sql_user->bindParam(1, $lan);
                $sql_user->execute();
                $sql_user->setFetchMode(PDO::FETCH_OBJ);
                $result = $sql_user->fetch();
            }catch(Exception $e){
                $app->halt(503, json_encode(['type' => 'error',
                                             'title' => 'Oops, something went wrong! catch',
                                             'message' => $query]));
            }finally{$db = null;}
        }else{
            $app->halt(503, json_encode([   'type' => 'error',
                                             'title' => 'Oops, something went wrong!',
                                             'message' => 'No database connection']));
        }
        if($result->toggle == 0){
            $app->deleteCookie('aco-lan');
        }
        $app->deleteCookie('aco-user');
        $app->redirect($app->urlFor('getContent'));
    });

    $app->get('/view', function() use($app){
        $app->response->status(200);
        $app->response->body(json_encode(['user' => $app->getCookie('aco-user')]));
    });
});

$app->run();
?>
