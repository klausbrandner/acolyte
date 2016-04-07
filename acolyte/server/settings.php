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

    // Protocol type - http or https
    define("PROTOCOL", "http");
?>
