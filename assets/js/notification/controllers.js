(function(){
    
    angular.module('Acolyte')
    
    .controller('AcoNotificationController',['$scope','$timeout','AcoNotificationService',function($scope,$timeout,AcoNotificationService){
        
        var self = this;
        
        self.notification = {};
        self.timer = null;
        
        // Listener to Login State
        $scope.$on('AcoPushNotification',function(){
            if(self.timer != null){
                $timeout.cancel(self.timer);
            }
            $("aco-notification-box").hide("slide",200,function(){
                self.notification = AcoNotificationService.getNotification();
                $("aco-notification-box").show("slide",200,function(){
                    self.timer = $timeout(function(){
                        $("aco-notification-box").hide("slide",200);
                    },5000);
                });
            });
        });
        
    }]);
    
})();