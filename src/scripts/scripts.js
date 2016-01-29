/**
 * https://aarontgrogg.com/
 */

// check if Service Worker exists
if ('serviceWorker' in navigator) {
	// register the Service Worker, must be in the root directory to have site-wide scope...
	navigator.serviceWorker.register('/serviceworker-min.js')
		.then(function(registration) {
			// registration succeeded :-)
			console.log('ServiceWorker registration succeeded, with this scope: ', registration.scope);
		}).catch(function(err) {
			// registration failed :-(
			console.log('ServiceWorker registration failed: ', err);
		});
}