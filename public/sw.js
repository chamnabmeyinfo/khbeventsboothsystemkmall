/* Service worker for Web Push notifications */
self.addEventListener('push', function (event) {
    var data = { title: 'Notification', body: '', url: null, icon: '/favicon.ico' };
    if (event.data) {
        try {
            data = event.data.json();
        } catch (e) {
            data.body = event.data.text();
        }
    }
    var options = {
        body: data.body || '',
        icon: data.icon || '/favicon.ico',
        badge: data.icon || '/favicon.ico',
        data: { url: data.url || null },
        tag: 'khb-notification',
        renotify: true
    };
    event.waitUntil(
        self.registration.showNotification(data.title || 'Notification', options)
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    var url = event.notification.data && event.notification.data.url;
    if (url) {
        event.waitUntil(
            clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (windowClients) {
                for (var i = 0; i < windowClients.length; i++) {
                    if (windowClients[i].url.indexOf(self.location.origin) === 0 && 'focus' in windowClients[i]) {
                        windowClients[i].navigate(url);
                        return windowClients[i].focus();
                    }
                }
                if (clients.openWindow) {
                    return clients.openWindow(url);
                }
            })
        );
    }
});
