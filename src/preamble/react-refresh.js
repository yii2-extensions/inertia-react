/**
 * React Refresh preamble for `@vitejs/plugin-react` on traditional backends.
 *
 * This script installs the Fast Refresh runtime hooks on `window` before the Vite client module graph loads, so
 * React components can hot-reload during development when the Vite dev server is fronted by PHP (instead of Vite's
 * own middleware). It is the canonical preamble published by the Vite team, mirrored verbatim except for the
 * `__VITE_DEV_SERVER_URL__` placeholder.
 *
 * The `__VITE_DEV_SERVER_URL__` token is substituted at runtime by
 * {@link https://github.com/yii2-framework/inertia-react `yii\inertia\react\Bootstrap::reactRefreshPreambleProvider()`}
 * with the resolved dev server URL (for example, `http://localhost:5173`) before the script is emitted inline in
 * the root view by `yii\inertia\Vite`.
 *
 * @see https://vite.dev/guide/backend-integration.html
 */
import RefreshRuntime from '__VITE_DEV_SERVER_URL__/@react-refresh'

RefreshRuntime.injectIntoGlobalHook(window)
window.$RefreshReg$ = () => {}
window.$RefreshSig$ = () => (type) => type
window.__vite_plugin_react_preamble_installed__ = true
