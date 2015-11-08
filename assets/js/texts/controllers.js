(function(){
    
    angular.module('Acolyte')
    
    .controller('AcoTextController',['$sce','$scope','$interval','AcoPageContentService',function($sce,$scope,$interval,AcoPageContentService){
        
        var self = this;
        
        // init variables
        self.editable = false;
        self.timer = {};
        self.text = acolyte.tmpText;
        
        // click Event on text
        self.edit = function(e){
            if(self.editable){
                e.preventDefault();
                e.stopPropagation();
                
                $interval.cancel(self.timer);
                self.timer = $interval(function(){
                    UpdateText();
                },acolyte.updateRate);
            }
        }
        
        // send request to server
        self.update = function(){
            $interval.cancel(self.timer);
            UpdateText();
        }
        
        function UpdateText(){
            // http -> update text
            var postData = {
                category: $scope.category,
                element: $scope.element,
                text: self.text
            }
            console.log(postData);
        }
        
        // Listener when PageContent gets updated
        $scope.$on('AcoPageContentChanged',function(){
            var txt = AcoPageContentService.getText($scope.category,$scope.element);
            if(!txt){
                txt = acolyte.newText;
            }
            self.text = txt;
        });
        
        // Listener when Edit-Mode gets activated
        $scope.$on('AcoEditModeChanged',function(){
            self.editable = AcoPageContentService.getEditMode();
        });
        
        
    }]);
    
})();