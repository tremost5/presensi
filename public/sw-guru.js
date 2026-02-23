const CACHE_NAME = 'presensi-guru-v2';
const OFFLINE_URL = '/pwa/offline.html';

self.addEventListener('install', event => {
  event.waitUntil((async () => {
    const cache = await caches.open(CACHE_NAME);
    try {
      const response = await fetch(OFFLINE_URL, { cache: 'no-cache' });
      if (response && response.ok) {
        await cache.put(OFFLINE_URL, response.clone());
      }
    } catch (e) {
      // Don't fail installation when offline page is unavailable.
    }
  })());
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;

  event.respondWith(
    fetch(event.request)
      .then(response => {
        const copy = response.clone();
        caches.open(CACHE_NAME).then(cache => cache.put(event.request, copy));
        return response;
      })
      .catch(() => caches.match(event.request).then(cached => cached || caches.match(OFFLINE_URL)))
  );
});
