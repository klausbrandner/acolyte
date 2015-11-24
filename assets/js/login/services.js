(function(){
    
    angular.module('Acolyte')
    
    .factory('AcoLoginService',['$rootScope','$http',function($rootScope,$http){
        
        var self = {};
        
        self.loggedIn = false;
        
        self.getLoginState = function(){
            return self.loggedIn;
        }
        
        self.login = function(username, password){
            
            CreateRequest(function(token){
                
                var postData = {
                    username: username,
                    password: password,
                    token: token
                }
                
                $http.post(acolyte.pathToServer + '/user/login', postData).success(function(response){
                    console.log(response);
                    // http -> login
                    self.loggedIn = true;
                    self.broadcastLoginStatus();
                }).error(function(response){
                    console.log(response);
                });
                
            });
            
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