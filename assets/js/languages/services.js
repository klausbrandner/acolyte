(function(){
    
    angular.module('Acolyte')
    
    .factory('AcoLanguageService',['$rootScope','$http','AcoPageContentService',function($rootScope,$http,AcoPageContentService){
        
        var self = this;
        
        self.lan = 'en';
        self.languages = [
            {
                lan: 'en',
                language: 'English',
                toggle: 1
            }
        ];
        self.availableLanguages = [];
        
        self.initLanguages = function(lancode, languages){
            self.lan = lancode;
            self.languages = languages;
            self.broadcastLanguagesChanged();
        }
        self.initAvailLanguages = function(languages){
            self.availableLanguages = languages;
        }
        
        self.getLan = function(){
            return self.lan;
        }
        self.setLan = function(lancode){
            CreateRequest(function(token){
                $http.put(acolyte.pathToServer + 'language/set/' + lancode).success(function(response){
                    AcoPageContentService.setContent(response);
                    self.lan = response.lan;
                    self.broadcastLanguagesChanged();
                }).error(function(response){
                    console.log(response);
                });
            });
        }
        
        self.getLanguages = function(){
            return self.languages;
        }
        self.searchAvailLans = function(keyword){
            var result = [];
            if(keyword != "" && keyword != null && keyword.length > 1){
                for(var l in self.availableLanguages){
                    var tmpLan = self.availableLanguages[l];
                    var lowerLan = tmpLan.language.toLowerCase();
                    if(tmpLan.lan == keyword.toLowerCase() || lowerLan.indexOf(keyword.toLowerCase()) >= 0){
                        result.push(self.availableLanguages[l]);
                    }
                }
            }
            return result;
        }
        
        
        self.setToggle = function(lan){
            // http -> set toggle
            CreateRequest(function(token){
                
                var setTo = 0;
                if(lan.toggle == 0){
                    setTo = 1;
                }
                
                var postData = {
                    toggle: setTo,
                    token: token
                };
                $http.put(acolyte.pathToServer + 'language/set/toggle/' + lan.lan, postData).success(function(response){
                    console.log(response);
                    for(var l in self.languages){
                        if(self.languages[l].lan == lan.lan){
                            self.languages[l].toggle = setTo;
                        }
                    }
                }).error(function(response){
                    console.log(response);
                });
            });
            
        }
        self.deleteLanguage = function(lan){
            // http -> delete language
            CreateRequest(function(token){
                $http.delete(acolyte.pathToServer + 'language/remove/lan/' + lan.lan).success(function(response){
                    console.log(response);
                    DeleteLanguage(lan,response);
                    self.broadcastLanguagesChanged();
                }).error(function(response){
                    console.log(response);
                });
            });
        }
        self.deleteAndTexts = function(lan){
            // http -> delete language
            CreateRequest(function(token){
                $http.delete(acolyte.pathToServer + 'language/remove/all/' + lan.lan).success(function(response){
                    console.log(response);
                    DeleteLanguage(lan,response);
                    self.broadcastLanguagesChanged();
                }).error(function(response){
                    console.log(response);
                });
            });
        }
        
        function DeleteLanguage(lan, content){
            console.log("delete lan: " + lan.lan);
            var i = 0;
            for(var l in self.languages){
                if(self.languages[l].lan == lan.lan){
                    self.languages.splice(i,1);
                }
                i++;
            }
            self.lan = content.lan;
            AcoPageContentService.setContent(content);
        }
        
        self.addLanguage = function(lan){
            // http -> add lan
            CreateRequest(function(token){
                var postData = {
                    lan: lan.lan,
                    language: lan.language,
                    token: token
                };
                $http.post(acolyte.pathToServer + 'language/add', postData).success(function(response){
                    console.log(response);
                    //lan.toggle = 0;
                    //self.languages.push(lan);
                    self.languages = response.language;
                    console.log(self.languages);
                    self.broadcastLanguagesChanged();
                }).error(function(response){
                    console.log(response);
                });
            });
        }
        
        
        self.broadcastLanguagesChanged = function(){
            $rootScope.$broadcast('AcoLanguagesChanged');
        }
        
        return self;
        
    }]);
    
})();