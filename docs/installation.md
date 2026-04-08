# Installation guide

## System requirements

- [PHP](https://www.php.net/downloads) `8.2` or higher.
- [Composer](https://getcomposer.org/download/) for dependency management.

## Installation

### Method 1: Using [Composer](https://getcomposer.org/download/) (recommended)

Install the extension.

```bash
composer require yii2-framework/inertia-react:^0.1
```

### Method 2: Manual installation

Add to your `composer.json`.

```json
{
    "require": {
        "yii2-framework/inertia-react": "^0.1"
    }
}
```

Then run.

```bash
composer update
```

## Register the bootstrap integration

Enable the React adapter in your web configuration.

```php
// config/web.php
return [
    'bootstrap' => [
        \yii\inertia\react\Bootstrap::class,
    ],
];
```

Do not register `yii\inertia\Bootstrap::class` separately. The React bootstrap already delegates that setup.

## Application client-side dependencies

`yii2-framework/inertia-react` only ships the PHP adapter. The React runtime, Inertia client, and Vite bundler live
in the consuming application's `package.json`. There are two supported ways to install them.

### Option 1: `php-forge/foxy` (recommended)

[`php-forge/foxy`](https://github.com/php-forge/foxy) is a Composer plugin that runs Bun, npm, Yarn, or pnpm as part
of every `composer install` / `composer update`, so a single command provisions both PHP and JavaScript dependencies.

Add `php-forge/foxy` to your application's `composer.json` and declare the client-side packages in your `package.json`
at the project root.

```json
{
    "require": {
        "php-forge/foxy": "^0.2",
        "yii2-framework/inertia-react": "^0.1"
    },
    "config": {
        "allow-plugins": {
            "php-forge/foxy": true
        },
        "foxy": {
            "manager": "npm"
        }
    }
}
```

```json
{
    "private": true,
    "type": "module",
    "dependencies": {
        "@inertiajs/react": "^2.0",
        "react": "^19.0",
        "react-dom": "^19.0"
    },
    "devDependencies": {
        "@vitejs/plugin-react": "^6.0",
        "vite": "^8.0"
    }
}
```

Then run:

```bash
composer install
```

Foxy will detect the configured manager and install the Node modules automatically. Switch `foxy.manager` to `bun`,
`yarn`, or `pnpm` if you prefer a different tool.

### Option 2: direct `npm install`

If you prefer to keep Composer and your Node package manager decoupled, install the client-side packages yourself in
the Yii2 application project.

```bash
npm install react react-dom @inertiajs/react @vitejs/plugin-react vite
```

Yarn, pnpm, and Bun are supported the same way.

## When not to install this package

Do not install `yii2-framework/inertia-react` for applications that do not use React as their frontend framework. In
that scenario, use `yii2-framework/inertia-vue` or `yii2-framework/inertia-svelte` instead.

## Next steps

- ⚙️ [Configuration Reference](configuration.md)
- 💡 [Usage Examples](examples.md)
- 🧪 [Testing Guide](testing.md)
