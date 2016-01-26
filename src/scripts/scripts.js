/**
 * https://aarontgrogg.com/
 */

/* Need Service Worker polyfill?
 * https://raw.githubusercontent.com/coonsta/cache-polyfill/master/index.js
 */
// https://github.com/coonsta/cache-polyfill
// include /wp-content/themes/atg/serviceworker-cache-polyfill.js
/*
self.addEventListener('install', function(event) {
	event.waitUntil(
		caches.open('demo-cache').then(function(cache) {
			return cache.put('/', new Response("From the cache!"));
		})
	);
});

self.addEventListener('fetch', function(event) {
	event.respondWith(
		caches.match(event.request).then(function(response) {
			return response || new Response("Nothing in the cache for this request");
		})
	);
});
*/
/* Check if Service Worker works */
if ('serviceWorker' in navigator) {
	navigator.serviceWorker.register('/serviceworker-min.js')
		.then(function(registration) {
			// Registration was successful
			console.log('ServiceWorker registration successful with scope: ', registration.scope);
		}).catch(function(err) {
			// registration failed :(
			console.log('ServiceWorker registration failed: ', err);
		});
}