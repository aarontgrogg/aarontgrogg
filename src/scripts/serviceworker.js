var cachename = 'atg.com-v1';
var cachefiles = [
  '/',
  '/wp-content/themes/atg/styles-min.css',
  '/wp-content/themes/atg/scripts-min.js'
];

// on install...
self.addEventListener('install', function(event) {
  // cache above assets
  event.waitUntil(
    caches.open( cachename )
      .then(function( cache ) {
        console.log( 'Opened cache' );
        return cache.addAll( cachefiles );
      })
  );
});

// on fetch...
self.addEventListener('fetch', function(event) {
  event.respondWith(
    // check if requested asset is in cache
    caches.match(event.request)
      .then(function(response) {
        // if so, respond with it
        if (response) {
          return response;
        }

        // IMPORTANT: Clone the request. A request is a stream and
        // can only be consumed once. Since we are consuming this
        // once by cache and once by the browser for fetch, we need
        // to clone the response
        var fetchRequest = event.request.clone();

        return fetch(fetchRequest).then(
          function(response) {
            // Check if we received a valid response
            if(!response || response.status !== 200 || response.type !== 'basic') {
              return response;
            }

            // IMPORTANT: Clone the response. A response is a stream
            // and because we want the browser to consume the response
            // as well as the cache consuming the response, we need
            // to clone it so we have 2 stream.
            var responseToCache = response.clone();

            caches.open(CACHE_NAME)
              .then(function(cache) {
                cache.put(event.request, responseToCache);
              });

            return response;
          }
        );
      })
    );
});

/*self.addEventListener('activate', function(event) {

  var cacheWhitelist = ['pages-cache-v1', 'blog-posts-cache-v1'];

  event.waitUntil(
    caches.keys().then(function(cacheNames) {
      return Promise.all(
        cacheNames.map(function(cacheName) {
          if (cacheWhitelist.indexOf(cacheName) === -1) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});*/