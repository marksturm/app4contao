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
        //alert("notification");
      };

      window.plugins.OneSignal.init(ContaoAppOneSignal.OneSignalKey,
                                     {googleProjectNumber: ContaoAppOneSignal.googleProjectNumber},
                                     notificationOpenedCallback);
      
      window.plugins.OneSignal.getIds(function(ids) {
        console.log("UserID: " + ids.userId);
        console.log("PushToken: " + ids.pushToken);
        console.log('getIds: ' + JSON.stringify(ids));
      });

      // alert box wenn eine notification ankommt und ein user in der app ist.
      window.plugins.OneSignal.setSubscription(true);
      window.plugins.OneSignal.enableNotificationsWhenActive(true);
      window.plugins.OneSignal.enableInAppAlertNotification(false);
      }
  });
})

.config(function($ionicConfigProvider, $stateProvider, $urlRouterProvider, $ionicNativeTransitionsProvider, ContaoAppConfig) {

  $ionicConfigProvider.views.transition('none');

  $ionicConfigProvider.backButton.previousTitleText(false).text('');

  $ionicConfigProvider.scrolling.jsScrolling(false);
  
    if (ionic.Platform.isAndroid()) {
      var menu_tpl = 'templates/menu.html';
      var pfixedPixelsTop = 44;
    } else {
      var menu_tpl = 'templates/menu_ios.html';
      var pfixedPixelsTop = 64;
    }

 $ionicNativeTransitionsProvider.setDefaultOptions({
      duration: 300,
      slowdownfactor: 4,
      fixedPixelsTop: pfixedPixelsTop,
      backInOppositeDirection: true,
  })

  $stateProvider
    .state('ContaoApp', {
    url: '/app',
    abstract: true,
    templateUrl: menu_tpl,
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
  $urlRouterProvider.otherwise(ContaoAppConfig.startPage);
})

.filter('parseContaoText', function ($sce, $sanitize, ContaoAppConfig) {
  // Hier wird der Output vom Contao geparsed.. bzw. die URLS um die Domain erweitert und dann der inAppBrowser aufgerufen.
  // Sollte das irgendwo ein Bug geben, wird die API erweitert und der Dreck wird mit PHP gelöst. APP -> DOMAIN -> API -> DOMAIN -> APP. 

  return function ( text ) {
  text = $sanitize(text);
  var source,sear,repl;
  var newString;
  var regex = /href="([\S]+)"/g;
  var extMatch = /http|https|\/\//g;
  var mtMatch = /mailto:/g;

  var div = document.createElement("div");
  div.innerHTML = text;
  var aList = div.getElementsByTagName("a");
  var aListNew = new Array();
  var href,t;
  
  if(aList.length>0){
  
    for(var i = 0; i<aList.length; i++ ){
    	href = aList[i].getAttribute("href");
    	t = aList[i].outerHTML;
    	t = $sanitize(t);
      
    	if(href.match(extMatch)!=null){
        	newString = t.replace(regex, "class=\"externalURL\" onClick=\"cordova.InAppBrowser.open('$1', '_blank', 'location=yes')\"");

      	} else if(href.match(mtMatch)!=null){
        	newString = t;
      	} else{
        	newString = t.replace(regex, "class=\"externalURL\" onClick=\"cordova.InAppBrowser.open('"+ContaoAppConfig.URL+"$1', '_blank', 'location=yes')\"");
      	}
	
		source = text;
		sear = t;
		repl = newString;
		text = text.replace(t,newString);
    	}
  	}
    return $sce.trustAsHtml(text);
  };
});
