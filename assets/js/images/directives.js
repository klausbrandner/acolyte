(function(){
    
    angular.module('Acolyte')
    
    .directive('acoImg',function(){
        return{
            restrict:'AC',
            controller: 'AcoImageController',
            controllerAs: 'acoImgCtrl',
            replace: true,
            scope:{
                category:'@',
                element:'@'
            },
            templateUrl: acolyte.pathToAcolyte + 'templates/aco-image.html'
        };
    })
    .directive('acoBackground',['$compile','AcoPageContentService',function($compile,AcoPageContentService){
        return{
            restrict: 'EC',
            controller: 'AcoBackgroundController',
            controllerAs: 'acoImgCtrl',
            scope:{
                category: '@',
                element: '@'
            },
            link: function(scope,elem,attr){

                var img = acolyte.tmpImage;
                var size = elem.css('background-size');
                var pos = elem.css('background-position');
                var atta = elem.css('background-attachment');
                var repeat = elem.css('background-repeat');
                var style = 'background:url('+img+');';
                style += 'background-size:'+size+';';
                style += 'background-position:'+pos+';';
                style += 'background-attachment:'+atta+';';
                style += 'background-repeat:'+repeat+';';
                attr.$set('style',style);
                
                scope.$on('AcoPageContentChanged',function(){
                    var c = attr.category;
                    var e = attr.element;
                    var img = AcoPageContentService.getImage(c,e);
                    var size = elem.css('background-size');
                    var pos = elem.css('background-position');
                    var atta = elem.css('background-attachment');
                    var repeat = elem.css('background-repeat');
                    var style = 'background:url('+img+');';
                    style += 'background-size:'+size+';';
                    style += 'background-position:'+pos+';';
                    style += 'background-attachment:'+atta+';';
                    style += 'background-repeat:'+repeat+';';
                    attr.$set('style',style);
                    elem.find("aco-update-img-btn").remove();
                    elem.append($compile('<aco-update-img-btn ng-show="acoImgCtrl.editable" ng-click="acoImgCtrl.edit()">edit</div>')(scope));
                });
                
            }
        };
    }])
    .directive('acoImgUpload',function(){
        return{
            restrict: 'E',
            controller: 'AcoImageUploadController',
            controllerAs: 'acoImgUploadCtrl',
            scope:{},
            templateUrl: acolyte.pathToAcolyte + 'templates/aco-img-upload.html'
        };
    });
    
})();