<?php
 
//----------------------------------------------------
//                  CONNECTION SETTINGS
//----------------------------------------------------
    

    // YOUR WEBHOST
    define("HOST", "localhost");

    // YOUR USERNAME
    define("USER", "root");

    // YOUR DATABASE
    define("DATABASE", "acolyte");

    // YOUR DATABASE PASSWORD
    define("PASSWORD", "");
    

/*
    // YOUR WEBHOST
    define("HOST", "mysqlsvr50.world4you.com");

    // YOUR USERNAME
    define("USER", "sql8580095");

    // YOUR DATABASE
    define("DATABASE", "8580095db2");

    // YOUR DATABASE PASSWORD
    define("PASSWORD", "p00ky0s");*/

//----------------------------------------------------
//                  USER SETTINGS
//----------------------------------------------------

    // YOUR ACOLYTE USER 
    define("ACOUSER", "admin");

    // YOUR ACOLYTE PW
    define("ACOPASSWORD", "admin");

//----------------------------------------------------
//                  SECURITY SETTINGS
//----------------------------------------------------

    // true = ALLOW CROSS SITE REQUEST FORGERY
    // false = DENY CROSS SITE REQUEST FORGERY
    define("CSRF", true);
    
    // true = COOKIES ARE ENCRYPTED
    // false = COKIES ARE NOT ENCRYPTED
    define("COOKIECRYPT", true);
    
    // KEY FOR COOKIE ENCRYPTION
    define("COOKIEKEY", "acolyte-secret-key");
?>