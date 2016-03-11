(function(){
    
    angular.module('Acolyte')
    
    .directive('acoLogin',[function(){
        return{
            controller: 'AcoLoginButtonController',
            controllerAs: 'loginCtrl',
            scope: {},
            templateUrl: acolyte.pathToAcolyte + 'templates/login-button.html'
        };
    }])
    
    .directive('acoLoginBox',[function(){
        return{
            controller: 'AcoLoginBoxController',
            controllerAs: 'loginCtrl',
            scope: {},
            templateUrl: acolyte.pathToAcolyte + 'templates/login-panel.html',
            link: function(scope, element, attr){
                element.click(function(event){
                    if(!$(event.target).is("#acoLoginBox") && $("#acoLoginBox").has(event.target).length===0){
                        $("#acoLoginPanel").stop().fadeOut(400);
                    }
                });
            }
        };
    }]);
    
    
})();