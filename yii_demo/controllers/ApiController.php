<?php

namespace app\controllers;

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


/**
 * @OA\Info(title="Book API", version="0.1")
 */
class ApiController extends Controller {
    public $enableCsrfValidation = false;

    public function init() {
        parent::init();
        \Yii::$app->user->enableSession = false;
//        $this->enableCsrfValidation = false;
    }

    public function behaviors() {

        $behaviors = parent::behaviors();

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'get-access-token' => ['post'],
            ],
        ];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'actions' => ['get-access-token'],
                    'allow' => true,
                    'roles' => ['@', '?'],
                ],
//                [
//                    'allow' => true,
//                    'roles' => ['admin'],
//                ],
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
//            'class' => CompositeAuth::class,
//            'authMethods' => [
//                QueryParamAuth::class,
//            ],
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
     *     tags={"Student"},
     *     summary="Получить токен доступа в API",
     *     @OA\RequestBody(
     *          description="Данные пользователя",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *              ),
     *          )
     *      ),
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
        return [1, 2, 3, 45, 67, 8, 5];
    }
}
