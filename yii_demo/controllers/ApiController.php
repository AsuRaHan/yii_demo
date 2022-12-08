<?php

namespace app\controllers;

use app\models\User;
use yii\base\InvalidConfigException;
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
     *     summary="Получить токен доступа(access_token) к API по логину и паролю пользователя. В дальнейшем этот токен используется ко всем приватным API запросам",
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
     *              example={"access_token": "N3s7HMsYpYF5D4-l9M7gyrK8F0qWek2K"}
     *          )
     *      ),
     *     @OA\Response(
     *          response="409",
     *          description="Неудачный запрос",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="string",
     *                  default="Не верный логин или пароль"
     *              ),
     *              example={"error": "user not found"}
     *          )
     *      )
     * )
     * @return Response
     * @throws InvalidConfigException
     */
    public function actionGetAccessToken(): Response {

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

        $access_token = yii::$app->user?->identity?->access_token;
        if(!$access_token){
            $result =  ['error' => 'error get access token on user'];
            Yii::$app->response->statusCode = 409;
            return Controller::asJson($result);
        }
        $result =  ['access_token' => $access_token];
        return Controller::asJson($result);
    }
    #[Route('/api/test',name:'test',methods: ['GET'])]
    public function actionTest() {
        $data = \Yii::$app->request->getBodyParams();
        return $data;
    }
}
