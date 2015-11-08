(function(){
    
    angular.module('Acolyte')
    
    .directive('acoAdmin',function(){
        return{
            restrict: 'E',
            controller: 'AcoAdminController',
            controllerAs: 'acoAdminCtrl',
            templateUrl: acolyte.pathToAcolyte + 'templates/aco-admin.html'
        };
    });
    
})();