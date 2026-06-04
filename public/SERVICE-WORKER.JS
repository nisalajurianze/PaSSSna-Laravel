// Service Worker for Offline Capability

const CACHE_NAME = 'passsna-v1';
const urlsToCache = [
    '/',
    '/css/style.css',
    '/js/app.js',
    '/js/chart.js',
    '/js/sweetalert-config.js',
    '/images/logo.png',
    '/manifest.json'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(urlsToCache))
    );
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request)
            .then((response) => {
                if (response) {
                    return response;
                }
                return fetch(event.request);
            })
    );
});

self.addEventListener('activate', (event) => {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// Handle push notifications
self.addEventListener('push', (event) => {
    const data = event.data.json();
    const options = {
        body: data.body,
        icon: '/images/logo.png',
        badge: '/images/logo.png',
        vibrate: [100, 50, 100],
        data: {
            url: data.url
        }
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data.url)
    );
});
