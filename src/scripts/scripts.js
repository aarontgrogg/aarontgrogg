/**
 * https://aarontgrogg.com/
 */

// check if Service Worker exists
if ('serviceWorker' in navigator) {
	// make sure one isn't already registered (don't need to dupe it)
	if (!navigator.serviceWorker.controller) {
		// register the Service Worker, which lives in my theme directory, but scope it to the site's root directory
		navigator.serviceWorker.register('/wp-content/themes/atg/serviceworker-min.js', { scope: './' })
			.then(function(registration) {
				// registration succeeded :-)
				console.log('ServiceWorker registration succeeded, with this scope: ', registration.scope);
			}).catch(function(err) {
				// registration failed :-(
				console.log('ServiceWorker registration failed: ', err);
			});
	}
}