(function(){
    
    angular.module('Acolyte')
    
    .controller('AcoAdminController',['$scope','AcoPageContentService','AcoLoginService',function($scope,AcoPageContentService,AcoLoginService){
        
        var self = this;
        self.edit = false;
        self.showPanel = false;
        
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
        
        // Listener to Edit Mode
        $scope.$on('AcoEditModeChanged',function(){
            self.edit = AcoPageContentService.getEditMode();
        });
        
        // Listener to Login State
        $scope.$on('AcoLoginStateChanged',function(){
            self.showPanel = AcoLoginService.getLoginState();
            if(!self.showPanel){
                AcoPageContentService.setEdit(false);
            }
        });
        
    }]);
    
})();