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
//    '/',
    'index.js',
    'app.js'
];

// install listener
self.addEventListener('install', function(event) {
    console.log('[install] Service Worker install event: ', event);
    // Perform install step:  loading each required file into cache
    event.waitUntil(
        console.log('[install] Service Worker install event.waitUntil.');
        caches.open(CACHE_NAME)
            .then(function(cache) {
                // Add all offline dependencies to the cache
                console.log('[install] Caches opened, adding all core components to cache');
                return cache.addAll(REQUIRED_FILES);
            })
            .then(function() {
                console.log('[install] All required resources have been cached, we\'re good!');
                return self.skipWaiting();
            })
    );
});

// listen to each fetch request the browser makes
self.addEventListener('fetch', function(event){
    console.log('[fetch] Service Worker fetch event: ', event);
    // for each event...
    event.respondWith(
        console.log('[fetch] Service Worker fetch event.respondWith.');
        // check if the item is in the cache
        caches.match(event.request)
            .then(function(response) {
                console.log('[fetch] Service Worker caches.match: ', response);
                // if so, return the cached asset
                if (response) {
                    return response;
                }
                // else process the fetch request
                return fetch(event.request);
            })
    );
});

// activate the service worker right away to bypass a page refresh to start it
self.addEventListener('activate', function(event){
    console.log('[activate] Service Worker activated.');
    event.waitUntil(self.clients.claim());
});