
/**

    Global Variable

*/

var acolyte = {
    pathToAcolyte: 'acolyte/',
    pathToServer: 'acolyte/server/',
    tmpImage: 'acolyte/src/black.png',
    tmpText: '...',
    newText: "New Acolyte Text",
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
        self.initialized = false;
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
                    SetNewContent(response);
                }).error(function(response){
                    console.log(response);
                });
            });
        }

        self.setContent = function(content){
            SetNewContent(content);
        }

        function SetNewContent(content){
            self.texts = content.textContent;
            self.images = content.fileContent;
            self.broadcastPageContent();
            if(!self.initialized){
                if(typeof pageContentLoaded !== 'undefined'){
                    pageContentLoaded();
                }
                self.initialized = true;
            }
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
    document.cookie = "aco-token=" + token + ";path=/";
    callback(token);
}
