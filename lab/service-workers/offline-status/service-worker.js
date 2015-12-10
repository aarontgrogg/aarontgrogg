// [Working example](/serviceworker-cookbook/offline-status/).

var CACHE_NAME = 'dependencies-cache';

// Files required to make this app work offline
var REQUIRED_FILES = [
  /***
    NOTE: 
      You can just use 'app.js' for example, and it will cache those relative URLs,
      but if you just use '/', it will cache the *absolute* root document, not the *relative root*,
      which is why I'm using the full path here.
  ***/
  '/lab/style.css',
  '/lab/service-workers/offline-status/',
  '/lab/service-workers/offline-status/app.js',
  '/lab/service-workers/offline-status/index.html',
  '/lab/service-workers/offline-status/index.js',
  '/lab/service-workers/offline-status/random-1.png',
  '/lab/service-workers/offline-status/random-2.png',
  '/lab/service-workers/offline-status/random-3.png',
  '/lab/service-workers/offline-status/random-4.png',
  '/lab/service-workers/offline-status/random-5.png',
  '/lab/service-workers/offline-status/random-6.png',
  '/lab/service-workers/offline-status/service-worker.js',
  '/lab/service-workers/offline-status/style.css'
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
