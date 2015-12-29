(function(){
    
    angular.module('Acolyte')
    
    .factory('AcoLanguageService',['$rootScope','AcoPageContentService',function($rootScope,AcoPageContentService){
        
        var self = this;
        
        self.lan = 'en';
        self.languages = [
            {
                lan: 'en',
                language: 'English',
                toggle: 1
            }
        ];
        self.availableLanguages = [];
        
        self.initLanguages = function(lancode, languages){
            self.lan = lancode;
            self.languages = languages;
            self.broadcastLanguagesChanged();
        }
        self.initAvailLanguages = function(languages){
            self.availableLanguages = languages;
        }
        
        self.getLan = function(){
            return self.lan;
        }
        self.setLan = function(lancode){
            // http -> setLan
            self.lan = lancode;
            AcoPageContentService.fetchContent();
            self.broadcastLanguagesChanged();
        }
        
        self.getLanguages = function(){
            return self.languages;
        }
        self.searchAvailLans = function(keyword){
            var result = [];
            if(keyword != "" && keyword != null && keyword.length > 1){
                for(var l in self.availableLanguages){
                    var tmpLan = self.availableLanguages[l];
                    var lowerLan = tmpLan.language.toLowerCase();
                    if(tmpLan.lan == keyword.toLowerCase() || lowerLan.indexOf(keyword.toLowerCase()) >= 0){
                        result.push(self.availableLanguages[l]);
                    }
                }
            }
            return result;
        }
        
        
        self.setToggle = function(lancode){
            // http -> set toggle
            for(var l in self.languages){
                var tmpLan = self.languages[l];
                if(tmpLan.lan == lancode){
                    var setTo = 0;
                    if(self.languages[l].toggle == 0){
                        setTo = 1;
                    }
                    self.languages[l].toggle = setTo;
                }
            }
        }
        self.deleteLanguage = function(lan){
            // http -> delete language
            var i = 0;
            for(var l in self.languages){
                if(self.languages[l].lan == lan.lan){
                    self.languages.splice(i,1);
                }
                i++;
            }
            self.broadcastLanguagesChanged();
        }
        self.addLanguage = function(lan){
            // http -> add lan
            lan.toggle = 0;
            self.languages.push(lan);
        }
        
        
        self.broadcastLanguagesChanged = function(){
            $rootScope.$broadcast('AcoLanguagesChanged');
        }
        
        return self;
        
    }]);
    
})();