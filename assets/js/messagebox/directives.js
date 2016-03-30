(function(){

    angular.module('Acolyte')

    .directive('acoMessageBox',function(){
        return{
            restrict: 'E',
            controller: 'AcoMessageBoxController',
            controllerAs: 'acoMBCtrl',
            scope: {},
            templateUrl: acolyte.pathToAcolyte + 'templates/message-box.html'
        };
    })

})();
