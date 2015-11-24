(function(){
    
    angular.module('Acolyte')
    
    .factory('AcoLoginService',['$rootScope','$http',function($rootScope,$http){
        
        var self = {};
        
        self.loggedIn = false;
        
        self.getLoginState = function(){
            return self.loggedIn;
        }
        
        self.checkLoginState = function(){
            
            CreateRequest(function(token){
                
                $http.get(acolyte.pathToServer + 'user/view').success(function(response){
                    console.log(response);
                    if(response.user != null){
                        self.loggedIn = true;
                    }
                    self.broadcastLoginStatus();
                }).error(function(response){
                    console.log(response);
                });
                
            });
            
        }
        
        self.login = function(username, password){
            
            CreateRequest(function(token){
                
                var postData = {
                    username: username,
                    password: password,
                    token: token
                }
                
                $http.get(acolyte.pathToServer + '/user/login').success(function(response){
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