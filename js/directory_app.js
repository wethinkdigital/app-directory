'use strict';

(function(){

	var dirapp = angular.module('dirApp',[]);

	// dirapp.config(['$locationProvider',function($locationProvider) {
 //      $locationProvider.html5Mode(true);
 //  	}]);

	// dirapp.config(function ($httpProvider) {
 //    // send all requests payload as query string
	//     $httpProvider.defaults.transformRequest = function(data){
	//         if (data === undefined) {
	//             return data;
	//         }
	//         return jQuery.param(data);
	//     };

	//     // set all post requests content type
	//     $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
	// });

	dirapp.controller('dirJobs',['$http','$scope','$location',function($http,$scope,$location){


		//$locationProvider.html5Mode(true);
		$scope.searchresults = {};
		$scope.inputs = $location.search();
		$scope.inputs.angular = true;


		$scope.search = function(){


			$http.post('/wp-admin/admin-ajax.php?action=directory_search',$scope.inputs)
			.success(function(results){
				$scope.searchresults = results.posts;
				console.log($scope.searchresults);
			})
			.error(function(error){
				console.log(error);
			});
			
		}

		$scope.isPromoted = function(item){
			console.log('isPromoted function called');
			console.log(item);
			if($scope.inputs.industry == item.meta.promote[0]) console.log('is promoted for this category');
			if(item.meta.promote_enabled == 'enabled') console.log('promotion is enabled');
			if(item.meta.ad_type[0] == 'sponsored') console.log('is a sponsored ad');
			return ($scope.inputs.industry == item.meta.promote[0] && item.meta.promote_enabled == 'enabled' && item.meta.ad_type[0] == 'sponsored');
		}

		$scope.search();



	}]);

})();