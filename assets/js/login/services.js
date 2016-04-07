(function(){

    angular.module('Acolyte')

    .factory('AcoLoginService',['$rootScope','$http','AcoNotificationService','AcoLanguageService',function($rootScope,$http,AcoNotificationService,AcoLanguageService){

        var self = {};

        self.loggedIn = false;

        self.getLoginState = function(){
            return self.loggedIn;
        }

        self.checkLoginState = function(){

            CreateRequest(function(token){

                $http.get(acolyte.pathToServer + 'user/view').success(function(response){
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
                    self.loggedIn = true;
                    AcoNotificationService.push('success','Welcome to Acolyte CMS','Successfully logged in.');
                    self.broadcastLoginStatus();
                }).error(function(response){
                    AcoNotificationService.push(response.type,response.title,response.message);
                });

            });

        }
        self.logout = function(){
            CreateRequest(function(token){
                $http.put(acolyte.pathToServer + 'user/logout').success(function(response){
                    self.loggedIn = false;
                    AcoLanguageService.setLanOnly(response.lan);
                    AcoNotificationService.push('success','Good Bye!','Successfully logged out.');
                    self.broadcastLoginStatus();
                }).error(function(response){
                    console.log(response);
                    AcoNotificationService.push(response.type,response.title,response.message);
                });
            });
        }

        self.broadcastLoginStatus = function(){
            $rootScope.$broadcast('AcoLoginStateChanged');
        }

        return self;

    }]);

})();
