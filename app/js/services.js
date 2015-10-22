'use strict';

/* Services */

var timeclockServices = angular.module('timeclockServices', ['ngResource']);

;

timeclockServices.factory('Tag', ['$resource',
  function($resource){
    return $resource('data/gateway.php?endpoint=tags&tagId=:tagId', {}, {
      query: {method:'GET', params:{tagId:'tags'}, isArray:true}
    });
  }]);


