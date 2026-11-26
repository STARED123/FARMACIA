const CACHE_NAME = 'farmacia-sjb-shell-v1';
const APP_SCOPE = self.registration.scope;
const STATIC_ASSETS = [
    'manifest.json',
    'offline.html',
    'css/bootstrap.css',
    'css/custom.css',
    'js/bootstrap.bundle.min.js',
    'js/app.js',
    'icons/icon-192.png',
    'icons/icon-512.png',
].map((assetPath) => new URL(assetPath, APP_SCOPE).href);
const OFFLINE_PAGE = new URL('offline.html', APP_SCOPE).href;

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(STATIC_ASSETS))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => Promise.all(
                cacheNames
                    .filter((cacheName) => cacheName.startsWith('farmacia-sjb-shell-') && cacheName !== CACHE_NAME)
                    .map((cacheName) => caches.delete(cacheName))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const request = event.request;
    const requestUrl = new URL(request.url);

    if (request.method !== 'GET' || requestUrl.origin !== self.location.origin) {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => caches.match(OFFLINE_PAGE))
        );

        return;
    }

    if (STATIC_ASSETS.includes(requestUrl.href)) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => cachedResponse || fetch(request))
        );
    }
});
