<?php

declare(strict_types=1);

use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\inertia\{Page, Vite};
use yii\web\View;

/**
 * @var string $id Unique ID for the root element.
 * @var Page $page Page data.
 * @var string $pageJson JSON-encoded page data.
 * @var View $this View component instance.
 */
$vite = \Yii::$app->get('inertiaReact');

if (!$vite instanceof Vite) {
    throw new InvalidConfigException(
        "The 'inertiaReact' application component must be an instance of " . Vite::class . '.',
    );
}

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Html::encode(\Yii::$app->language) ?>">
<head>
    <meta charset="<?= Html::encode(\Yii::$app->charset) ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title data-inertia><?= Html::encode(\Yii::$app->name) ?></title>
    <?= Html::csrfMetaTags() ?>
    <?php $this->head(); ?>
    <?= $vite->renderTags() ?>
</head>
<body>
<?php $this->beginBody(); ?>
<div id="<?= Html::encode($id) ?>">
    <script type="application/json"><?= $pageJson ?></script>
</div>
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
