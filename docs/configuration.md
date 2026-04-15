# Configuration reference

## Overview

`yii2-extensions/inertia-react` provides a Vite asset helper registered as the `inertiaReact` application component.
The bootstrap class registers it automatically when missing.

## Basic configuration

Enable the package through the application bootstrap and configure the component.

```php
// config/web.php
return [
    'bootstrap' => [
        \yii\inertia\react\Bootstrap::class,
    ],
    'components' => [
        'inertiaReact' => [
            'class' => \yii\inertia\Vite::class,
            'baseUrl' => '@web/build',
            'devMode' => YII_ENV_DEV,
            'devServerUrl' => 'http://localhost:5173',
            'entrypoints' => [
                'resources/js/app.jsx',
            ],
            'manifestPath' => '@webroot/build/.vite/manifest.json',
            'modulePreload' => true,
            'preambleProvider' => \yii\inertia\react\Bootstrap::reactRefreshPreambleProvider(),
        ],
    ],
];
```

## Properties

The `inertiaReact` component extends `yii\inertia\Vite` from the base Inertia package. Every property below except
`preambleProvider` is inherited from that class; `preambleProvider` is the only option unique to this package and is
wired automatically by `yii\inertia\react\Bootstrap`.

### `baseUrl`

Base URL prefix for built assets referenced by the Vite manifest. Supports Yii aliases. Defaults to `@web/build`.

### `devMode`

When `true`, the component renders tags that point to the Vite dev server instead of reading the manifest. Defaults to
`false`.

### `devServerUrl`

Base URL of the Vite dev server. Required when `devMode` is `true`. Example: `http://localhost:5173`.

### `entrypoints`

One or more Vite entrypoints that should be rendered in the root view. Example: `['resources/js/app.jsx']`.

### `includeViteClient`

Controls whether the `@vite/client` script should be included in development mode. Defaults to `true`.

### `manifestPath`

Path to the Vite manifest file. Supports Yii aliases. Defaults to `@webroot/build/.vite/manifest.json`.

### `modulePreload`

Controls whether production mode should emit `modulepreload` tags for imported JavaScript chunks. Defaults to `true`.

### `preambleProvider`

A `Closure(string $devServerUrl): string` that returns an inline preamble script to emit before `@vite/client` in
development mode. When unset (`null`), no preamble is emitted.

Use `\yii\inertia\react\Bootstrap::reactRefreshPreambleProvider()` to wire the canonical React Refresh preamble required
by `@vitejs/plugin-react` for traditional backends.

## Next steps

- 📚 [Installation Guide](installation.md)
- 💡 [Usage Examples](examples.md)
- 🧪 [Testing Guide](testing.md)
