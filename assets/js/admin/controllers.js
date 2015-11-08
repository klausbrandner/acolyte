(function(){
    
    angular.module('Acolyte')
    
    .controller('AcoAdminController',['$scope','AcoPageContentService',function($scope,AcoPageContentService){
        
        var self = this;
        self.edit = false;
        
        init();
        function init(){
            $("aco-admin").draggable({ 
                handle: '#aco-admin-drag',
                containment: 'document'
            });
        }
        
        self.setEditMode = function(){
            AcoPageContentService.setEdit();
        }
        
        $scope.$on('AcoEditModeChanged',function(){
            self.edit = AcoPageContentService.getEditMode();
        });
        
    }]);
    
})();