(function(){
    
    angular.module('Acolyte')
    
    .factory('AcoLanguageService',['$rootScpe',function($rootScope){
        
        var self = this;
        
        self.lan = 'en';
        self.languages = [
            {
                lan: 'en',
                language: 'English'
            }
        ];
        
        self.getLanguages = function(){
            return self.languages;
        }
        self.setLanguages = function(languages){
            self.languages = languages;
            self.broadcastLanguagesChanged();
        }
        self.setSelectedLan = function(lan){
            self.lan = lan;
            self.broadcastLanguagesChanged();
        }
        
        self.broadcastLanguagesChanged = function(){
            $rootScope.$broadcast('AcoLoginStateChanged');
        }
        
        return self;
        
    }]);
    
})();