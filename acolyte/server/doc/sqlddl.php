_____________________________________________________________________________________

TABLES --- TABLES --- TABLES --- TABLES --- TABLES --- TABLES --- TABLES --- TABLES
_____________________________________________________________________________________
DROP TABLE IF EXISTS textcontent;
DROP TABLE IF EXISTS filecontent;
DROP TABLE IF EXISTS language;

CREATE TABLE Language(
    lan             VARCHAR(255),    
    language        VARCHAR(255),    
    
    tmp_lan         VARCHAR(255),
    tmp_language    VARCHAR(255),
    
    PRIMARY KEY(lan)
);

CREATE TABLE TextContent(
    category    VARCHAR(255)    NOT NULL,
    element     VARCHAR(255)    NOT NULL,
    text        TEXT,            
    lan         VARCHAR(255)    NOT NULL,
    
    tmp_text    TEXT,
    
    PRIMARY KEY(category, element, lan),
    FOREIGN KEY (lan) REFERENCES language(lan) ON DELETE CASCADE ON UPDATE CASCADE
);



CREATE TABLE FileContent(
    category    VARCHAR(255)    NOT NULL,
    element     VARCHAR(255)    NOT NULL,
    url         VARCHAR(255),    
    src         VARCHAR(255),    
    width       INT(11)         NOT NULL,
    height      INT(11)         NOT NULL,
    
    tmp_url     VARCHAR(255),
    tmp_src     VARCHAR(255),
    
    PRIMARY KEY(category, element)
);
_____________________________________________________________________________________

TABLES --- TABLES --- TABLES --- TABLES --- TABLES --- TABLES --- TABLES --- TABLES
_____________________________________________________________________________________



