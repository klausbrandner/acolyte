(function(){
    
    angular.module('Acolyte')
    
    .factory('AcoLoginService',['$rootScope','$http','AcoNotificationService',function($rootScope,$http,AcoNotificationService){
        
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
                    self.broadcastLoginStatus();
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
                
                $http.post(acolyte.pathToServer + 'user/login', postData).success(function(response){
                    console.log(response);
                    // http -> login
                    self.loggedIn = true;
                    AcoNotificationService.push('success','Logged in','You where successfully logged in.');
                    self.broadcastLoginStatus();
                }).error(function(response){
                    console.log(response);
                });
                
            });
            
        }
        self.logout = function(){
            // http -> logout
            self.loggedIn = false;
            AcoNotificationService.push('success','Logged out','You where successfully logged out.');
            self.broadcastLoginStatus();
        }
        
        self.broadcastLoginStatus = function(){
            $rootScope.$broadcast('AcoLoginStateChanged');
        }
        
        return self;
        
    }]);
    
})();