(function(){
    
    angular.module('Acolyte')
    
    .factory('AcoNotificationService',['$rootScope',function($rootScope){
        
        var self = this;
        
        self.notification = {
            type: '',
            title: '',
            message: ''
        }
        
        self.push = function(type, title, message){
            self.notification.type = type;
            self.notification.title = title;
            self.notification.message = message;
            self.broadcastNotification();
        }
        
        self.getNotification = function(){
            return self.notification;
        }
        
        // boradcast to notification controller
        self.broadcastNotification = function(){
            $rootScope.$broadcast('AcoPushNotification');
        }
        
        return self;
        
    }]);
    
})();