(function(){
    
    angular.module('Acolyte')
    
    .factory('AcoMessageBoxService',['$rootScope',function($rootScope){
        
        var self = this;
        
        self.messageData = {};
        
        self.getMessageData = function(){
            return self.messageData;
        }
        self.pushMessage = function(messageData){
            self.messageData = messageData;
            self.broadcastMessageDataChanged();
        }
        
        self.broadcastMessageDataChanged = function(){
            $rootScope.$broadcast('AcoMessageBoxDataChanged');
        }
        
        return self;
    }]);
    
})();