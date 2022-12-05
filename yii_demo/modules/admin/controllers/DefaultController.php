<?php

namespace app\modules\admin\controllers;

use Yii;
use app\modules\admin\controllers\AdminController;
use app\models\authors;
use app\models\AuthorSearch;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends AdminController {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {

        return $this->render('index');
    }

    public function actionAuthor() {

        $searchModel = new AuthorSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('../author/index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

}
