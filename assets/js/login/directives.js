(function(){
    
    angular.module('Acolyte')
    
    .directive('acoLogin',[function(){
        return{
            controller: 'AcoLoginButtonController',
            controllerAs: 'acoLoginBtnCtrl',
            scope: {},
            templateUrl: acolyte.pathToAcolyte + 'templates/aco-login-btn.html'
        };
    }])
    
    .directive('acoLoginBox',[function(){
        return{
            controller: 'AcoLoginBoxController',
            controllerAs: 'acoLoginBoxCtrl',
            scope: {},
            templateUrl: acolyte.pathToAcolyte + 'templates/aco-login-box.html'
        };
    }]);
    
    
})();