var CACHE_NAME = 'atg.com-v1';
var urlsToCache = [
  '/',
  '/wp-content/themes/atg/style-min.css',
  '/wp-content/themes/atg/script-min.js'
];

self.addEventListener('install', function(event) {
  // Perform install steps
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(function(cache) {
        console.log('Opened cache');
        return cache.addAll(urlsToCache);
      })
  );
});