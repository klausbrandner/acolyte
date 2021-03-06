(function (){

    angular.module('Acolyte')

    .controller('AcoImageController',['$scope','AcoPageContentService','AcoLoginService','AcoImageUploadService',function($scope,AcoPageContentService,AcoLoginService,AcoImageUploadService){

        var self = this;

        // init variables
        self.editable = false;
        self.src = acolyte.tmpImage;

        // init controller to fetch the image
        init();
        function init(){
            var src = AcoPageContentService.getImage($scope.category,$scope.element);
            if(!src){
                src = acolyte.tmpImage;
            }
            self.src = src;
        }


        // click event on image
        self.edit = function(e){
            if(AcoLoginService.getLoginState()){
                if(self.editable){
                    e.preventDefault();
                    e.stopPropagation();

                    // trigger click event on file upload input
                    $("#aco-img-upload-box").find("#aco-img-upload-input").trigger("click");
                    AcoImageUploadService.setTmpImage({category: $scope.category, element: $scope.element});
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
    .controller('AcoBackgroundController',['$scope','AcoPageContentService','AcoLoginService','AcoImageUploadService',function($scope,AcoPageContentService,AcoLoginService,AcoImageUploadService){

        var self = this;

        self.editable = false;

        self.edit = function(e){
            if(AcoLoginService.getLoginState()){
                // trigger click event on file upload input
                $("#aco-img-upload-box").find("#aco-img-upload-input").trigger("click");
                AcoImageUploadService.setTmpImage({category: $scope.category, element: $scope.element});
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



    // Controller for uploading both, aco-background and aco-img Images
    .controller('AcoImageUploadController',['$scope','AcoNotificationService','AcoImageUploadService','AcoPageContentService',function($scope,AcoNotificationService,AcoImageUploadService,AcoPageContentService){

        var self = this;

        self.status = "Preparing upload...";
        self.percentage = "please wait";
        self.tmpImage = {};

        init();
        function init(){
            $(document).on("change","#aco-img-upload-box #aco-img-upload-input",function(){

                var files = $(this)[0].files;
                if(files.length > 0){
                    self.status = "Preparing upload...";
                    self.percentage = "please wait";
                    $scope.$apply();
                    $("#aco-img-upload-box").css({"display":"table","opacity":"1"});
                    var fileExt = $(this).val().split('.').pop();
                    if(fileExt === 'JPG'){
                        fileExt = "jpg";
                    }
                    var allowed_file_types = ['jpg','jpeg','png','gif','bmp','svg'];
                    if(allowed_file_types.indexOf(fileExt) >= 0){
                        UploadFile(files[0]);
                    }else{
                        AcoNotificationService.push('error','Invalid file', 'We are sorry, but this file-type is not supported.');
                    }
                }

            });
        }

        function UploadFile(file){

            CreateRequest(function(token){
                self.status = "Uploading image...";
                $scope.$apply();
                var url = acolyte.pathToServer + 'content/file/edit/' + self.tmpImage.category + '/' + self.tmpImage.element;
                var dataPost = new FormData();
                dataPost.append('image',file);
                dataPost.append('token', token);

                $.ajax({
                    url: url,
                    data: dataPost,
                    type: 'POST',
                    processData: false,
                    contentType: false,
                    mimeType: 'multipart/form-data',
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = evt.loaded / evt.total;
                                //Do something with upload progress here
                                $("#aco-img-upload-box").find("#aco-img-upload-progress").css({"width":percentComplete*100+"%"});
                                self.percentage = Math.round(percentComplete * 100) + "%";
                                $scope.$apply();
                            }
                       }, false);

                       xhr.addEventListener("progress", function(evt) {
                           if (evt.lengthComputable) {
                               var percentComplete = evt.loaded / evt.total;
                               //Do something with download progress
                                $("#aco-img-upload-box").find("#aco-img-upload-progress").css({"width":percentComplete*100+"%"});
                                self.percentage = Math.round(percentComplete * 100) + "%";
                                $scope.$apply();
                           }
                       }, false);

                       return xhr;
                    },
                    success: function(data){
                        data = JSON.parse(data);
                        console.log("success");
                        console.log(data);

                        $("#aco-img-upload-box").find("#aco-img-upload-progress").css({"width":"0%"});
                        $("#aco-img-upload-box").animate({"opacity":"0"},400,function(){
                            $("#aco-img-upload-box").css({"display":"none"});
                        });
                        AcoPageContentService.setImage(self.tmpImage.category, self.tmpImage.element, data.fileContent.url)
                        AcoNotificationService.push('success','Image uploaded','Image has been successfully uploaded.');
                        $scope.$apply();
                    },
                    error:function(data,textStatus,xhr){
                        data = JSON.parse(data);
                        console.log("error");
                        console.log(data);
                        $("#aco-img-upload-box").find("#aco-img-upload-progress").css({"width":"0%"});
                        $("#aco-img-upload-box").animate({"opacity":"0"},400,function(){
                            $("#aco-img-upload-box").css({"display":"none"});
                        });
                        AcoNotificationService.push("error","Unknown error","Sorry, an unknown error occured while processing your request!");
                        $scope.$apply();
                    }
                });
            });
        }

        // Listener when there is a new image to be uploaded
        $scope.$on('AcoNewUploadImage',function(){
            self.tmpImage = AcoImageUploadService.getTmpImage();
        });

    }]);


})();
