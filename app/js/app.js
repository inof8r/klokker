	'use strict';

/* App Module */

var timeclockApp = angular.module('timeclockApp', [
  'ngRoute',
  'timeclockAnimations',

  'timeclockControllers',
  'timeclockFilters',
  'timeclockServices'
]);



timeclockApp.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
      when('/home', {
        templateUrl: 'partials/home.html',
        controller: 'HomeCtrl'
      }).
      when('/about', {
        templateUrl: 'partials/about.html',
        controller: 'AboutCtrl'
      }).

      when('/login', {
        templateUrl: 'partials/login.html',
        controller: 'PostController',
        controllerAs: 'vm'
      }).
      when('/logout', {
        templateUrl: 'partials/logout.html',
        controller: 'LogoutController',
        controllerAs: 'vm'
      }).
      when('/timeclock', {
        templateUrl: 'partials/timeclock.html',
        controller: 'TimeclockCtrl'
      }).
      when('/tags', {
        templateUrl: 'partials/tag-list.html',
        controller: 'TagsListCtrl'
      }).
      when('/tags/:tagId', {
        templateUrl: 'partials/tag-detail.html',
        controller: 'TagDetailCtrl'
      }).
      when('/tags/:tagId/register', {
        templateUrl: 'partials/tag-register.html',
        controller: 'TagRegisterCtrl'
      }).
      when('/projects', {
        templateUrl: 'partials/project-list.html',
        controller: 'ProjectsListCtrl'
      }).
      when('/projects/:projectId', {
        templateUrl: 'partials/project-detail.html',
        controller: 'ProjectDetailCtrl'
      }).
      otherwise({
        redirectTo: '/login'
      });
  }]);

if (!Android) {
	var Android = new Object();
	Android.receiveMessage = function () {};
	Android.playSound = function () {};
}

function getNFC(str) {
	sendToast("getNFC" + str);
}

function playSound(str) {
	sendToast("playSound: " + str);
	Android.playSound(str);
}

function sendToast(msg) {
    Android.receiveMessage("sendToast:" + msg);
 }  