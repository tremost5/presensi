const CACHE_NAME = 'dscmkids-unified-v4';
const SCOPE_PATH = new URL(self.registration.scope).pathname.replace(/\/$/, '');
const OFFLINE_URL = `${SCOPE_PATH}/pwa/offline.html`;

const APP_SHELL_ASSETS = [
  OFFLINE_URL,
  `${SCOPE_PATH}/assets/adminlte/css/adminlte.min.css`,
  `${SCOPE_PATH}/assets/adminlte/plugins/fontawesome-free/css/all.min.css`,
  `${SCOPE_PATH}/assets/custom/ui-phase1.css`,
];

function isSameOrigin(url) {
  return url.origin === self.location.origin;
}

function normalizePath(pathname) {
  if (SCOPE_PATH && pathname.startsWith(SCOPE_PATH)) {
    const rel = pathname.slice(SCOPE_PATH.length);
    return rel || '/';
  }
  return pathname;
}

function shouldBypass(path) {
  return (
    path.includes('/ajax') ||
    path.includes('/count') ||
    path.includes('/notif') ||
    path.includes('/simpan') ||
    path.includes('/delete') ||
    path.includes('/logout')
  );
}

function isStaticAsset(path) {
  return (
    path.startsWith('/assets/') ||
    path.startsWith('/uploads/') ||
    path.startsWith('/pwa/icons/') ||
    path.endsWith('.css') ||
    path.endsWith('.js') ||
    path.endsWith('.png') ||
    path.endsWith('.jpg') ||
    path.endsWith('.jpeg') ||
    path.endsWith('.webp') ||
    path.endsWith('.svg') ||
    path.endsWith('.ico')
  );
}

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(APP_SHELL_ASSETS))
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k)))
    )
  );
  self.clients.claim();
});

async function networkFirst(request, fallback) {
  const cache = await caches.open(CACHE_NAME);
  try {
    const response = await fetch(request);
    if (response && response.ok) {
      cache.put(request, response.clone());
    }
    return response;
  } catch (error) {
    const cached = await cache.match(request);
    if (cached) return cached;
    return caches.match(fallback);
  }
}

async function staleWhileRevalidate(request) {
  const cache = await caches.open(CACHE_NAME);
  const cached = await cache.match(request);

  const fetchPromise = fetch(request)
    .then((response) => {
      if (response && response.ok) {
        cache.put(request, response.clone());
      }
      return response;
    })
    .catch(() => cached);

  return cached || fetchPromise;
}

self.addEventListener('fetch', (event) => {
  const request = event.request;
  if (request.method !== 'GET') return;

  const url = new URL(request.url);
  if (!isSameOrigin(url)) return;

  const path = normalizePath(url.pathname);
  if (shouldBypass(path)) return;

  if (request.mode === 'navigate') {
    event.respondWith(networkFirst(request, OFFLINE_URL));
    return;
  }

  if (isStaticAsset(path)) {
    event.respondWith(staleWhileRevalidate(request));
    return;
  }

  event.respondWith(networkFirst(request, OFFLINE_URL));
});
