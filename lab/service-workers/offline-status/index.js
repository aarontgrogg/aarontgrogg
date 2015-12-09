// register the service worker
navigator.serviceWorker.register('service-worker.js', {
    // not sure what this means yet...
    scope: '.'
}).then(function(registration){
    // notify the console, or not...
    console.log('Service Worker registered.');
});

// listen for changes to the service worker
navigator.serviceWorker.addEventListener('controllerchange', function(event){
    // the state of the service worker will change when the serice worker becomes activated, 
    // meaning all files have been cached
    navigator.serviceWorker.controller.addEventListener('statechange', function(){
        // if so...
        if (this.state === 'activated') {
            // show a message that it is okay to go offline
            document.getElementById('offlineNotification').classList.remove('hidden');
        }
    });
});