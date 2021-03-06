'use strict';

/* Controllers */



var timeclockControllers = angular.module('timeclockControllers', []);

timeclockControllers.controller('HomeCtrl2', ['$scope', 'Home',
  function($scope, Home) {

  }]);

timeclockControllers.controller('AboutCtrl2', ['$scope', 'Home',
  function($scope, Home) {
		 $scope.date = new Date();
  }]);

timeclockControllers.controller("AboutCtrl", function($scope, $http) {
  $http.get('data/gateway.php').
    success(function(data, status, headers, config) {
      $scope.tags = data;
 $scope.date = new Date();      
    }).
    error(function(data, status, headers, config) {
      // log error
    });
});


timeclockControllers.controller("HomeCtrl", function($scope, $http) {
  $http.get('data/gateway.php?mode=home').
    success(function(data, status, headers, config) {
      $scope.tags = data;
              $scope.displayContent = true;
        $scope.toggleContent = function(showContent) {
            $scope.displayContent = showContent;
        };

    }).
    error(function(data, status, headers, config) {
      // log error
    });
});


timeclockControllers.controller("TagsListCtrl", function($scope, $http) {
  $http.get('data/gateway.php?mode=gettag').
    success(function(data, status, headers, config) {
      $scope.tags = data.data;
		$scope.authorized = data.authorized;
    }).
    error(function(data, status, headers, config) {
      // log error
    });
});

timeclockControllers.controller('TagDetailCtrl', ['$scope', '$routeParams', 'Tag',
  function($scope, $routeParams, Tag) {
    $scope.tag = Tag.get({tagId: $routeParams.tagId}, function(tag) {
    });
    
}]);

timeclockControllers.controller('TagRegisterCtrl', ['$scope', '$routeParams', 'Tag',
  function($scope, $routeParams, Tag) {
    $scope.tag = Tag.get({tagId: $routeParams.tagId,mode: "register"}, function(tag) {
    });
    $scope.$on('$viewContentLoaded', function() {
    //call it here
//    alert("viewContentLoaded" + $scope.tags);
	Android.receiveMessage("TagRegisterCtrl:" + $scope.tag	);
	});
    
}]);



timeclockControllers.controller('ProjectsListCtrl', ['$scope', 'Project',
  function($scope, Project) {
    $scope.projects = Project.query();
    $scope.orderProp = 'id';
  }]);


timeclockControllers.controller('ProjectDetailCtrl', ['$scope', '$routeParams', 'Project',
  function($scope, $routeParams, Project) {
    $scope.project = Project.get({projectId: $routeParams.projectId}, function(project) {
      $scope.mainImageUrl = project.images[0];
    });

    $scope.setImage = function(imageUrl) {
      $scope.mainImageUrl = imageUrl;
    };
  }]);


timeclockControllers.controller('PostController', ['$scope', '$http', function($scope, $http) {
		this.postForm = function() {
		
			var encodedString = 'mode=login&username=' +
				encodeURIComponent(this.inputData.username) +
				'&password=' +
				encodeURIComponent(this.inputData.password);
				
			$http({
				method: 'POST',
				url: 'data/gateway.php',
				data: encodedString,
				headers: {'Content-Type': 'application/x-www-form-urlencoded'}
			})
			.success(function(data, status, headers, config) {
				console.log(data);
//				if ( data.trim() === 'correct') {
				//alert(data);
				if ( data.result == 'success') {				
					window.location.href = '#/home';
				} else {
					$scope.errorMsg = "Login not correct";
				}
			})
			.error(function(data, status, headers, config) {
				$scope.errorMsg = 'Unable to submit form';
			})
		}
		
	}]);
	
timeclockControllers.controller('LogoutController', ['$scope', '$http', function($scope, $http) {
		this.postForm = function() {
		
			var encodedString = 'mode=logout';
				
			$http({
				method: 'POST',
				url: 'data/gateway.php',
				data: encodedString,
				headers: {'Content-Type': 'application/x-www-form-urlencoded'}
			})
			.success(function(data, status, headers, config) {
				console.log(data);
//				if ( data.trim() === 'correct') {
				//alert(data.result);
				if ( data.result == 'success') {				
					window.location.href = '#/login';
				} else {
					$scope.errorMsg = "Login not correct";
				}
			})
			.error(function(data, status, headers, config) {
				$scope.errorMsg = 'Unable to submit form';
			})
		}
		
	}]);	
	

timeclockControllers.controller('SaveTagController', ['$scope', '$http', function($scope, $http) {



		this.postForm = function() {
			var obid = $("#obid").val();
			var tagid = $("#tagid").val();
			var owner = $("#owner").val().split(":")[1];
			var obtype = $("#obtype").val().split(":")[1];
			var note = $("#note").val();
			var encodedString = 'mode=savetag';
			encodedString += '&obid=' + encodeURIComponent(obid);
			encodedString += '&tagid=' +encodeURIComponent(tagid);
			encodedString += '&obtype=' +encodeURIComponent(obtype);			
			encodedString += '&owner=' +encodeURIComponent(owner);				
			encodedString += '&note=' +encodeURIComponent(note);				
			$http({
				method: 'POST',
				url: 'data/gateway.php',
				data: encodedString,
				headers: {'Content-Type': 'application/x-www-form-urlencoded'}
			})
			.success(function(data, status, headers, config) {
				console.log(data);
//				if ( data.trim() === 'correct') {
	

				if ( data[0].result == 'success') {	
					$("#logContainer").html(data[0].log);			
					//window.location.href = '#/tags';
				} else {
					$scope.errorMsg = "Tag not saved";
				}
			})
			.error(function(data, status, headers, config) {
				$scope.errorMsg = 'Unable to submit form';
			})
		}
		
	}]);	


timeclockControllers.controller('UserListController', function($scope, $http, userService) {
	$scope.selectedUserId = null;
    $scope.data = userService.getUsers();
});


timeclockControllers.controller('TagTypeListController', function($scope, $http, tagTypeService) {
	$scope.selectedTagTypeId = null;
    $scope.data = tagTypeService.getTagTypes();
});


timeclockControllers.controller("RecordsListCtrl", function($scope, $http) {
  $http.get('data/gateway.php?mode=records').
    success(function(data, status, headers, config) {
      $scope.tags = data.data;
		$scope.authorized = data.authorized;
    }).
    error(function(data, status, headers, config) {
      // log error
    });
});



