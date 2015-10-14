(function(){
    
    angular.module('Acolyte',[])
    
    .directive('acoText',function(){
        return{
            restrict: 'A',
            controller: 'TextController',
            controllerAs: 'tCtrl',
            templateUrl: 'templates/aco-text.html'
        }
    })
    .directive('acoImg',function(){
        return{
            restrict: 'A',
            controller: 'ImgController',
            controllerAs: 'iCtrl',
            templateUrl: 'templates/aco-image.html'
        }
    })
    
    .controller('TextController',['$scope',function($scope){
        
        var self = this;
        
        self.text = "We provide the perfect cms framework for your next web project.";
        
        self.update = function(){
            alert("update");
        }
        
    }])
    
    .controller('ImgController',['$scope',function($scope){
        
        var self = this;
        
        self.src = "src/images/image1.jpg";
        
        self.update = function(){
            alert("update");
        }
        
    }]);
    
})();