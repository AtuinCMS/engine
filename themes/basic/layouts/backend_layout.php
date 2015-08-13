<?php
use atuin\engine\assets\AppAsset;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);

$isGuest = Yii::$app->user->getIsGuest();


$this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= $this->title ?></title>
    <?php $this->head() ?>
</head>
<body class="skin-blue sidebar-mini<?= (($isGuest) ? ' layout-top-nav' : '') ?>">
<?php $this->beginBody() ?>
<div class="wrapper">
    <header class="main-header">
        <?php
        if ($isGuest === TRUE)
        {
            $this->beginContent('@vendor/atuin/engine/views/backend_header_logged_out.php');
        } else
        {
            $this->beginContent('@vendor/atuin/engine/views/backend_header_logged_in.php');
        }
        $this->endContent();
        ?>
    </header>

    <?php
    if ($isGuest !== TRUE)
    {
        $this->beginContent('@vendor/atuin/engine/views/backend_sidebar_logged_in.php');
        $this->endContent();
    }
    ?>

    <!-- /.main-sidebar-->
    <!-- The Right Sidebar for special events-->
    <aside class="control-sidebar control-sidebar-light">
        <!-- Content of the sidebar goes here -->
    </aside>
    <!-- The sidebar's background -->
    <!-- This div must placed right after the sidebar for it to work-->
    <div class="control-sidebar-bg"></div>
    <div class="content-wrapper">

        <?= $content ?>

    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <?= Yii::powered() ?>
        </div>
        <strong>
            Atuin &copy; 2015
        </strong>
        No rights reserved.
    </footer>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
