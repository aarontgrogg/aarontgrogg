(function() {

	'use strict';

	/* App Module */
	var app = angular.module( 'store-products', [ ] );

	app.directive('productTitle', function() {
		return {
			restrict: 'E', // E = element
			templateUrl: 'product-title.html'
		}
	});

})();