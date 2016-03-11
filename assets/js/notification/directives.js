(function(){
    
    angular.module('Acolyte')
    
    .directive('acoNotificationBox', function(){
        return{
            restrict: 'E',
            controller: 'AcoNotificationController',
            controllerAs: 'notifyCtrl',
            templateUrl: acolyte.pathToAcolyte + 'templates/notification-panel.html'
        };
    });
    
})();