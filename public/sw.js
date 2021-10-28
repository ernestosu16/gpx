const CACHE_NAME = 'v1_cache_gpx';
const urlToCache = [
    './',
    './homer-theme/vendor/jquery/dist/jquery.min.js',
    './homer-theme/vendor/jquery-validation/jquery.validate.min.js',
    './homer-theme/vendor/jquery-validation/messages_es.min.js',
    './homer-theme/vendor/jquery-ui/jquery-ui.min.js',
    './homer-theme/vendor/slimScroll/jquery.slimscroll.min.js',
    './homer-theme/vendor/bootstrap/dist/js/bootstrap.min.js',
    './homer-theme/vendor/metisMenu/dist/metisMenu.min.js',
    './homer-theme/vendor/select2-3.5.2/select2.min.js',
    './homer-theme/vendor/select2-3.5.2/select2_locale_es.js',
    './homer-theme/vendor/sweetalert/lib/sweet-alert.min.js',
    './homer-theme/vendor/readmore/readmore.min.js'
];

// Instalacion archivos estaticos del sitio
self.addEventListener('install', e => {
    e.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                return cache.addAll(urlToCache)
                    .then(() => self.skipWaiting())
            })
            .catch(err => console.log('Fallo registro de la cache', err))
    )
});

// Funciones sin conexion
self.addEventListener('activate', e => {
    const cacheWhite = [CACHE_NAME];

    e.waitUntil(
        caches.keys()
            .then(cachesNames => {
                cachesNames.map(cacheName => {
                    // Eliminar los que no se necesite
                    if (cacheWhite.indexOf(cacheName) === -1)
                        return caches.delete(cacheName);
                })
            })
            .then(() => self.clients.claim())
    );
});

// Recuperar todos los recursos del navegador
self.addEventListener('fetch', e => {
    e.respondWith(
        caches
            .match(e.request)
            .then(res => {
                if (res)
                    return res;

                return fetch(e.request);
            })
    )
});