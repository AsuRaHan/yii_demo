<?php

namespace app\controllers;

use app\models\User;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\HttpHeaderAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;
use Yii;

/**
 * @OA\Info(title="Books library API", version="0.1")
 */
class ApiController extends Controller {
    public $enableCsrfValidation = false;

    public function init() {
        parent::init();
        \Yii::$app->user->enableSession = false;
    }

    public function behaviors() {

        $behaviors = parent::behaviors();

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'get-access-token' => ['post', 'put'],
            ],
        ];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
//                    'actions' => ['test'],
                    'allow' => true,

                ],

            ],
        ];

        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::class,
            'except' => ['get-access-token'],
        ];

//        $behaviors['corsFilter' ] = [
//            'class' => Cors::class,
//            'cors' => [
//                'Origin' => ['http://localhost', 'http://book.rrdev.ru'],
//                'Access-Control-Request-Method' => ['POST', 'PUT', 'GET', 'DELETE'],
//                'Access-Control-Request-Headers' => ['X-Limit', 'X-Offset', 'X-Sort-Field', 'X-Sort-Direction', 'X-Access-Token'],
//                'Access-Control-Expose-Headers' => ['X-Record-Count'],
//                'Access-Control-Allow-Credentials' => true,
//            ]
//        ];
        return $behaviors;
    }

    public function actionIndex() {
        \Yii::$app->response->format = Response::FORMAT_HTML;
        return $this->render('index');
    }

    /**
     * @OA\Post(
     *     path="/api/get-access-token",
     *     tags={"auth"},
     *     summary="Получить токен доступа в API",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="username",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 example={"username": "tester", "password": "123456"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Вернет access-token доступа",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *              ),
     *          )
     *      )
     * ,
     *     @OA\Response(
     *          response="409",
     *          description="Неудачный запрос",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="string",
     *                  default="Не верный логин или пароль"
     *              ),
     *          )
     *      )
     * )
     * @return Response
     */
    public function actionGetAccessToken() {

        $data = Yii::$app->request->getBodyParams();

        if(!isset($data['username'])){
            Yii::$app->response->statusCode = 409;
            $result =  ['error' => 'username not set in param'];
            return Controller::asJson($result);
        }

        if(!isset($data['password'])){
            Yii::$app->response->statusCode = 409;
            $result =  ['error' => 'password not set in param'];
            return Controller::asJson($result);
        }

        $user = User::findByUsername($data['username']);
        if(!$user){
            $result =  ['error' => 'user not found'];
            Yii::$app->response->statusCode = 409;
            return Controller::asJson($result);
        }

        if(!$user->validatePassword($data['password'])){
            $result =  ['error' => 'user password is wrong'];
            Yii::$app->response->statusCode = 409;
            return Controller::asJson($result);
        }

        $is_login = Yii::$app->user->login($user);
        if ($is_login) {
            $user->access_token = Yii::$app->security->generateRandomString();
            $user->save();
        }

        $result =  ['access_token' => yii::$app->user->identity->access_token];
        return Controller::asJson($result);
    }

    public function actionTest() {
        $data = \Yii::$app->request->post();;
        return $data;
    }
}
