// global for offline html name
var offlineHTML = 'offline.html';

self.addEventListener('install', function(event) {
  // new request for offline html
  var offlineRequest = new Request(offlineHTML);

  // Perform install step:  loading each required file into cache
  event.waitUntil(
    fetch(offlineRequest).then(function(response){
      return caches.open('offline')
                .then(function(cache) {
                  console.log('[install] Cache opened, adding offline html to cache: ', cache);
                  return cache.put(offlineRequest, response);
                });
    })
  );
});
    

self.addEventListener('fetch', function(event) {
  // grab the request
  var request = event.request;
  console.log('[fetch] request: ', request);
  // ensure it is a GET requyest
  if (request.method === 'GET' && request.headers.get('accept').includes('text/html')) {
    event.respondWith(
      fetch(request).catch(function(error){
        // using the catch to trigger the replacement
        return caches.open('offline').then(function(cache){
          return cache.match(offlineHTML);
        });
      })
    );
  }
});