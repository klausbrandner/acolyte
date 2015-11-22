(function(){
    
    angular.module('Acolyte')
    
    .controller('AcoImageController',['$scope','$timeout','AcoPageContentService','AcoLoginService',function($scope,$timeout,AcoPageContentService,AcoLoginService){
        
        var self = this;
        
        // init variables
        self.editable = false;
        self.src = acolyte.tmpImage;
        
        
        // click event on image
        self.edit = function(e){
            if(AcoLoginService.getLoginState()){
                if(self.editable){
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // trigger click event on file upload input
                    $("#aco-img-upload-box").find("#aco-img-upload-input").trigger("click");
                }
            }
        }
        
        
        // Listener when PageContent gets updated
        $scope.$on('AcoPageContentChanged',function(){
            var src = AcoPageContentService.getImage($scope.category,$scope.element);
            if(!src){
                src = acolyte.tmpImage;
            }
            self.src = src;
        });
        
        // Listener when Edit-Mode gets activated
        $scope.$on('AcoEditModeChanged',function(){
            self.editable = AcoPageContentService.getEditMode();
        });
        
        
    }])
    
    
    // Background Controller
    .controller('AcoBackgroundController',['$scope','AcoPageContentService','AcoLoginService',function($scope,AcoPageContentService,AcoLoginService){
        
        var self = this;
        
        self.editable = false;
        
        self.edit = function(e){
            if(AcoLoginService.getLoginState()){
                // trigger click event on file upload input
                $("#aco-img-upload-box").find("#aco-img-upload-input").trigger("click");
            }
        }
        
        // Listener when Edit-Mode gets activated
        $scope.$on('AcoEditModeChanged',function(){
            self.editable = AcoPageContentService.getEditMode();
        });
        
        // Listener to Login State
        $scope.$on('AcoLoginStateChanged',function(){
            if(!AcoLoginService.getLoginState()){
                self.editable = false;
            }
        });
        
    }])
    
    
    // Image Upload Controller
    .controller('AcoImageUploadController',['$scope',function($scope){
        
        var self = this;
        
        self.status = "Preparing upload...";
        self.percentage = "please wait";
        
        init();
        function init(){
            $(document).on("change","#aco-img-upload-box #aco-img-upload-input",function(){
                
                var files = $(this)[0].files;
                if(files.length > 0){
                    self.status = "Preparing upload...";
                    self.percentage = "please wait";
                    $scope.$apply();
                    $("#aco-img-upload-box").css({"display":"table"});
                    convertImgToBase64URL(files[0],uploadFile,"image/png");
                }
                
            });
        }
        
        function convertImgToBase64URL(file, callback, outputFormat){
            
            var img = new Image();
            
            var fileReader = new FileReader();
            fileReader.onload = function(fileLoadedEvent){
                img.src = fileLoadedEvent.target.result;
            }
            fileReader.readAsDataURL(file);
            
            img.onload = function(){
                var canvas = document.createElement('canvas'),
                ctx = canvas.getContext('2d'), dataURL;
                canvas.height = img.height;
                canvas.width = img.width;
                ctx.drawImage(img, 0, 0);
                dataURL = canvas.toDataURL(outputFormat);
                callback(dataURL);
                canvas = null;
            }
        }
        
        function uploadFile(dataUrl){
            self.status = "Uploading image...";
            self.percentage = "60%";
            $scope.$apply();
        }
        
        
    }]);
    
    
})();