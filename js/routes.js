(function(){
    angular.module('AcolyteWeb',['ngRoute','Acolyte'])
    .config(function($routeProvider){

        $routeProvider.when('/home',{
            title: 'home',
            templateUrl: 'pages/home.html'
        })
        .when('/gettingstarted',{
            title: 'gettingstarted',
            templateUrl: 'pages/intro.html'
        })
        .when('/',{
            title: 'home',
            templateUrl: 'pages/home.html'
        })
        .otherwise({ redirectTo: '/' });
    })
    
    
    .run(['$rootScope', function($rootScope) {
        
        $rootScope.$on("$routeChangeStart", function(event, next, current) {
            
        });
        
        $rootScope.$on('$routeChangeSuccess', function (event, current, previous) {
            $rootScope.title = current.$$route.title;
            
            $(".header").each(function(){
                $(this).hide();
            });
            
            $("#" + $rootScope.title + "Header").show();
            
        });
        
    }]);
    
})();