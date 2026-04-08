# Usage examples

## React bootstrap in Yii2

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
            'preambleProvider' => \yii\inertia\react\Bootstrap::reactRefreshPreambleProvider(),
        ],
    ],
];
```

## React client entrypoint

```jsx
import { createInertiaApp } from "@inertiajs/react";
import { createElement } from "react";
import { createRoot } from "react-dom/client";

createInertiaApp({
  resolve: (name) => {
    const pages = import.meta.glob("./Pages/**/*.jsx", { eager: true });
    return pages[`./Pages/${name}.jsx`];
  },
  setup({ el, App, props }) {
    createRoot(el).render(createElement(App, props));
  },
});
```

## Vite configuration

```js
import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";

export default defineConfig({
  plugins: [react()],
  build: {
    manifest: true,
    rollupOptions: {
      input: "resources/js/app.jsx",
    },
  },
});
```

## Next steps

- 📚 [Installation Guide](installation.md)
- ⚙️ [Configuration Reference](configuration.md)
- 🧪 [Testing Guide](testing.md)
