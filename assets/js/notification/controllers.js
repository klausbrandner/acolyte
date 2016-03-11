(function(){
    
    angular.module('Acolyte')
    
    .controller('AcoNotificationController',['$scope','$timeout','AcoNotificationService',function($scope,$timeout,AcoNotificationService){
        
        var self = this;
        
        self.notification = {};
        self.timer = null;
        
        self.push = function(notification){
            if(self.timer != null){
                $timeout.cancel(self.timer);
            }
            $("#acoNotificationPanel").hide("slide",200,function(){
                self.notification = notification;
                $("#acoNotificationPanel").show("slide",200,function(){
                    self.timer = $timeout(function(){
                        $("#acoNotificationPanel").hide("slide",200);
                    },5000);
                });
            });
        }
        
        // Listener to Login State
        $scope.$on('AcoPushNotification',function(){
            var notification = AcoNotificationService.getNotification();
            self.push(notification);
        });
        
    }]);
    
})();