(function(){
    
    angular.module('Acolyte')
    
    .factory('AcoLoginService',['$rootScope',function($rootScope){
        
        var self = {};
        
        self.loggedIn = false;
        
        self.getLoginState = function(){
            return self.loggedIn;
        }
        
        self.login = function(username, password){
            // http -> login
            self.loggedIn = true;
            self.broadcastLoginStatus();
        }
        self.logout = function(){
            // http -> logout
            self.loggedIn = false;
            self.broadcastLoginStatus();
        }
        
        self.broadcastLoginStatus = function(){
            $rootScope.$broadcast('AcoLoginStateChanged');
        }
        
        return self;
        
    }]);
    
})();