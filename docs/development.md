# Development notes

## Scope

This package is a thin adapter over `yii2-extensions/inertia`.

It intentionally does not include.

- React components for your application pages.
- npm dependency installation.
- SSR setup.
- A replacement for classic Yii2 jQuery widgets.

## Adapter responsibility

The PHP-side responsibility of this package is to connect the base Inertia server package with a React/Vite frontend
by providing.

- a React-oriented bootstrap class;
- a root view that renders the page payload and asset tags;
- a canonical `yii\inertia\Vite` helper (inherited from the base package) that understands the manifest and
  development server modes;
- a `reactRefreshPreambleProvider()` factory that ships the React Refresh preamble required by `@vitejs/plugin-react`
  on traditional backends; it is assigned unconditionally to `Vite::$preambleProvider` during bootstrap, and the
  preamble is only emitted at runtime when `Vite::renderTags()` detects development mode.

## Inertia v3 alignment

The package assumes the v3-style initial page payload output via a `<script type="application/json">` element, which
matches the current `yii2-extensions/inertia` base package implementation.

## HMR / dev server workflow

`vite build` is a one-shot build: it emits hashed assets into `public/build/` and exits. Editing a `.jsx` file after
`npm run build` has no effect on the browser until another build is run. To develop against live source, the Vite dev
server must be running and Yii2 must be in dev mode at the same time.

Run two processes side by side:

```bash
# Terminal 1 — Vite dev server (HMR websocket on :5173)
npm run dev

# Terminal 2 — Yii2 with YII_ENV=dev
YII_ENV=dev ./yii serve
```

### How the pieces connect

- `public/index.php` reads the `YII_ENV` environment variable. The application configuration should flip
  `inertiaReact.devMode` based on that value (for example, `YII_ENV === 'dev'`).
- When `devMode` is `true`, `\yii\inertia\Vite::renderDevelopmentTags()` emits, in order:
  - the React Refresh preamble (returned by `reactRefreshPreambleProvider()`, with
    `__VITE_DEV_SERVER_URL__` substituted for the configured `devServerUrl`);
  - `<script type="module" src="{devServerUrl}/@vite/client">`, which opens the Vite HMR WebSocket;
  - `<script type="module" src="{devServerUrl}/{entrypoint}">` for each configured entrypoint.
- Vite detects source changes, pushes module updates over the WebSocket, and `@vitejs/plugin-react` performs Fast
  Refresh on React components while preserving local state.

### Cross-origin requests to the Vite dev server

The PHP application and the Vite dev server run on different origins (typically `http://localhost:8080` and
`http://localhost:5173`), so the browser loads modules cross-origin. Two distinct Vite options govern this:

- `server.origin` — only rewrites asset URLs emitted during development so that imports resolve to absolute URLs. It
  does not affect CORS headers. Keep it in sync with `devServerUrl` on the PHP side.
- `server.cors` — controls the CORS headers the Vite dev server actually sends. Vite's default allowlist accepts
  requests from `localhost`, `127.0.0.1`, and `[::1]`, which is why a plain `localhost` setup works out of the box.

For non-localhost setups (Docker hostnames, reverse proxies, tunnels, custom domains), the default allowlist does not
match and module fetches will be blocked. Extend `server.cors` explicitly, for example:

```js
// vite.config.js
export default defineConfig({
  server: {
    origin: "http://myapp.test:5173",
    cors: {
      origin: "http://myapp.test:8080",
    },
  },
});
```

If you change Vite's port, update `devServerUrl` in the PHP configuration to the same value.

### Troubleshooting

- **Browser shows 404 or connection refused for `/@vite/client`** the page is rendering dev-server tags (`devMode`
  is `true`) but the Vite dev server is unreachable at `devServerUrl`. Confirm `npm run dev` is live and that the host and
  port match `devServerUrl`.
- **No HMR, but the page loads normally** `devMode` resolved to `false`, so the Vite helper rendered manifest tags
  from `public/build/` and `/@vite/client` was never requested. Confirm that `YII_ENV=dev` reached the PHP process and
  that your configuration actually flips `inertiaReact.devMode` based on it.
- **Port 5173 already in use** free the port, or change Vite's `server.port` and the PHP `devServerUrl` to the new value
  together. Mismatches silently break the page.
- **Mixed-content warnings over HTTPS** either terminate TLS in front of Yii2 with the same protocol on both sides and
  enable `server.https` in `vite.config.js`, or run both over plain HTTP during development.
- **Stale assets after switching modes** hard refresh the browser (Ctrl+Shift+R) after toggling `YII_ENV` so the browser
  discards the previous module graph.

### Switching back to production

```bash
# stop the Vite dev server, then:
npm run build
unset YII_ENV   # or: export YII_ENV=prod
./yii serve
```

In production mode the Vite helper reads `public/build/.vite/manifest.json` and emits hashed asset tags; the dev
server is not contacted.

## Next steps

- 📚 [Installation Guide](installation.md)
- ⚙️ [Configuration Reference](configuration.md)
- 💡 [Usage Examples](examples.md)
- 🧪 [Testing Guide](testing.md)
