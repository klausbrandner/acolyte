<?php
session_start();

function security_token($token){
    if($token == $_SESSION['token']) return true;
    return false;
}
?>