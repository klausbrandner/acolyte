(function(){
    
    angular.module('Acolyte')
    
    .controller('AcoMessageBoxController',['$scope','AcoMessageBoxService',function($scope,AcoMessageBoxService){
        
        var self = this;
        self.title = "";
        self.message = "";
        self.buttons = [];
        
        self.doCallback = function(callback){
            callback();
            HideMessageBox();
        }
        
        self.cancel = function(){
            HideMessageBox();
        }
        function HideMessageBox(){
            $("aco-message-box").hide("fade",300,function(){
                self.title = "";
                self.message = "";
                self.buttons = [];
            });
        }
        
        $scope.$on('AcoMessageBoxDataChanged',function(){
            var messageData = AcoMessageBoxService.getMessageData();
            self.title = messageData.title;
            self.message = messageData.message;
            self.buttons = messageData.buttons;
            $("aco-message-box").fadeIn(300);
        });
        
    }]);
    
})();