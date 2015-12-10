// see if Service Worker already exists
if (navigator.serviceWorker.controller) {
  console.log('[controller] exists');
} else {
  // Register the ServiceWorker
  navigator.serviceWorker.register('service-worker.js', {
    scope: '.'
  }).then(function(registration) {
    console.log('[register] The service worker has been registered: ', registration);
  }).catch(function(error){
    console.log('[register] The service worker has NOT been registered: ', error);
  });
}

// The refresh link needs a cache-busting URL parameter
document.querySelector('#refresh').search = Date.now();

// Allow for "replaying" this example
document.getElementById('clearAndReRegister').addEventListener('click',
  function() {
    navigator.serviceWorker.getRegistration().then(function(registration) {
      registration.unregister();
      window.location.reload();
    });
  }
);