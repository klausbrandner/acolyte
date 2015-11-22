<?php
    function connectToMySql($host, $db, $user, $pw){
        try{
            $dbCon = new PDO("mysql:host=" . $host . ";dbname=" . $db, $user, $pw);
            $dbCon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $dbCon;
        }catch(PDOException $e){
            //echo $e->getMessage();
            return false;
        }
    }

    function connectToLocalhost($db){
        try{
            $host = 'localhost';
            $user = 'root';
            $pw = '';
            $dbCon = new PDO("mysql:host=" . $host . ";dbname=" . $db, $user, $pw);
            $dbCon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $dbCon;
        }catch(PDOException $e){
            //echo $e->getMessage();
            return false;
        }
    }

    function connectTo5Design(){
        try{
            return connectToLocalHost('acolyte');
            $host = 'mysqlsvr41.world4you.com';
            $db = '8580095db1';
            $user = 'sql8580095';
            $pw = 'jigb@zx';
            $dbCon = new PDO("mysql:host=" . $host . ";dbname=" . $db, $user, $pw);
            $dbCon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $dbCon;
        }catch(PDOException $e){
            //echo $e->getMessage();
            return false;
        }
    }
?>