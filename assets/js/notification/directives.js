(function(){
    
    angular.module('Acolyte')
    
    .directive('acoNotificationBox', function(){
        return{
            restrict: 'E',
            controller: 'AcoNotificationController',
            controllerAs: 'acoNotifyCtrl',
            templateUrl: acolyte.pathToAcolyte + 'templates/aco-notification-box.html'
        };
    });
    
})();