/**
 * https://aarontgrogg.com/
 */

// check if Service Worker is supported
if ('serviceWorker' in navigator) {
	// register the Service Worker, must be in the root directory to have site-wide scope...
	navigator.serviceWorker.register('/serviceworker-min.js')
		.then(function(registration) {
			// registration succeeded :-)
			console.log('ServiceWorker registration succeeded, with this scope: ', registration.scope);
			// you may occasionally need to clear a service worker; this is the only way i've found to do that...
			// comment this out while not using it
			/*registration.unregister().then(function(boolean) {
				// if boolean = true, unregister is successful
				console.log('ServiceWorker unregistered');
			});*/
		}).catch(function(err) {
			// registration failed :-(
			console.log('ServiceWorker registration failed: ', err);
		});
}