<?php

namespace app\controllers;

use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class ProfileController extends Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex() {
        return $this->render('index', [
            'model' => $this->findModel(),
        ]);
    }

    /**
     * @return User the loaded model
     */
    private function findModel() {
        return User::findOne(Yii::$app->user->identity->getId());
    }
}
