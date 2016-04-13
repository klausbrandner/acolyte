<?php
require_once("settings.php"); 

function security_token($token){
    if(CSRF === true){
        if(isset($_COOKIE['aco-token'])){
            if($token === $_COOKIE['aco-token']) return true;
        }else return false;
    }
    return true;
}

function security_login($user, $password){
    if($user === ACOUSER && $password === ACOPASSWORD) return true;
    else return false;
}
?>