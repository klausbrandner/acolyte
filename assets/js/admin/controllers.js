(function(){

    angular.module('Acolyte')

    .controller('AcoAdminController',['$scope','$http','AcoPageContentService','AcoLoginService','AcoMessageBoxService','AcoNotificationService',function($scope,$http,AcoPageContentService,AcoLoginService,AcoMessageBoxService,AcoNotificationService){

        var self = this;
        self.edit = false;
        self.showPanel = false;
        self.img = {};
        self.img.drag = acolyte.pathToAcolyte + 'src/dragDots.svg';
        self.img.edit = acolyte.pathToAcolyte + 'src/editW.svg';

        init();
        function init(){
            $("#acoAdminPanel").draggable({
                handle: '#acoAdminDragBtn',
                containment: 'document',
                stop: function(){
                    var offPos = $(this).offset().top;
                    var winPos = $(window).scrollTop();
                    var screenHeight = screen.availHeight;
                    if(offPos < winPos){
                        $(this).css({"top":"0px"});
                    }
                    var bottomLimit = winPos + screenHeight;
                    if(offPos > bottomLimit){
                        $(this).css({"bottom":"0px"});
                    }
                }
            });
        }

        self.setEditMode = function(){
            AcoPageContentService.setEdit();
        }

        self.selectLan = function(){
            var lanBox = $("#acoAdminPanel").find("#acoAdminLanBox");
            var adminTable = $("#acoAdminPanel").find("#acoAdminTable");

            if(lanBox.css("display") == "none"){
                adminTable.find("#acoAdminLanBtn").addClass("active");
                var boxHeight = lanBox.outerHeight();
                var tableOffset = adminTable.offset().top - $(window).scrollTop();
                if(boxHeight > tableOffset){
                    lanBox.addClass('aco-lan-top');
                    lanBox.removeClass('aco-lan-bottom');
                }else{
                    lanBox.addClass('aco-lan-bottom');
                    lanBox.removeClass('aco-lan-top');
                }
            }else{
                adminTable.find("#acoAdminLanBtn").removeClass("active");
            }
            lanBox.slideToggle(200);
        }

        self.publish = function(){
            AcoMessageBoxService.pushMessage({
                title: "Publish your page",
                message: "Now, you can make your changes available for everyone. Are you sure that you want to publish your page?",
                buttons: [
                    {
                        title: "Yes, Publish",
                        callback: PublishAll
                    }
                ]
            });
        }

        function PublishAll(){
            CreateRequest(function(token){
                $http.put(acolyte.pathToServer + 'content/save/all').success(function(response){
                    AcoNotificationService.push("success","Page published","Yeah, your page content is now visible for everyone.");
                }).error(function(response){
                    console.log(response);
                });
            });
        }
        function PublishCurrent(){
            CreateRequest(function(token){
                $http.put(acolyte.pathToServer + 'content/save/lan').success(function(response){
                    AcoNotificationService.push("success","Page published","Yeah, your page content is now visible for everyone.");
                }).error(function(response){
                    console.log(response);
                });
            });
        }

        // Listener to Edit Mode
        $scope.$on('AcoEditModeChanged',function(){
            self.edit = AcoPageContentService.getEditMode();
        });

        // Listener to Login State
        $scope.$on('AcoLoginStateChanged',function(){
            self.showPanel = AcoLoginService.getLoginState();
            if(!self.showPanel){
                AcoPageContentService.setEdit(false);
            }
        });

    }]);

})();
