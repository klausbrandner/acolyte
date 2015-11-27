<?php
    require_once("settings.php");   // FOR THE DATABASE CONNECTION
    require_once("sql_ddl.php");    // FOR THE DATABASE SETUP

    function connectToMySql(){
        try{
            $dbCon = new PDO("mysql:host=" . HOST . ";dbname=" . DATABASE, USER, PASSWORD);
            $dbCon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $dbCon;
        }catch(PDOException $e){
            //echo $e->getMessage();
            return false;
        }
    }

    function setupMySql($dbCon){
        try{
            $query = getSql();
            $setup = $dbCon->prepare($query);
            $setup->execute();
        }catch(Exception $e){
            return false;
        }
        return $dbCon;
    }
?>