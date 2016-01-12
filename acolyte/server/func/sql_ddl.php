<?php
function getSql(){
return "
##DROP TABLE IF EXISTS TextContent;
##DROP TABLE IF EXISTS FileContent;
##DROP TABLE IF EXISTS Language;
##DROP TABLE IF EXISTS Languages;


CREATE TABLE IF NOT EXISTS Languages(
		            lan             			VARCHAR(255) NOT NULL,    
                	language       	 		    VARCHAR(255) NOT NULL,   

		            PRIMARY KEY (lan, language)
);

CREATE TABLE IF NOT EXISTS Language(
                	lan             			VARCHAR(255) NOT NULL,    
                	language       	 		    VARCHAR(255) NOT NULL,    

		            toggle				        TINYINT(1),
		            preset			            TINYINT(1),
    
                	PRIMARY KEY(lan)
                    ##,FOREIGN KEY (lan) REFERENCES Languages(lan) 
);

CREATE TABLE IF NOT EXISTS TextContent(
                	category    			VARCHAR(255)    NOT NULL,
                	element     			VARCHAR(255)    NOT NULL,
                	text        			TEXT,            
                	lan         			VARCHAR(255)    NOT NULL,

                 	tmp_text    			TEXT,

                	PRIMARY KEY(category, element, lan)
                    ##,FOREIGN KEY (lan) REFERENCES Language(lan) 
);


CREATE TABLE IF NOT EXISTS FileContent(
                	category    			VARCHAR(255)    NOT NULL,
                	element     			VARCHAR(255)    NOT NULL,
                	url         			VARCHAR(255),    
                	src         			VARCHAR(255),    
                	width       			INT(11),         
                	height     			    INT(11),  
                    lan         			VARCHAR(255)    NOT NULL,

                	tmp_url     			VARCHAR(255),
                	tmp_src     			VARCHAR(255),

                	PRIMARY KEY(category, element, lan)
                    ##,FOREIGN KEY (lan) REFERENCES Language(lan) ON DELETE NO ACTION ON UPDATE CASCADE
);


INSERT INTO Languages (lan, language) VALUES('ab', 'Abkhazian') ON DUPLICATE KEY UPDATE lan = 'ab', language = 'Abkhazian';
INSERT INTO Languages (lan, language) VALUES('aa', 'Afar') ON DUPLICATE KEY UPDATE lan = 'aa', language = 'Afar';
INSERT INTO Languages (lan, language) VALUES('af', 'Afrikaans') ON DUPLICATE KEY UPDATE lan = 'af', language = 'Afrikaans';
INSERT INTO Languages (lan, language) VALUES('sq', 'Albanian') ON DUPLICATE KEY UPDATE lan = 'sq', language = 'Albanian';
INSERT INTO Languages (lan, language) VALUES('am', 'Amharic') ON DUPLICATE KEY UPDATE lan = 'am', language = 'Amharic';
INSERT INTO Languages (lan, language) VALUES('ar', 'Arabic') ON DUPLICATE KEY UPDATE lan = 'ar', language = 'Arabic';
INSERT INTO Languages (lan, language) VALUES('an', 'Aragonese') ON DUPLICATE KEY UPDATE lan = 'an', language = 'Aragonese';
INSERT INTO Languages (lan, language) VALUES('hy', 'Armenian') ON DUPLICATE KEY UPDATE lan = 'hy', language = 'Armenian';
INSERT INTO Languages (lan, language) VALUES('as', 'Assamese') ON DUPLICATE KEY UPDATE lan = 'as', language = 'Assamese';
INSERT INTO Languages (lan, language) VALUES('ay', 'Aymara') ON DUPLICATE KEY UPDATE lan = 'ay', language = 'Aymara';
INSERT INTO Languages (lan, language) VALUES('az', 'Azerbaijani') ON DUPLICATE KEY UPDATE lan = 'az', language = 'Azerbaijani';
INSERT INTO Languages (lan, language) VALUES('ba', 'Bashkir') ON DUPLICATE KEY UPDATE lan = 'ba', language = 'Bashkir';
INSERT INTO Languages (lan, language) VALUES('eu', 'Basque') ON DUPLICATE KEY UPDATE lan = 'eu', language = 'Basque';
INSERT INTO Languages (lan, language) VALUES('bn', 'Bengali (Bangla)') ON DUPLICATE KEY UPDATE lan = 'bn', language = 'Bengali (Bangla)';
INSERT INTO Languages (lan, language) VALUES('dz', 'Bhutani') ON DUPLICATE KEY UPDATE lan = 'dz', language = 'Bhutani';
INSERT INTO Languages (lan, language) VALUES('bh', 'Bihari') ON DUPLICATE KEY UPDATE lan = 'bh', language = 'Bihari';
INSERT INTO Languages (lan, language) VALUES('bi', 'Bislama') ON DUPLICATE KEY UPDATE lan = 'bi', language = 'Bislama';
INSERT INTO Languages (lan, language) VALUES('br', 'Breton') ON DUPLICATE KEY UPDATE lan = 'br', language = 'Breton';
INSERT INTO Languages (lan, language) VALUES('bg', 'Bulgarian') ON DUPLICATE KEY UPDATE lan = 'bg', language = 'Bulgarian';
INSERT INTO Languages (lan, language) VALUES('my', 'Burmese') ON DUPLICATE KEY UPDATE lan = 'my', language = 'Burmese';
INSERT INTO Languages (lan, language) VALUES('be', 'Byelorussian (Belarusian)') ON DUPLICATE KEY UPDATE lan = 'be', language = 'Byelorussian (Belarusian)';
INSERT INTO Languages (lan, language) VALUES('km', 'Cambodian') ON DUPLICATE KEY UPDATE lan = 'km', language = 'Cambodian';
INSERT INTO Languages (lan, language) VALUES('ca', 'Catalan') ON DUPLICATE KEY UPDATE lan = 'ca', language = 'Catalan';
INSERT INTO Languages (lan, language) VALUES('zh', 'Chinese') ON DUPLICATE KEY UPDATE lan = 'zh', language = 'Chinese';
INSERT INTO Languages (lan, language) VALUES('co', 'Corsican') ON DUPLICATE KEY UPDATE lan = 'co', language = 'Corsican';
INSERT INTO Languages (lan, language) VALUES('hr', 'Croatian') ON DUPLICATE KEY UPDATE lan = 'hr', language = 'Croatian';
INSERT INTO Languages (lan, language) VALUES('cs', 'Czech') ON DUPLICATE KEY UPDATE lan = 'cs', language = 'Czech';
INSERT INTO Languages (lan, language) VALUES('da', 'Danish') ON DUPLICATE KEY UPDATE lan = 'da', language = 'Danish';
INSERT INTO Languages (lan, language) VALUES('nl', 'Dutch') ON DUPLICATE KEY UPDATE lan = 'nl', language = 'Dutch';
INSERT INTO Languages (lan, language) VALUES('en', 'English') ON DUPLICATE KEY UPDATE lan = 'en', language = 'English';
INSERT INTO Languages (lan, language) VALUES('eo', 'Esperanto') ON DUPLICATE KEY UPDATE lan = 'eo', language = 'Esperanto';
INSERT INTO Languages (lan, language) VALUES('et', 'Estonian') ON DUPLICATE KEY UPDATE lan = 'et', language = 'Estonian';
INSERT INTO Languages (lan, language) VALUES('fo', 'Faeroese') ON DUPLICATE KEY UPDATE lan = 'fo', language = 'Faeroese';
INSERT INTO Languages (lan, language) VALUES('fa', 'Farsi') ON DUPLICATE KEY UPDATE lan = 'fa', language = 'Farsi';
INSERT INTO Languages (lan, language) VALUES('fj', 'Fiji') ON DUPLICATE KEY UPDATE lan = 'fj', language = 'Fiji';
INSERT INTO Languages (lan, language) VALUES('fi', 'Finnish') ON DUPLICATE KEY UPDATE lan = 'fi', language = 'Finnish';
INSERT INTO Languages (lan, language) VALUES('fr', 'French') ON DUPLICATE KEY UPDATE lan = 'fr', language = 'French';
INSERT INTO Languages (lan, language) VALUES('fy', 'Frisian') ON DUPLICATE KEY UPDATE lan = 'fy', language = 'Frisian';
INSERT INTO Languages (lan, language) VALUES('gl', 'Galician') ON DUPLICATE KEY UPDATE lan = 'gl', language = 'Galician';
INSERT INTO Languages (lan, language) VALUES('gd', 'Gaelic (Scottish)') ON DUPLICATE KEY UPDATE lan = 'gd', language = 'Gaelic (Scottish)';
INSERT INTO Languages (lan, language) VALUES('gv', 'Gaelic (Manx)') ON DUPLICATE KEY UPDATE lan = 'gv', language = 'Gaelic (Manx)';
INSERT INTO Languages (lan, language) VALUES('ka', 'Georgian') ON DUPLICATE KEY UPDATE lan = 'ka', language = 'Georgian';
INSERT INTO Languages (lan, language) VALUES('de', 'German') ON DUPLICATE KEY UPDATE lan = 'de', language = 'German';
INSERT INTO Languages (lan, language) VALUES('el', 'Greek') ON DUPLICATE KEY UPDATE lan = 'el', language = 'Greek';
INSERT INTO Languages (lan, language) VALUES('kl', 'Greenlandic') ON DUPLICATE KEY UPDATE lan = 'kl', language = 'Greenlandic';
INSERT INTO Languages (lan, language) VALUES('gn', 'Guarani') ON DUPLICATE KEY UPDATE lan = 'gn', language = 'Guarani';
INSERT INTO Languages (lan, language) VALUES('gu', 'Gujarati') ON DUPLICATE KEY UPDATE lan = 'gu', language = 'Gujarati';
INSERT INTO Languages (lan, language) VALUES('ht', 'Haitian Creole') ON DUPLICATE KEY UPDATE lan = 'ht', language = 'Haitian Creole';
INSERT INTO Languages (lan, language) VALUES('ha', 'Hausa') ON DUPLICATE KEY UPDATE lan = 'ha', language = 'Hausa';
##INSERT INTO Languages (lan, language) VALUES('he, iw', 'Hebrew') ON DUPLICATE KEY UPDATE lan = 'he, iw', language = 'Hebrew';
INSERT INTO Languages (lan, language) VALUES('hi', 'Hindi') ON DUPLICATE KEY UPDATE lan = 'hi', language = 'Hindi';
INSERT INTO Languages (lan, language) VALUES('hu', 'Hungarian') ON DUPLICATE KEY UPDATE lan = 'hu', language = 'Hungarian';
INSERT INTO Languages (lan, language) VALUES('is', 'Icelandic') ON DUPLICATE KEY UPDATE lan = 'is', language = 'Icelandic';
INSERT INTO Languages (lan, language) VALUES('io', 'Ido') ON DUPLICATE KEY UPDATE lan = 'io', language = 'Ido';
##INSERT INTO Languages (lan, language) VALUES('id, in', 'Indonesian') ON DUPLICATE KEY UPDATE lan = 'id, in', language = 'Indonesian';
INSERT INTO Languages (lan, language) VALUES('ia', 'Interlingua') ON DUPLICATE KEY UPDATE lan = 'ia', language = 'Interlingua';
INSERT INTO Languages (lan, language) VALUES('ie', 'Interlingue') ON DUPLICATE KEY UPDATE lan = 'ie', language = 'Interlingue';
INSERT INTO Languages (lan, language) VALUES('iu', 'Inuktitut') ON DUPLICATE KEY UPDATE lan = 'iu', language = 'Inuktitut';
INSERT INTO Languages (lan, language) VALUES('ik', 'Inupiak') ON DUPLICATE KEY UPDATE lan = 'ik', language = 'Inupiak';
INSERT INTO Languages (lan, language) VALUES('ga', 'Irish') ON DUPLICATE KEY UPDATE lan = 'ga', language = 'Irish';
INSERT INTO Languages (lan, language) VALUES('it', 'Italian') ON DUPLICATE KEY UPDATE lan = 'it', language = 'Italian';
INSERT INTO Languages (lan, language) VALUES('ja', 'Japanese') ON DUPLICATE KEY UPDATE lan = 'ja', language = 'Japanese';
INSERT INTO Languages (lan, language) VALUES('jv', 'Javanese') ON DUPLICATE KEY UPDATE lan = 'jv', language = 'Javanese';
INSERT INTO Languages (lan, language) VALUES('kn', 'Kannada') ON DUPLICATE KEY UPDATE lan = 'kn', language = 'Kannada';
INSERT INTO Languages (lan, language) VALUES('ks', 'Kashmiri') ON DUPLICATE KEY UPDATE lan = 'ks', language = 'Kashmiri';
INSERT INTO Languages (lan, language) VALUES('kk', 'Kazakh') ON DUPLICATE KEY UPDATE lan = 'kk', language = 'Kazakh';
INSERT INTO Languages (lan, language) VALUES('rw', 'Kinyarwanda (Ruanda)') ON DUPLICATE KEY UPDATE lan = 'rw', language = 'Kinyarwanda (Ruanda)';
INSERT INTO Languages (lan, language) VALUES('ky', 'Kirghiz') ON DUPLICATE KEY UPDATE lan = 'ky', language = 'Kirghiz';
INSERT INTO Languages (lan, language) VALUES('rn', 'Kirundi (Rundi)') ON DUPLICATE KEY UPDATE lan = 'rn', language = 'Kirundi (Rundi)';
INSERT INTO Languages (lan, language) VALUES('ko', 'Korean') ON DUPLICATE KEY UPDATE lan = 'ko', language = 'Korean';
INSERT INTO Languages (lan, language) VALUES('ku', 'Kurdish') ON DUPLICATE KEY UPDATE lan = 'ku', language = 'Kurdish';
INSERT INTO Languages (lan, language) VALUES('lo', 'Laothian') ON DUPLICATE KEY UPDATE lan = 'lo', language = 'Laothian';
INSERT INTO Languages (lan, language) VALUES('la', 'Latin') ON DUPLICATE KEY UPDATE lan = 'la', language = 'Latin';
INSERT INTO Languages (lan, language) VALUES('lv', 'Latvian (Lettish)') ON DUPLICATE KEY UPDATE lan = 'lv', language = 'Latvian (Lettish)';
INSERT INTO Languages (lan, language) VALUES('li', 'Limburgish (Limburger)') ON DUPLICATE KEY UPDATE lan = 'li', language = 'Limburgish ( Limburger)';
INSERT INTO Languages (lan, language) VALUES('ln', 'Lingala') ON DUPLICATE KEY UPDATE lan = 'ln', language = 'Lingala';
INSERT INTO Languages (lan, language) VALUES('lt', 'Lithuanian') ON DUPLICATE KEY UPDATE lan = 'lt', language = 'Lithuanian';
INSERT INTO Languages (lan, language) VALUES('mk', 'Macedonian') ON DUPLICATE KEY UPDATE lan = 'mk', language = 'Macedonian';
INSERT INTO Languages (lan, language) VALUES('mg', 'Malagasy') ON DUPLICATE KEY UPDATE lan = 'mg', language = 'Malagasy';
INSERT INTO Languages (lan, language) VALUES('ms', 'Malay') ON DUPLICATE KEY UPDATE lan = 'ms', language = 'Malay';
INSERT INTO Languages (lan, language) VALUES('ml', 'Malayalam') ON DUPLICATE KEY UPDATE lan = 'ml', language = 'Malayalam';
INSERT INTO Languages (lan, language) VALUES('mt', 'Maltese') ON DUPLICATE KEY UPDATE lan = 'mt', language = 'Maltese';
INSERT INTO Languages (lan, language) VALUES('mi', 'Maori') ON DUPLICATE KEY UPDATE lan = 'mi', language = 'Maori';
INSERT INTO Languages (lan, language) VALUES('mr', 'Marathi') ON DUPLICATE KEY UPDATE lan = 'mr', language = 'Marathi';
INSERT INTO Languages (lan, language) VALUES('mo', 'Moldavian') ON DUPLICATE KEY UPDATE lan = 'mo', language = 'Moldavian';
INSERT INTO Languages (lan, language) VALUES('mn', 'Mongolian') ON DUPLICATE KEY UPDATE lan = 'mn', language = 'Mongolian';
INSERT INTO Languages (lan, language) VALUES('na', 'Nauru') ON DUPLICATE KEY UPDATE lan = 'na', language = 'Nauru';
INSERT INTO Languages (lan, language) VALUES('ne', 'Nepali') ON DUPLICATE KEY UPDATE lan = 'ne', language = 'Nepali';
INSERT INTO Languages (lan, language) VALUES('no', 'Norwegian') ON DUPLICATE KEY UPDATE lan = 'no', language = 'Norwegian';
INSERT INTO Languages (lan, language) VALUES('oc', 'Occitan') ON DUPLICATE KEY UPDATE lan = 'oc', language = 'Occitan';
INSERT INTO Languages (lan, language) VALUES('or', 'Oriya') ON DUPLICATE KEY UPDATE lan = 'or', language = 'Oriya';
INSERT INTO Languages (lan, language) VALUES('om', 'Oromo (Afaan Oromo)') ON DUPLICATE KEY UPDATE lan = 'om', language = 'Oromo (Afaan Oromo)';
INSERT INTO Languages (lan, language) VALUES('ps', 'Pashto (Pushto)') ON DUPLICATE KEY UPDATE lan = 'ps', language = 'Pashto (Pushto)';
INSERT INTO Languages (lan, language) VALUES('pl', 'Polish') ON DUPLICATE KEY UPDATE lan = 'pl', language = 'Polish';
INSERT INTO Languages (lan, language) VALUES('pt', 'Portuguese') ON DUPLICATE KEY UPDATE lan = 'pt', language = 'Portuguese';
INSERT INTO Languages (lan, language) VALUES('pa', 'Punjabi') ON DUPLICATE KEY UPDATE lan = 'pa', language = 'Punjabi';
INSERT INTO Languages (lan, language) VALUES('qu', 'Quechua') ON DUPLICATE KEY UPDATE lan = 'qu', language = 'Quechua';
INSERT INTO Languages (lan, language) VALUES('rm', 'Rhaeto-Romance') ON DUPLICATE KEY UPDATE lan = 'rm', language = 'Rhaeto-Romance';
INSERT INTO Languages (lan, language) VALUES('ro', 'Romanian') ON DUPLICATE KEY UPDATE lan = 'ro', language = 'Romanian';
INSERT INTO Languages (lan, language) VALUES('ru', 'Russian') ON DUPLICATE KEY UPDATE lan = 'ru', language = 'Russian';
INSERT INTO Languages (lan, language) VALUES('sm', 'Samoan') ON DUPLICATE KEY UPDATE lan = 'sm', language = 'Samoan';
INSERT INTO Languages (lan, language) VALUES('sg', 'Sangro') ON DUPLICATE KEY UPDATE lan = 'sg', language = 'Sangro';
INSERT INTO Languages (lan, language) VALUES('sa', 'Sanskrit') ON DUPLICATE KEY UPDATE lan = 'sa', language = 'Sanskrit';
INSERT INTO Languages (lan, language) VALUES('sr', 'Serbian') ON DUPLICATE KEY UPDATE lan = 'sr', language = 'Serbian';
INSERT INTO Languages (lan, language) VALUES('sh', 'Serbo-Croatian') ON DUPLICATE KEY UPDATE lan = 'sh', language = 'Serbo-Croatian';
INSERT INTO Languages (lan, language) VALUES('st', 'Sesotho') ON DUPLICATE KEY UPDATE lan = 'st', language = 'Sesotho';
INSERT INTO Languages (lan, language) VALUES('tn', 'Setswana') ON DUPLICATE KEY UPDATE lan = 'tn', language = 'Setswana';
INSERT INTO Languages (lan, language) VALUES('sn', 'Shona') ON DUPLICATE KEY UPDATE lan = 'sn', language = 'Shona';
INSERT INTO Languages (lan, language) VALUES('ii', 'Sichuan Yi') ON DUPLICATE KEY UPDATE lan = 'ii', language = 'Sichuan Yi';
INSERT INTO Languages (lan, language) VALUES('sd', 'Sindhi') ON DUPLICATE KEY UPDATE lan = 'sd', language = 'Sindhi';
INSERT INTO Languages (lan, language) VALUES('si', 'Sinhalese') ON DUPLICATE KEY UPDATE lan = 'si', language = 'Sinhalese';
INSERT INTO Languages (lan, language) VALUES('ss', 'Siswati') ON DUPLICATE KEY UPDATE lan = 'ss', language = 'Siswati';
INSERT INTO Languages (lan, language) VALUES('sk', 'Slovak') ON DUPLICATE KEY UPDATE lan = 'sk', language = 'Slovak';
INSERT INTO Languages (lan, language) VALUES('sl', 'Slovenian') ON DUPLICATE KEY UPDATE lan = 'sl', language = 'Slovenian';
INSERT INTO Languages (lan, language) VALUES('so', 'Somali') ON DUPLICATE KEY UPDATE lan = 'so', language = 'Somali';
INSERT INTO Languages (lan, language) VALUES('es', 'Spanish') ON DUPLICATE KEY UPDATE lan = 'es', language = 'Spanish';
INSERT INTO Languages (lan, language) VALUES('su', 'Sundanese') ON DUPLICATE KEY UPDATE lan = 'su', language = 'Sundanese';
INSERT INTO Languages (lan, language) VALUES('sw', 'Swahili (Kiswahili)') ON DUPLICATE KEY UPDATE lan = 'sw', language = 'Swahili (Kiswahili)';
INSERT INTO Languages (lan, language) VALUES('sv', 'Swedish') ON DUPLICATE KEY UPDATE lan = 'sv', language = 'Swedish';
##INSERT INTO Languages (lan, language) VALUES(' ', 'Syriac') ON DUPLICATE KEY UPDATE lan = ' ', language = 'Syriac';
INSERT INTO Languages (lan, language) VALUES('tl', 'Tagalog') ON DUPLICATE KEY UPDATE lan = 'tl', language = 'Tagalog';
INSERT INTO Languages (lan, language) VALUES('tg', 'Tajik') ON DUPLICATE KEY UPDATE lan = 'tg', language = 'Tajik';
##INSERT INTO Languages (lan, language) VALUES(' ', 'Tamazight') ON DUPLICATE KEY UPDATE lan = ' ', language = 'Tamazight';
INSERT INTO Languages (lan, language) VALUES('ta', 'Tamil') ON DUPLICATE KEY UPDATE lan = 'ta', language = 'Tamil';
INSERT INTO Languages (lan, language) VALUES('tt', 'Tatar') ON DUPLICATE KEY UPDATE lan = 'tt', language = 'Tatar';
INSERT INTO Languages (lan, language) VALUES('te', 'Telugu') ON DUPLICATE KEY UPDATE lan = 'te', language = 'Telugu';
INSERT INTO Languages (lan, language) VALUES('th', 'Thai') ON DUPLICATE KEY UPDATE lan = 'th', language = 'Thai';
INSERT INTO Languages (lan, language) VALUES('bo', 'Tibetan') ON DUPLICATE KEY UPDATE lan = 'bo', language = 'Tibetan';
INSERT INTO Languages (lan, language) VALUES('ti', 'Tigrinya') ON DUPLICATE KEY UPDATE lan = 'ti', language = 'Tigrinya';
INSERT INTO Languages (lan, language) VALUES('to', 'Tonga') ON DUPLICATE KEY UPDATE lan = 'to', language = 'Tonga';
INSERT INTO Languages (lan, language) VALUES('ts', 'Tsonga') ON DUPLICATE KEY UPDATE lan = 'ts', language = 'Tsonga';
INSERT INTO Languages (lan, language) VALUES('tr', 'Turkish') ON DUPLICATE KEY UPDATE lan = 'tr', language = 'Turkish';
INSERT INTO Languages (lan, language) VALUES('tk', 'Turkmen') ON DUPLICATE KEY UPDATE lan = 'tk', language = 'Turkmen';
INSERT INTO Languages (lan, language) VALUES('tw', 'Twi') ON DUPLICATE KEY UPDATE lan = 'tw', language = 'Twi';
INSERT INTO Languages (lan, language) VALUES('ug', 'Uighur') ON DUPLICATE KEY UPDATE lan = 'ug', language = 'Uighur';
INSERT INTO Languages (lan, language) VALUES('uk', 'Ukrainian') ON DUPLICATE KEY UPDATE lan = 'uk', language = 'Ukrainian';
INSERT INTO Languages (lan, language) VALUES('ur', 'Urdu') ON DUPLICATE KEY UPDATE lan = 'ur', language = 'Urdu';
INSERT INTO Languages (lan, language) VALUES('uz', 'Uzbek') ON DUPLICATE KEY UPDATE lan = 'uz', language = 'Uzbek';
INSERT INTO Languages (lan, language) VALUES('vi', 'Vietnamese') ON DUPLICATE KEY UPDATE lan = 'vi', language = 'Vietnamese';
INSERT INTO Languages (lan, language) VALUES('vo', 'Volapük') ON DUPLICATE KEY UPDATE lan = 'vo', language = 'Volapük';
INSERT INTO Languages (lan, language) VALUES('wa', 'Wallon') ON DUPLICATE KEY UPDATE lan = 'wa', language = 'Wallon';
INSERT INTO Languages (lan, language) VALUES('cy', 'Welsh') ON DUPLICATE KEY UPDATE lan = 'cy', language = 'Welsh';
INSERT INTO Languages (lan, language) VALUES('wo', 'Wolof') ON DUPLICATE KEY UPDATE lan = 'wo', language = 'Wolof';
INSERT INTO Languages (lan, language) VALUES('xh', 'Xhosa') ON DUPLICATE KEY UPDATE lan = 'xh', language = 'Xhosa';
##INSERT INTO Languages (lan, language) VALUES('yi, ji', 'Yiddish') ON DUPLICATE KEY UPDATE lan = 'yi, ji', language = 'Yiddish';
INSERT INTO Languages (lan, language) VALUES('yo', 'Yoruba') ON DUPLICATE KEY UPDATE lan = 'yo', language = 'Yoruba';
INSERT INTO Languages (lan, language) VALUES('zu', 'Zulu') ON DUPLICATE KEY UPDATE lan = 'zu', language = 'Zulu';

INSERT INTO Language (lan, language, toggle, preset) VALUES ('en', 'English', 1, 1) ON DUPLICATE KEY UPDATE lan = 'en', language = 'english', toggle = 1, preset = 1;
";
}
?>