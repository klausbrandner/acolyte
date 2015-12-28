(function(){
    
    angular.module('Acolyte')
    
    .factory('AcoLanguageService',['$rootScope',function($rootScope){
        
        var self = this;
        
        self.lan = 'en';
        self.languages = [
            {
                lan: 'de',
                language: 'Deutsch',
                toggle: 1
            },{
                lan: 'en',
                language: 'English',
                toggle: 1
            },{
                lan: 'nl',
                language: 'Dutch',
                toggle: 0
            }
        ];
        self.availableLanguages = [
            {
                lan: 'de',
                language: 'Deutsch'
            },{
                lan: 'en',
                language: 'English'
            },{
                lan: 'nl',
                language: 'Dutch'
            }
        ];
        
        self.getLan = function(){
            return self.lan;
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
        self.setLan = function(lancode){
            // http -> setLan
            self.lan = lancode;
            self.broadcastLanguagesChanged();
        }
        self.setLanguages = function(languages){
            self.languages = languages;
            self.broadcastLanguagesChanged();
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
        self.deleteLanguage = function(lancode){
            // http -> delete language
            var i = 0;
            for(var l in self.languages){
                if(self.languages[l].lan == lancode){
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