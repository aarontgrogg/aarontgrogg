(function() {

	'use strict';

	/* App Module */
	var app = angular.module( 'store', [ 'store-products' ] );

	var gems = [
		{
			name: 'Dodecahedron',
			price: 2.00,
			description: '...',
			specification: '...',
			canPurchase: true,
			soldOut: false,
			images: [
				{
					full: '01.jpg',
					thumb: '01-thumb.jpg'
				},
				{
					full: '02.jpg',
					thumb: '02-thumb.jpg'
				}
			],
			reviews: [
				{
					stars: 5,
					body: 'I love this gem!',
					author: 'joe@thomas.com'
				},
				{
					stars: 2,
					body: 'This gem sucks!',
					author: 'tim@hater.com'
				}
			]
		},
		{
			name: 'Dodecahedron',
			price: 2.95,
			description: '...',
			specification: '...',
			canPurchase: true,
			soldOut: false,
			images: [
				{
					full: '01.jpg',
					thumb: '01-thumb.jpg'
				},
				{
					full: '02.jpg',
					thumb: '02-thumb.jpg'
				}
			],
			reviews: [
				{
					stars: 5,
					body: 'I love this gem!',
					author: 'joe@thomas.com'
				},
				{
					stars: 2,
					body: 'This gem sucks!',
					author: 'tim@hater.com'
				}
			]
		}
	];


	app.controller( 'StoreController', function() {
		this.products = gems;
	});

	app.controller( 'PanelController', function() {
		this.activeTab = 1;
		this.selectTab = function( setTab ) {
			this.activeTab = setTab;
		};
		this.isSelected = function( checkTab ) {
			return this.activeTab === checkTab;
		};
	});

	app.controller( 'ReviewController', function() {
		this.review = {};
		this.addReview = function(product) {
			product.reviews.push(this.review);
			this.review = {};
		};
	});

	/*	$http service function w/ options object:
		$http({
			method: 'GET',
			url: 'some/url.html'
		});
		or using a $http service function shortcut:
		$http.get('some/url.html', {
			apiKey: 'someAPIkey'
		});
		both return a promise with a .success() and .error()
	*/	

	/*	services dependency injection:
		in order to use a service, have to create a controller, like:
		app.controller('controllerName', ['$http', '$log', function($http, $log){
			// some stuff here...?
		} ]);
		Angular injects these services (that our app depends on), to our app...
	*/

})();