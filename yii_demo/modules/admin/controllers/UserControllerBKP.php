<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\rest\ActiveController;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use yii\filters\Cors;
use app\models\User;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

class UserController extends ActiveController {

    public $modelClass = 'app\models\User';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'data',
    ];

    public function init() {
        parent::init();
        Yii::$app->user->enableSession = false;
    }

    public function behaviors() {
        $behaviors = parent::behaviors();
        
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        
//        $behaviors['authenticator'] = [
//            'class' => CompositeAuth::class,
//            'except' => ['options', 'login'],
//        ];
        $behaviors['authenticator'] = [
                'class' => CompositeAuth::class,
                'authMethods' => [
                    QueryParamAuth::class,
                ],
            ];

        return $behaviors;
    }

//    public function behaviors() {
//        return [
//            'contentNegotiator' => [
//                'class' => ContentNegotiator::class,
//                'formats' => [
//                    'application/json' => Response::FORMAT_JSON,
//                ],
//            ],
//            'access' => [
//                'class' => AccessControl::class,
//                'rules' => [
//                    [
//                        'allow' => true,
//                        'actions' => ['index'],
//                        'roles' => ['admin'],
//                    ],
//                    [
//                        'allow' => true,
//                        'actions' => ['test'],
//                        'roles' => ['?'],
//                    ],
//                ],
//            ],
//            'authenticator' => [
//                'class' => CompositeAuth::class,
//                'authMethods' => [
////                    HttpBasicAuth::class,
//                    HttpBearerAuth::class,
////                    QueryParamAuth::class,
//                ],
//            ]
//        ];
//    }

    public function actionTest() {
        $faker = \Faker\Factory::create();

        $iter = 666;
//        for ($i = 1; $i <= 100; $i++) {
//
////            $user->setIsNewRecord(true);
//            $user = new User();
//            $user->username = $faker->username;
//            $user->email = $faker->email;
//            $user->password_hash = Yii::$app->getSecurity()->generatePasswordHash('123456');
//            $user->auth_key = '';
//            $user->created_at = Yii::$app->formatter->asTimestamp(date('Y-d-m h:i:s'));
//            $user->updated_at = Yii::$app->formatter->asTimestamp(date('Y-d-m h:i:s'));
//            $user->save();
//            $iter = $i;
//        }

        return ['Id' => $iter];
    }

}
