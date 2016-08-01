/**
 * @author Mark Sturm - privat@mark-sturm.de
 * @license CC BY-NC-SA 4.0
*/

angular.module('ContaoApp.services', [])

.constant('ContaoAppConfig', {
    appVersion: 1.0,
    URL: 'http://www.marq.one/',
    apiURL: 'http://api.marq.one/',
    fileURL: 'http://marq.one/',
    ShareTitle: 'Geteilt mit App4Contao'
})

.constant('ContaoAppMenu',
  [
    { label: 'Home', url:'#/app/newslist/1',icon:'ion-home' },
    { label: 'Demoseite', url:'#/app/content/tl_article/5',icon:'ion-information-circled' },
    { label: 'Merkliste', url:'#/app/bookmarks',icon:'ion-heart' }
  ]
)

.constant('ContaoAppSocialMenu',
  [
    { label: 'Facebook', url:'https://www.facebook.com/marqone-1328757690487671/', icon:'ion-social-facebook' },
    { label: 'Twitter', url:'#',icon:'ion-social-twitter' },
    { label: 'GooglePlus', url:'https://aboutme.google.com/u/0/b/111282895061009637135/',icon:'ion-social-google' },
    { label: 'Youtube', url:'https://www.youtube.com/channel/UC3ULhnfgdW4HsVVf4IQSvFQ',icon:'ion-social-youtube' },  
  ]
)

.constant('ContaoAppLabel', {
    addedBookmark: 'Der Beitrag wurde zu Ihrer Merkliste hinzugefügt.',
    deletedBookmark: 'Der Beitrag wurde aus Ihrer Merkliste entfernt.',
    offlineTitle: 'Keine Internetverbindung',
    offlineMsg: 'Sie benötigen eine aktive Internetverbidnung.',
})

.constant('ContaoAppOneSignal', {
    OneSignalKey: 'DEINOneSignalKey',
    googleProjectNumber: 'DEINEgoogleProjectNumber'
})

.factory('DataService', function($http,ContaoAppConfig) {

    var _GetNewsList = function(page,pid) {
        return $http.get(ContaoAppConfig.apiURL+'?modul=NewsList&limit=5&pid='+pid+'&page=' + page);
        }
    var _GetNewsEntry = function(id) {
        return $http.get(ContaoAppConfig.apiURL+'?modul=NewsList&id='+id);
        }
    var _GetContentList = function(ptable,page,pid) {
        return $http.get(ContaoAppConfig.apiURL+'?modul=Element&ptable='+ptable+'&limit=2&page='+page+'&pid='+pid);
        }
    var _GetContentElement =function(ptable,id) {
        return $http.get(ContaoAppConfig.apiURL+'?modul=Element&ptable='+ptable+'&id='+id);
      }

    return {
      GetNewsList:  _GetNewsList,
      GetNewsEntry: _GetNewsEntry,
      GetContentList: _GetContentList,
      GetContentElement: _GetContentElement
    }
    
})

.factory ('StorageService', function($localStorage) {

  $localStorage = $localStorage.$default({
    content: []
  });

  var _getAllContent = function () {
    return $localStorage.content;
  }
  var _addContent = function (item) {
    $localStorage.content.push(item);
  }
  var _removeContent = function (item) {
    $localStorage.content.splice($localStorage.content.indexOf(item), 1);
  }
  var _reset = function () {
     $localStorage.content.$reset();
  }

  return {
    getContent: _getAllContent,
    addContent: _addContent,
    removeContent: _removeContent,
    reset: _reset
  };
})


.factory('FindIndexByKeyValue', function() {

  var factory = {};
  
  factory.functionIndexByKeyValue = function(arraytosearch, key, valuetosearch) {
       
    for (var i = 0; i < arraytosearch.length; i++) {
      
      if (arraytosearch[i][key] == valuetosearch) {
          return i;
        }
      }
    return null;
    }

  return factory;

})
