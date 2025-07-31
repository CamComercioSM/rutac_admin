const CACHE_NAME = 'visitur-dynamic-cache-v1';

self.addEventListener('install', event => {
  self.skipWaiting(); // Para activar inmediatamente
});

self.addEventListener('activate', event => {
  clients.claim(); // Para tomar control sin recargar
  event.waitUntil(
    caches.keys().then(keys => 
      Promise.all(keys.map(key => {
        if (key !== CACHE_NAME) return caches.delete(key);
      }))
    )
  );
});

self.addEventListener('fetch', event => {
  const request = event.request;

  if (!request.url.startsWith('http')) {
    return; // Ignora otras solicitudes
  }


  // Solo manejar GET y archivos .js o .css
  if (
    request.method === 'GET' &&
    (request.url.endsWith('.js') || request.url.endsWith('.css') || request.url.endsWith('.svg'))
  ) {
    event.respondWith(
      caches.match(request).then(cachedResponse => {
        if (cachedResponse) return cachedResponse;

        return fetch(request).then(networkResponse => {
          return caches.open(CACHE_NAME).then(cache => {
            cache.put(request, networkResponse.clone());
            return networkResponse;
          });
        }).catch(() => {
          // Opcional: devolver archivo fallback si la red falla
        });
      })
    );
  }
});
