/**
 * https://aarontgrogg.com/
 */

// check if Service Worker exists
if ('serviceWorker' in navigator) {
	// make sure one isn't already registered (don't need to dupe it)
	if (!navigator.serviceWorker.controller) {
		// register the Service Worker, must be in the site's root directory to have full scope
		navigator.serviceWorker.register('/serviceworker-min.js')
			.then(function(registration) {
				// registration succeeded :-)
				console.log('ServiceWorker registration succeeded, scope: ', registration.scope);
			}).catch(function(err) {
				// registration failed :-(
				console.log('ServiceWorker registration failed: ', err);
			});
	}
}