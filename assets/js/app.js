
/**

    Global Variable

*/

var acolyte = {
    pathToAcolyte: 'acolyte/',
    pathToServer: 'acolyte/server/',
    tmpImage: 'src/images/black.png',
    tmpText: '...',
    newText: "Hi, I'm a new text.",
    updateRate: 1000
};


(function(){
    
    
    angular.module('Acolyte',['ngSanitize'])
    
    
    /*
    
        ---------------- Directives ----------------
    
    */
    
    .directive('acoRoot',function(){
        return {
            restrict:'E',
            controller: 'AcoRootController',
            controllerAs: 'rootCtrl',
            templateUrl: acolyte.pathToAcolyte + 'templates/aco-root.html'
        }
    })
    
    
    /*
    
        ---------------- Services ----------------
    
    */
    
    .factory('AcoPageContentService',['$rootScope','$http',function($rootScope,$http){
        
        var self = {};
        
        self.texts = {};
        self.images = {};
        
        self.edit = false;
        
        self.setEdit = function(setTo){
            if(typeof setTo !== 'undefined'){
                self.edit = setTo;
            }else{
                self.edit = !self.edit;
            }
            self.broadcastEditMode();
        }
        self.getEditMode = function(){
            return self.edit;
        }
        
        self.fetchContent = function(){
            
            CreateRequest(function(token){
                
                $http.get(acolyte.pathToServer + 'content/get').success(function(response){
                    console.log(response);
                    self.texts = response.textContent;
                    self.images = response.fileContent;
                    self.broadcastPageContent();
                }).error(function(response){
                    console.log(response);
                });
                
            });
        }
        
        self.getText = function(category, element){
            var text = false;
            for(var i=0; i<self.texts.length; i++){
                var tmpText = self.texts[i];
                if(tmpText.category == category && tmpText.element == element){
                    text = tmpText.text;
                }
            }
            return text;
        }
        self.setText = function(category, element, text){
            var found = false;
            for(var i=0; i<self.texts.length; i++){
                var tmpText = self.texts[i];
                if(tmpText.category == category && tmpText.element == element){
                    self.texts[i].text = text;
                    found = true;
                }
            }
            // if there is no such element -> create new one
            if(!found){
                var tmpText = {
                    category: category,
                    element: element,
                    text: text
                }
                self.texts.push(tmpText);
            }
            self.broadcastPageContent();
        }
        self.getImage = function(category,element){
            var src = false;
            for(var i=0; i<self.images.length; i++){
                var tmpImage = self.images[i];
                if(tmpImage.category == category && tmpImage.element == element){
                    src = tmpImage.url;
                }
            }
            return src;
        }
        self.setImage = function(category, element, url){
            var found = false;
            for(var i=0; i<self.images.length; i++){
                var tmpImage = self.images[i];
                if(tmpImage.category == category && tmpImage.element == element){
                    self.images[i].url = url;
                    found = true;
                }
            }
            if(!found){
                var tmpImage = {
                    category: category,
                    element: element,
                    url: url
                }
                self.images.push(tmpImage);
            }
            self.broadcastPageContent();
        }
        
        self.refreshContent = function(){
            self.fetchContent();
        }
        
        self.broadcastPageContent = function(){
            $rootScope.$broadcast('AcoPageContentChanged');
        }
        self.broadcastEditMode = function(){
            $rootScope.$broadcast('AcoEditModeChanged');
        }
        
        return self;
        
    }])
    
    
    /*
    
        ---------------- Controllers ----------------
    
    */
    .controller('AcoRootController',['$scope','$http','$timeout','AcoPageContentService','AcoLoginService',function($scope,$http,$timeout,AcoPageContentService,AcoLoginService){
        
        var self = this;
        
        init();
        function init(){
            $timeout(function(){
                AcoLoginService.checkLoginState();
            });
        }
        
        // Listener to Login State
        $scope.$on('AcoLoginStateChanged',function(){
            AcoPageContentService.fetchContent();
        });
        
    }]);
    
})();


function CreateRequest(callback){
    var length = 10;
    var chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOP1234567890";
    var token = "";
    for (var x = 0; x < length; x++) {
        var i = Math.floor(Math.random() * chars.length);
        token += chars.charAt(i);
    }
    document.cookie = "aco-key=" + token + ";path=/";
    callback(token);
}


$(document).ready(function(){
    
    $("#clickEvent").click(function(){
        alert("this is a jQuery click event");
    });
    
});