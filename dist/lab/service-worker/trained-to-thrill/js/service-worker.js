// monitor installation
self.addEventListener('install', function(event) {
  console.log('[install] event: ', event);
  event.waitUntil(
    caches.open('trained-to-thrill')
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

// monitor activation
self.addEventListener('activate', function(event) {
  console.log('[activate] event: ', event);
  /*event.waitUntil(self.clients.claim());*/
});

// monitor fetches
self.addEventListener('fetch', function(event) {
  console.log('[fetch] event: ', event);
  event.respondWith(
  /*  caches.match(event.request.url)
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
    })*/
  );
});