/* Service worker do ForTEA
   - Arquivos estáticos (css, js, imagens): guardados no aparelho e atualizados em segundo plano.
   - Páginas .php: SEMPRE buscadas na internet (têm dados da conta). Sem internet, mostra offline.html.
   - Nunca guarda fotos de /uploads/, nem nada que não seja GET. */

const VERSAO = 'fortea-v1';
const CACHE = VERSAO + '-estatico';
const PRE_CACHE = ['offline.html', 'css/estilo.css', 'css/responsivo.css', 'img/icons/icon-192.png'];
const ESTATICO = /\.(css|js|png|jpe?g|webp|gif|svg|ico|woff2?|ttf)$/i;

self.addEventListener('install', (e) => {
  e.waitUntil(
    caches.open(CACHE).then((c) => Promise.all(PRE_CACHE.map((u) => c.add(u).catch(() => {}))))
  );
  self.skipWaiting();
});

self.addEventListener('activate', (e) => {
  e.waitUntil(
    caches.keys()
      .then((nomes) => Promise.all(nomes.filter((n) => n !== CACHE).map((n) => caches.delete(n))))
      .then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (e) => {
  const req = e.request;
  if (req.method !== 'GET') return;

  const url = new URL(req.url);
  if (url.origin !== location.origin) return;

  // Páginas: internet primeiro; sem internet, tela "você está offline"
  if (req.mode === 'navigate') {
    e.respondWith(fetch(req).catch(() => caches.match('offline.html')));
    return;
  }

  // Estáticos (menos as fotos privadas dos usuários)
  if (ESTATICO.test(url.pathname) && !url.pathname.includes('/uploads/')) {
    e.respondWith(
      caches.open(CACHE).then(async (cache) => {
        const guardado = await cache.match(req);
        const rede = fetch(req)
          .then((res) => { if (res.ok) cache.put(req, res.clone()); return res; })
          .catch(() => guardado);
        return guardado || rede;
      })
    );
  }
});