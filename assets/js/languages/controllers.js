(function(){
    
    angular.module('Acolyte')
    
    .controller('AcoLanguageController',['$scope','$http','AcoLanguageService','AcoMessageBoxService','AcoNotificationService',function($scope,$http,AcoLanguageService,AcoMessageBoxService,AcoNotificationService){
        
        var self = this;
        self.lan = 'en';
        self.languages = [];
        self.newLanKeyword = '';
        self.searchResults = [];
        
        init();
        function init(){
            
            // fetch languages for the first time
            CreateRequest(function(token){
                
                $http.get(acolyte.pathToServer + 'language/get').success(function(response){
                    AcoLanguageService.initLanguages(response.lan,response.language);
                    AcoLanguageService.initAvailLanguages(response.languages);
                }).error(function(response){
                    console.log(response);
                });
                
            });
            
        }
        
        self.setLan = function(lan){
            AcoLanguageService.setLan(lan);
        }
        self.setToggle = function(lan){
            if(lan.preset != 1){
                console.log(lan);
                AcoLanguageService.setToggle(lan);
            }else{
                AcoNotificationService.push("error","Can not be deactivated","Sorry, but this language is set as default language and can not be deactivated.")
            }
        }
        self.deleteLanguage = function(lan){
            if(lan.preset != 1){
                AcoMessageBoxService.pushMessage({
                    title: "Delete Language",
                    message: "Do you want to keep the texts in the database or delete them permanently?",
                    buttons: [
                        {
                            title: "Delete texts",
                            callback: function(){
                                DeleteLanPermanently(lan);
                            }
                        },{
                            title: "Keep texts",
                            callback: function(){
                                DeleteLanKeepTexts(lan);
                            }
                        }
                    ]
                });
            }else{
                AcoNotificationService.push("error","Can not be deleted","Sorry, but this language is set as default language and can not be deleted.")
            }
        }
        function DeleteLanPermanently(lan){
            AcoLanguageService.deleteAndTexts(lan);
        }
        function DeleteLanKeepTexts(lan){
            AcoLanguageService.deleteLanguage(lan);
        }
        self.searchLan = function(){
            self.searchResults = AcoLanguageService.searchAvailLans(self.newLanKeyword);
        }
        self.addLanguage = function(newlan){
            self.newLanKeyword = '';
            self.searchResults = [];
            AcoLanguageService.addLanguage(newlan);
        }
        
        // Listener to Language Changes
        $scope.$on('AcoLanguagesChanged',function(){
            self.lan = AcoLanguageService.getLan();
            self.languages = AcoLanguageService.getLanguages();
        });
        
    }])
    
    .controller('AcolyteLanguageController',['$scope','AcoLanguageService',function($scope,AcoLanguageService){
        
        var self = this;
        
        self.active = 'en';
        self.languages = [
            {
                lan: 'en',
                language: 'English'
            }
        ];
        
        self.select = function(language){
            AcoLanguageService.setLan(language.lan);
        }
        
        // Listener to Language Changes
        $scope.$on('AcoLanguagesChanged',function(){
            self.active = AcoLanguageService.getLan();
            //self.languages = AcoLanguageService.getLanguages();
            self.languages = [];
            var languages = AcoLanguageService.getLanguages();
            for(var l in languages){
                if(languages[l].toggle == 1){
                    self.languages.push(languages[l]);
                }
            }
        });
        
    }]);
    
    
})();