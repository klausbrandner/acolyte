(function(){
    
    angular.module('Acolyte')
    
    .controller('AcoTextController',['$sce','$scope','$interval','$http','AcoPageContentService','AcoLoginService',function($sce,$scope,$interval,$http,AcoPageContentService,AcoLoginService){
        
        var self = this;
        
        // init variables
        self.editable = false;
        self.timer = null;
        self.text = acolyte.tmpText;
        
        // click Event on text
        self.edit = function(e){
            if(AcoLoginService.getLoginState()){
                if(self.editable){
                    e.preventDefault();
                    e.stopPropagation();

                    $interval.cancel(self.timer);
                    self.timer = $interval(function(){
                        UpdateText();
                    },acolyte.updateRate);
                }
            }
        }
        
        // send request to server
        self.update = function(){
            if(AcoLoginService.getLoginState()){
                $interval.cancel(self.timer);
                self.timer = null;
                UpdateText();
            }
        }
        
        function UpdateText(){
            if(AcoLoginService.getLoginState()){
                
                CreateRequest(function(token){
                    
                    var url = acolyte.pathToServer + 'content/text/set/modified/' + $scope.category + '/' + $scope.element;
                    var postData = {
                        text: self.text,
                        token: token
                    }
                    
                    $http.put(url,postData).success(function(response){
                        AcoPageContentService.setText($scope.category, $scope.element, response.textContent.text);
                    }).error(function(response){
                        console.log(response);
                    });
                    
                });
                
            }
        }
        
        // Listener when PageContent gets updated
        $scope.$on('AcoPageContentChanged',function(){
            var txt = AcoPageContentService.getText($scope.category,$scope.element);
            if(self.timer == null){
                if(!txt){
                    txt = acolyte.newText;
                }
                self.text = txt;
            }
        });
        
        // Listener when Edit-Mode gets activated
        $scope.$on('AcoEditModeChanged',function(){
            self.editable = AcoPageContentService.getEditMode();
        });
        
        
    }]);
    
})();