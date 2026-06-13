// Service worker disabled. The previous implementation cached every page
// response with stale-while-revalidate, causing teachers to see old marks
// long after admin approval. Any browser that still has this SW registered
// will trigger the activate listener below, which clears all caches and
// unregisters the SW so the next page load goes directly to the network.

self.addEventListener('install', function () {
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(
        Promise.all([
            self.caches.keys().then(function (keys) {
                return Promise.all(keys.map(function (k) { return self.caches.delete(k); }));
            }),
            self.registration.unregister(),
        ]).then(function () {
            return self.clients.matchAll();
        }).then(function (clients) {
            clients.forEach(function (c) { c.navigate(c.url); });
        })
    );
});

// Pass every fetch straight through to the network — no caching layer.
self.addEventListener('fetch', function () { return; });
