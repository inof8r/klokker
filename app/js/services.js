'use strict';

/* Services */

var timeclockServices = angular.module('timeclockServices', ['ngResource']);

;

timeclockServices.factory('Tag', ['$resource',
  function($resource){
    return $resource('data/gateway.php?mode=gettag&tagId=:tagId', {}, {
      query: {method:'GET', params:{tagId:'tags'}, isArray:true}
    });
  }]);

timeclockServices.factory('userService', function($rootScope, $http) {
    var userService = {};
    userService.data = {};
    userService.getUsers = function() {
        $http.get('data/gateway.php?mode=getusers')
            .success(function(data) {
                userService.data.users = data;
            });
        return userService.data;
    };
    return userService;
});

timeclockServices.factory('tagTypeService', function($rootScope, $http) {
    var tagTypeService = {};
    tagTypeService.data = {};
    tagTypeService.getTagTypes = function() {
        $http.get('data/gateway.php?mode=gettagtypes')
            .success(function(data) {
                tagTypeService.data.tagtypes = data;
            });
        return tagTypeService.data;
    };
    return tagTypeService;
});