// name cache
var CACHE_NAME = 'dependencies-cache';

// manifest of files to cache
var REQUIRED_FILES = [
    'random-1.png',
    'random-2.png',
    'random-3.png',
    'random-4.png',
    'random-5.png',
    'random-6.png',
    'style.css',
    'index.html',
    '/',
    'index.js',
    'app.js'
];

// install listener
self.addEventListener('install', function(event){
    // wait until the service worker is installed...
    event.waitUntil(
        // then open the cache...
        caches.open(CACHE_NAME)
            // then cache all the required files
            .then(function(cache){
                return cache.addAll(REQUIRED_FILES);
            })
            // then... not sure what this does yet
            .then(function(){
                return self.skipWaiting();
            })
    );
});

// listen to each fetch request the browser makes
self.addEventListener('fetch', function(event){
    // for each event...
    event.respondWith(
        // check if the item is in the cache
        caches.match(event.request)
            .then(function(response) {
                // if so, return the cached asset
                if (response) {
                    return response;
                }
                // else process the fetch request
                return fetch(event.request);
            });
    );
});

// activate the service worker right away to bypass a page refresh to start it
self.addEventListener('activate', function(event){
    event.waitUntil(self.clients.claim());
});