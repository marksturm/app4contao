/**
 * @author Mark Sturm - privat@mark-sturm.de
 * @license CC BY-NC-SA 4.0
*/

angular.module('ContaoApp.controllers', [])

.controller('AppCtrl', function($scope, $rootScope, $cordovaNetwork, $ionicPopup, $ionicNativeTransitions, $stateParams, StorageService, ContaoAppMenu, ContaoAppSocialMenu, ContaoAppLabel) {

  $rootScope.$on('$stateChangeStart', function(event, toState, toParams, fromState, fromParams){ 

  if(window.cordova && $cordovaNetwork.getNetwork()=='none') {
        $scope.noMoreItemsAvailable = true;
        $ionicPopup.alert({
          title: ContaoAppLabel.offlineTitle,
          content: ContaoAppLabel.offlineMsg
        })
      }
    })

  $scope.getTemplateUrl = function(element) {
      return 'templates/elements/'+element+'.html';
  }

  $scope.playVideo = function(id) {
    YoutubeVideoPlayer.openVideo(id);
  }

  $scope.goBack = function() {
    $ionicNativeTransitions.goBack();
  }

  $scope.openInAppBrowser = function(url)
  {
    window.open(url,'_blank');
  }

  $scope.mainmenu = ContaoAppMenu;

  $scope.socialmenu = ContaoAppSocialMenu;

})

.controller('NewslistsCtrl', function($scope, $stateParams, $ionicLoading, $timeout, $ionicScrollDelegate, $cordovaToast, DataService, ContaoAppConfig) {

    $ionicLoading.show();
    $scope.page = 0;
    $scope.items =[];
    
      $scope.doRefresh = function() {
            $scope.page = 1;
            $ionicLoading.show();
            $scope.loadNewsListDoRefresh();
      };

      $scope.loadNewsListDoRefresh = function() {     
        DataService.GetNewsList($scope.page,$stateParams.pid).then(function(items){
          $scope.items =[];
              if (items.data.response) {
                for(i in items.data.response){
                  $scope.items.push(items.data.response[i]);
                }
              }
          }).finally(function() {         
              $ionicLoading.hide();
              $ionicScrollDelegate.scrollTop();
              $scope.noMoreItemsAvailable = false;
            });
      };

      $scope.loadNewsList = function() {

        $scope.page++;
        
        DataService.GetNewsList($scope.page,$stateParams.pid).then(function(items){

              if (items.data.response) {
                for(i in items.data.response){
                    $scope.items.push(items.data.response[i]);
                  }
                $scope.noMoreItemsAvailable = false;
              } else {
                $scope.noMoreItemsAvailable = true;
              }
          }).finally(function() {
            $ionicLoading.hide();
            $scope.$broadcast('scroll.infiniteScrollComplete');
          })

      };

})

.controller('ContentCtrl', function($scope, $stateParams, $ionicLoading, $cordovaSocialSharing, $cordovaToast, DataService, StorageService, FindIndexByKeyValue, ContaoAppConfig, ContaoAppLabel) {

    var data = []
    $scope.elements =[]
    $scope.items =[]
    $scope.page = 0;
    $scope.ptable = $stateParams.ptable;
    $ionicLoading.show();
    $scope.addToBookmarks = function(objContent) {
     

      var allreadyinlist = FindIndexByKeyValue.functionIndexByKeyValue(StorageService.getContent(), "headline", objContent.headline);
      
      if (allreadyinlist==null) {
            StorageService.addContent(objContent);
            $scope.liked = true;
            $cordovaToast.showLongBottom(ContaoAppLabel.addedBookmark);
                }  else {
                    $cordovaToast.showLongBottom(ContaoAppLabel.deletedBookmark);
                    StorageService.removeContent(objContent);
                    $scope.liked = false;
                }
      }

    $scope.CheckNewsBookmarks = function(objContent) {

        var allreadyinlist = FindIndexByKeyValue.functionIndexByKeyValue(StorageService.getContent(), "headline", objContent.headline);

        if (allreadyinlist==null) {
              $scope.liked = false;
              }  else {
              $scope.liked = true;
            }
        }
    
    $scope.share = function (objContent) {
        
        var teaser = objContent.teaser.replace(/(<([^>]+)>)/ig,"");
        var headline = objContent.headline;
        var url = ContaoAppConfig.URL+objContent.newsurl;
        var image = ContaoAppConfig.fileURL+objContent.picture.img.src;
        
          var options = {
            message: teaser, // nicht supported von eingein apps (Facebook, Instagram)
            subject: headline, // z.B. für E-Mails
            files: [image], // array von files lokal und remote möglich
            url: url,
            chooserTitle: ContaoAppConfig.ShareTitle // Android only, hier kann man den default share sheet titel überschreiben
          }
          
        
        $cordovaSocialSharing.shareWithOptions(options);
        
      }  

    $scope.GetNewsHeader = function() {
        
        DataService.GetNewsEntry($stateParams.id).then(function(items){

              if (items.data.response) {
                  $scope.item = items.data.response[0];
                  $scope.CheckNewsBookmarks($scope.item);
                  $scope.noMoreItemsAvailable = false;
              } else {
                  $scope.noMoreItemsAvailable = true;
              }
          })
      };

    if($stateParams.ptable == 'tl_news') {
      $scope.GetNewsHeader();
    }

    $scope.loadElementList = function() {

        $scope.page++;
        DataService.GetContentList($stateParams.ptable,$scope.page,$stateParams.id).then(function(items){
              $ionicLoading.hide();             
              if (items.data.response) {
                for(i in items.data.response){
                  $scope.elements.push(items.data.response[i]);
                }
                $scope.noMoreItemsAvailable = false;
              } else {
                $scope.noMoreItemsAvailable = true;
              }
          }).finally(function() {
              $scope.$broadcast('scroll.infiniteScrollComplete');
          });

      };
})

.controller('ImageViewerCtrl', function($scope, $stateParams, $ionicLoading, DataService) {

        DataService.GetContentElement($stateParams.ptable,$stateParams.id).then(function(items){
                $scope.item = items.data.response[0].picture;
          }).finally(function() {
      });
})

.controller('GalleryViewerCtrl', function($scope, $stateParams, $ionicLoading, DataService, $ionicScrollDelegate, $ionicSlideBoxDelegate) {

     DataService.GetContentElement($stateParams.ptable,$stateParams.pid,$stateParams.id).then(function(items){
                $scope.elements = items.data.response[0].pictures;
          }).finally(function() {
      });
    
    // Startbild festlegen * timeout Hack. Wird benötigt um die ImageId zu übergeben. Script ist zu schnell.
     $scope.updateSlider = function () {
            $ionicSlideBoxDelegate.slide($stateParams.index);
            $ionicSlideBoxDelegate.update();
        }
   
})

.controller('BookmarksCtrl', function ($scope, $cordovaToast, StorageService, ContaoAppLabel) {
      $scope.items = StorageService.getContent();
        $scope.remove = function (item) {
          $cordovaToast.showLongBottom(ContaoAppLabel.deletedBookmark);
          StorageService.removeContent(item);
      };
})

