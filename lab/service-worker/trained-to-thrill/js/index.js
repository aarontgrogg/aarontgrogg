// register the service worker
navigator.serviceWorker.register('service-worker.js').then(function(registration) {
  console.log('[register] registered: ', registration);
}).catch(function(error){
  console.log('[register] NOT registered: ', error);
});