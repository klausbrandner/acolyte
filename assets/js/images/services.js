(function(){
    
    angular.module('Acolyte')
    
    .factory('AcoImageUploadService',['$rootScope',function($rootScope){
        
        var self = this;
        
        self.tmpImage = {};
        
        self.setTmpImage = function(tmpImage){
            self.tmpImage = tmpImage;
            self.broadcastNewTmpImage();
        }
        
        self.getTmpImage = function(){
            return self.tmpImage;
        }
        
        self.broadcastNewTmpImage = function(){
            $rootScope.$broadcast('AcoNewUploadImage');
        }
        
        return self;
        
    }]);
    
})();