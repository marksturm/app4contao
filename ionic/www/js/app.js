/**
 * @author Mark Sturm - privat@mark-sturm.de
 * @license CC BY-NC-SA 4.0
*/

angular.module('ContaoApp', ['ionic','ContaoApp.controllers','ContaoApp.services','ionic-native-transitions','drawer','ngCordova','ngStorage','ngSanitize'])

.run(function($ionicPlatform,$rootScope,$cordovaNetwork,ContaoAppOneSignal) {

  $ionicPlatform.ready(function() {
    // Fontsizefix
    if(window.MobileAccessibility) {
      function setTextZoomCallback(textZoom) { }
      MobileAccessibility.setTextZoom(100, setTextZoomCallback);
    }

    $rootScope.$on('$stateChangeSuccess', function (evt, toState) {
      if (toState.changeColor) {
        $rootScope.changeColor = true;
      } else {
        $rootScope.changeColor = false;
      }
    });

    if (window.cordova && window.cordova.plugins.Keyboard) {
      cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);
      cordova.plugins.Keyboard.disableScroll(true);
    }
    if (window.StatusBar) {

      StatusBar.styleDefault();
    }
    
    if(window.cordova && window.plugins.OneSignal) {
  
      var notificationOpenedCallback = function(jsonData) {
        //alert("notification is received");
      };

      window.plugins.OneSignal.init(ContaoAppOneSignal.OneSignalKey,
                                     {googleProjectNumber: ContaoAppOneSignal.googleProjectNumber},
                                     notificationOpenedCallback);
      
      window.plugins.OneSignal.getIds(function(ids) {
        console.log("UserID: " + ids.userId);
        console.log("PushToken: " + ids.pushToken);
        console.log('getIds: ' + JSON.stringify(ids));
      });

      // Show an alert box if a notification comes in when the user is in your app.
      window.plugins.OneSignal.setSubscription(true);
      window.plugins.OneSignal.enableNotificationsWhenActive(true);
      window.plugins.OneSignal.enableInAppAlertNotification(false);
      }
  });
})

.config(function($ionicConfigProvider, $stateProvider, $urlRouterProvider, $ionicNativeTransitionsProvider) {

  $ionicConfigProvider.views.transition('none');

  $ionicConfigProvider.backButton.previousTitleText(false).text('');

  $ionicConfigProvider.scrolling.jsScrolling(false);
  
 if (ionic.Platform.isAndroid()) {
  
  var test = 'templates/menu.html';
  $ionicNativeTransitionsProvider.setDefaultOptions({
      duration: 300,
      slowdownfactor: 4,
      fixedPixelsTop: 44,
      backInOppositeDirection: true,
  })  

    } else {
  var test = 'templates/menu_ios.html';
  $ionicNativeTransitionsProvider.setDefaultOptions({
      duration: 300,
      slowdownfactor: 4,
      fixedPixelsTop: 64,
      backInOppositeDirection: true,
  })    

    }

  $stateProvider
    .state('ContaoApp', {
    url: '/app',
    abstract: true,
    templateUrl: test,
    controller: 'AppCtrl'
    })

    .state('ContaoApp.newslist', {
      url: '/newslist/:pid',
      views: {
        'menuContent': {
          templateUrl: 'templates/newslists.html',
          controller: 'NewslistsCtrl'
        }
      }
    })

  .state('ContaoApp.content', {
    url: '/content/:ptable/:id',
    views: {
      'menuContent': {
        templateUrl: 'templates/content.html',
        controller: 'ContentCtrl'
      }
    }
  })
  .state('ContaoApp.bookmarks', {
    url: '/bookmarks',
    views: {
      'menuContent': {
        templateUrl: 'templates/bookmarks.html',
        controller: 'BookmarksCtrl'
      }
    }
  })
 .state('ContaoApp.imageviewer', {
    url: '/imageviewer/:ptable/:id',
    changeColor: true,
    nativeTransitions: {
        "type": "slide",
        "direction": "up"
    },
    views: {
      'menuContent': {
        templateUrl: 'templates/imageviewer.html',
        controller: 'ImageViewerCtrl'
      }
    }
  })
  .state('ContaoApp.galleryviewer', {
    url: '/galleryviewer/:ptable/:pid/:index',
    changeColor: true,
    nativeTransitions: {
        "type": "slide",
        "direction": "up"
    },
    views: {
      'menuContent': {
        templateUrl: 'templates/galleryviewer.html',
        controller: 'GalleryViewerCtrl'
      }
    }
  })
  // fallback
  $urlRouterProvider.otherwise('/app/newslist/1');
})

.filter('parseContaoText', function ($sce, $sanitize, ContaoAppConfig) {
  // Hier wird der Output vom Contao geparsed.. bzw. die URLS um die Domain erweitert und dann der inAppBrowser aufgerufen.
  // Ich weiß noch nicht, wie ich es besser löse. 01.08.2016
  // Eigentlich müsste hier ein richtig dicker Parser her - daher werden auch nur LINKS unterstützt.
    return function (text) {
        var newStringUrlReplace = $sanitize(text).replace('href="','href="'+ContaoAppConfig.URL);
        var regex = /href="([\S]+)"/g;
        var newString = newStringUrlReplace.replace(regex, "class=\"externalURL\" onClick=\"cordova.InAppBrowser.open('$1', '_blank', 'location=yes')\"");
        return $sce.trustAsHtml(newString);
    }
});

