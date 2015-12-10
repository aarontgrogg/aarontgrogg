// [Working example](/serviceworker-cookbook/offline-status/).

var CACHE_NAME = 'dependencies-cache';

// Files required to make this app work offline
var REQUIRED_FILES = [
  'app.js',
  'index.html',
  'index.js',
  'random-1.png',
  'random-2.png',
  'random-3.png',
  'random-4.png',
  'random-5.png',
  'random-6.png',
  'service-worker.js',
  'style.css'
];

self.addEventListener('install', function(event) {
  // Perform install step:  loading each required file into cache
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(function(cache) {
        // Add all offline dependencies to the cache
        console.log('[install] Caches opened, adding all core components to cache: ', cache);
        return cache.addAll(REQUIRED_FILES);
      })
      .then(function() {
        console.log('[install] All required resources have been cached: ', self);
        return self.skipWaiting();
      })
      .catch(function(error){
        console.log('[install] The service worker has NOT been installed: ', error);
      })
  );
});

self.addEventListener('fetch', function(event) {
  console.log('[fetch] event: ', event);
  event.respondWith(
    /*** NOTE: Dave's tutorial only passes event.request to this Promise, which fails ***/
    caches.match(event.request.url)
      .then(function(response) {
        console.log('[fetch] response: ', response);
        // Cache hit - return the response from the cached version
        if (response) {
          console.log('[fetch] Returning from ServiceWorker cache: ', event.request.url);
          return response;
        }

        // Not in cache - return the result from the live server
        // `fetch` is essentially a "fallback"
        console.log('[fetch] Returning from server: ', event.request.url);
        return fetch(event.request);
      }
    )
    .catch(function(error){
      console.log('[fetch] No cache.match: ', error);
    })
  );
});

self.addEventListener('activate', function(event) {
  console.log('[activate] event: ', event);

  // Calling claim() to force a "controllerchange" event on navigator.serviceWorker
  console.log('[activate] self: ', self);
  event.waitUntil(self.clients.claim());
});
