(function(){
    
    angular.module('Acolyte')
    
    .controller('AcoAdminController',['$scope','AcoPageContentService','AcoLoginService',function($scope,AcoPageContentService,AcoLoginService){
        
        var self = this;
        self.edit = false;
        self.showPanel = false;
        self.dragImg = acolyte.pathToAcolyte + 'src/dragW.png';
        
        init();
        function init(){
            $("aco-admin").draggable({ 
                handle: '#aco-admin-drag',
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
            var lanBox = $("#aco-admin-panel").find("#aco-admin-lan-box");
            var adminTable = $("#aco-admin-panel").find("#aco-admin-table");
            
            if(lanBox.css("display") == "none"){
                adminTable.find("#aco-admin-lan").addClass("active");
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
                adminTable.find("#aco-admin-lan").removeClass("active");
            }
            lanBox.slideToggle(200);
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