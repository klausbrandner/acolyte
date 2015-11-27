<?php
    function getSql(){
        return "
            ##DROP TABLE IF EXISTS TextContent;
            ##DROP TABLE IF EXISTS FileContent;
            ##DROP TABLE IF EXISTS Language;

            CREATE TABLE IF NOT EXISTS Language(
                lan             VARCHAR(255),    
                language        VARCHAR(255),    
    
                tmp_lan         VARCHAR(255),
                tmp_language    VARCHAR(255),
    
                PRIMARY KEY(lan)
            );

            CREATE TABLE IF NOT EXISTS TextContent(
                category    VARCHAR(255)    NOT NULL,
                element     VARCHAR(255)    NOT NULL,
                text        TEXT,            
                lan         VARCHAR(255)    NOT NULL,

                tmp_text    TEXT,

                PRIMARY KEY(category, element, lan),
                FOREIGN KEY (lan) REFERENCES language(lan) ON DELETE CASCADE ON UPDATE CASCADE
            );



            CREATE TABLE IF NOT EXISTS FileContent(
                category    VARCHAR(255)    NOT NULL,
                element     VARCHAR(255)    NOT NULL,
                url         VARCHAR(255),    
                src         VARCHAR(255),    
                width       INT(11),         
                height      INT(11),         

                tmp_url     VARCHAR(255),
                tmp_src     VARCHAR(255),

                PRIMARY KEY(category, element)
            );

            INSERT IGNORE INTO `Language`(`lan`, `language`, `tmp_lan`, `tmp_language`) VALUES ('en', 'en', 'en', 'en');
        ";
    }
?>