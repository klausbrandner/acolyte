(function(){
    
    angular.module('Acolyte')
    
    .controller('AcoLoginButtonController',['$scope','AcoLoginService',function($scope,AcoLoginService){
        
        var self = this;
        
        self.loggedIn = false;
        
        self.openLoginPanel = function(){
            if(self.loggedIn){
                // logout
                AcoLoginService.logout();
            }else{
                // open panel
                $("#acoLoginPanel").stop().fadeIn(400);
            }
        }
        
        // Listener to Login State
        $scope.$on('AcoLoginStateChanged',function(){
            self.loggedIn = AcoLoginService.getLoginState();
        });
    }])
    
    .controller('AcoLoginBoxController',['$scope','AcoLoginService',function($scope,AcoLoginService){
        
        var self = this;
        
        self.loginData = {};
        
        self.login = function(){
            AcoLoginService.login(self.loginData.username,self.loginData.password);
        }
        self.close = function(){
            $("#acoLoginPanel").stop().fadeOut(400);
        }
        
        // Listener to Login State
        $scope.$on('AcoLoginStateChanged',function(){
            $("#acoLoginPanel").stop().fadeOut(400);
            self.loginData = {};
        });
        
    }]);
    
})();