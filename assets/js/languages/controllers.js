(function(){
    
    angular.module('Acolyte')
    
    .controller('AcoLanguageController',['$scope','AcoLanguageService',function($scope,AcoLanguageService){
        
        var self = this;
        self.lan = 'en';
        self.languages = [];
        self.newLanKeyword = '';
        self.searchResults = [];
        
        init();
        function init(){
            self.lan = AcoLanguageService.getLan();
            self.languages = AcoLanguageService.getLanguages();
        }
        
        self.setLan = function(lan){
            AcoLanguageService.setLan(lan);
        }
        self.setToggle = function(lan){
            AcoLanguageService.setToggle(lan);
        }
        self.deleteLanguage = function(lan){
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