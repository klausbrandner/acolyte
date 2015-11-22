
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
    
    .factory('AcoPageContentService',['$rootScope',function($rootScope){
        
        var self = {};
        
        self.texts = {};
        self.images = {};
        
        self.edit = false;
        
        self.setEdit = function(setTo){
            if(setTo){
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
            
            // http --> get content
            var content = {
                textContent:
                [
                    {
                        category: 'home',
                        element: 'header',
                        text: 'Say Hello To Acolyte!',
                        lan: 'en'
                    },
                    {
                        category: 'home',
                        element: 'subtitle',
                        text: 'The Angularjs based CMS framework for web-developers',
                        lan: 'en'
                    },
                    {
                        category: 'home',
                        element: 'text1',
                        text: 'Hello, this is just an example text. You should be able to edit this text by clicking and holding the mouse pointer.',
                        lan: 'en'
                    },
                    {
                        category: 'home',
                        element: 'text2',
                        text: 'This is a link.',
                        lan: 'en'
                    }
                ],
                fileContent:
                [
                    {
                        category: 'home',
                        element: 'banner',
                        src: 'src/images/image2.jpg'
                    },
                    {
                        category: 'home',
                        element: 'image1',
                        src: 'src/images/eys2.jpg'
                    },
                    {
                        category: 'home',
                        element: 'image2',
                        src: 'src/images/image3.jpg'
                    }
                ]
            }
            
            self.texts = content.textContent;
            self.images = content.fileContent;
            
            console.log(self.texts);
            console.log(self.images);
            
            self.broadcastPageContent();
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
        self.getImage = function(category,element){
            var src = false;
            for(var i=0; i<self.images.length; i++){
                var tmpImage = self.images[i];
                if(tmpImage.category == category && tmpImage.element == element){
                    src = tmpImage.src;
                }
            }
            return src;
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
                AcoPageContentService.fetchContent();
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