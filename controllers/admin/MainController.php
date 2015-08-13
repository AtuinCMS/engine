<?php
namespace atuin\engine\controllers\admin;


use atuin\skeleton\controllers\admin\BaseAdminController;

class MainController extends BaseAdminController
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionError()
    {
        echo '<pre>';
        	echo var_dump('skafasfjasdjlf');
        echo '</pre>';
    }
}
