<?php
require_once("settings.php");   

function security_token($token){
    return $_COOKIE['aco-token'];
    //if($token === $_SESSION['token']) return true;
    //else return false;
}

function security_login($user, $password){
    if($user === ACOUSER && $password === ACOPASSWORD) return true;
    else return false;
}
?>