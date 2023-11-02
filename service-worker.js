console.log('Started', self);
self.addEventListener('install', function(event) {
  self.skipWaiting();
  console.log('Installed', event);
});
self.addEventListener('activate', function(event) {
  console.log('Activated', event);
});
self.addEventListener('push', function(event) {
    console.log("hhdd");
  console.log('[Service Worker] Push Receivedddddddd.');
  console.log(`[Service Worker] Push had this dataaaaaaaa: "${event.data.text()}"`);

  const title = 'Push Codelab';
  const options = {
    body: event.data.msg,
    icon: 'images/icon.png',
    badge: 'images/badge.png',
    silent : 'false',
     sound : 'default'
  };

  event.waitUntil(self.registration.showNotification(title, options));

});

