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
                $("aco-login-box").fadeIn();
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
            $("aco-login-box").fadeOut();
        }
        
        // Listener to Login State
        $scope.$on('AcoLoginStateChanged',function(){
            $("aco-login-box").fadeOut();
        });
        
    }]);
    
})();