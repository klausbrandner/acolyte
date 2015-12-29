(function(){
    
    angular.module('Acolyte')
    
    .controller('AcoLanguageController',['$scope','$timeout','AcoLanguageService','AcoMessageBoxService','AcoNotificationService',function($scope,$timeout,AcoLanguageService,AcoMessageBoxService,AcoNotificationService){
        
        var self = this;
        self.lan = 'en';
        self.languages = [];
        self.newLanKeyword = '';
        self.searchResults = [];
        
        init();
        function init(){
            // http -> get all language data
            
            var data = {
                lan: 'en',
                language: [
                    {
                        lan: 'de',
                        language: 'Deutsch',
                        toggle: 1,
                        preset: 0
                    },{
                        lan: 'en',
                        language: 'English',
                        toggle: 1,
                        preset: 1
                    },{
                        lan: 'nl',
                        language: 'Dutch',
                        toggle: 0,
                        preset: 0
                    }
                ],
                languages: [
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
                ]
            }
            
            $timeout(function(){
                AcoLanguageService.initLanguages(data.lan,data.language);
                AcoLanguageService.initAvailLanguages(data.languages);
            });
        }
        
        self.setLan = function(lan){
            AcoLanguageService.setLan(lan);
        }
        self.setToggle = function(lan){
            AcoLanguageService.setToggle(lan);
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
            AcoLanguageService.deleteLanguage(lan);
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
        
    }]);
    
})();