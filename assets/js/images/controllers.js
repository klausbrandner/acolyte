(function(){
    
    angular.module('Acolyte')
    
    .controller('AcoImageController',['$scope','$timeout','AcoPageContentService','AcoLoginService',function($scope,$timeout,AcoPageContentService,AcoLoginService){
        
        var self = this;
        
        // init variables
        self.editable = false;
        self.src = acolyte.tmpImage;
        
        
        // click event on image
        self.edit = function(e){
            if(AcoLoginService.getLoginState()){
                if(self.editable){
                    e.preventDefault();
                    e.stopPropagation();
                    alert("update image");
                }
            }
        }
        
        
        // Listener when PageContent gets updated
        $scope.$on('AcoPageContentChanged',function(){
            var src = AcoPageContentService.getImage($scope.category,$scope.element);
            if(!src){
                src = acolyte.tmpImage;
            }
            self.src = src;
        });
        
        // Listener when Edit-Mode gets activated
        $scope.$on('AcoEditModeChanged',function(){
            self.editable = AcoPageContentService.getEditMode();
        });
        
        
    }])
    
    
    // Background Controller
    .controller('AcoBackgroundController',['$scope','AcoPageContentService',function($scope,AcoPageContentService){
        
        var self = this;
        
        self.edit = function(e){
            alert("update");
        }
        
    }]);
    
    
})();