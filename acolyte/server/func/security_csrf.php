<?php
require_once("settings.php"); 

function security_token($token){
    if(CSRF === true){    
        if($token === $_COOKIE['aco-token']) return true;
    }
    return false;
}

function security_login($user, $password){
    if($user === ACOUSER && $password === ACOPASSWORD) return true;
    else return false;
}
?>