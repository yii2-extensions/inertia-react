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

## Next steps

- 📚 [Installation Guide](installation.md)
- ⚙️ [Configuration Reference](configuration.md)
- 💡 [Usage Examples](examples.md)
- 🧪 [Testing Guide](testing.md)
