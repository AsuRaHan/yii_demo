<?php

namespace app\controllers;

use app\models\Book;
use app\models\BookSearch;
use app\models\User;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
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
                'books-list' => ['post'],
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
            'except' => [ // do not protect token this actions
                'get-access-token',
                'books-list'
            ],
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
     *     tags={"Auth API"},
     *     summary="Получить токен доступа(access_token) к API по логину и паролю пользователя. В дальнейшем этот токен используется ко всем приватным API запросам",
     *      @OA\Parameter(
     *          name="username",
     *          description="Имя пользователя",
     *          in="query",
     *          example="tester",
     *          @OA\Schema(
     *              type="string",
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="password",
     *          description="Пароль. внимание!!! пароль передается в открытом виде в теле запроса",
     *          in="query",
     *          example="123456",
     *          @OA\Schema(
     *              type="string",
     *          )
     *      ),
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

        $request = Yii::$app->request->getBodyParams();

        if (!isset($request['username'])) {
            Yii::$app->response->statusCode = 409;
            $result = ['error' => 'username not set in param'];
            return Controller::asJson($result);
        }

        if (!isset($request['password'])) {
            Yii::$app->response->statusCode = 409;
            $result = ['error' => 'password not set in param'];
            return Controller::asJson($result);
        }

        $user = User::findByUsername($request['username']);
        if (!$user) {
            $result = ['error' => 'user not found'];
            Yii::$app->response->statusCode = 409;
            return Controller::asJson($result);
        }

        if (!$user->validatePassword($request['password'])) {
            $result = ['error' => 'user password is wrong'];
            Yii::$app->response->statusCode = 409;
            return Controller::asJson($result);
        }

        $is_login = Yii::$app->user->login($user);
        if ($is_login) {
            $user->access_token = Yii::$app->security->generateRandomString();
            $user->save();
        }

        $access_token = yii::$app->user?->identity?->access_token;
        if (!$access_token) {
            $result = ['error' => 'error get access token on user'];
            Yii::$app->response->statusCode = 409;
            return Controller::asJson($result);
        }
        $result = ['access_token' => $access_token];
        return Controller::asJson($result);
    }

    #[Route('/api/test', name: 'test', methods: ['GET'])]
    public function actionTest() {
        $data = \Yii::$app->request->getBodyParams();
        return $data;
    }

    /**
     * @OA\Post(
     *     path="/api/books-list",
     *     tags={"Book API"},
     *     summary="Получить список книг с учетом пагинации, сортировки и поиска. Для этого в теле запроса есть следующие параметры",
     *      @OA\Parameter(
     *          name="limit",
     *          description="Количетво элементов, которое необходимо вернуть",
     *          in="query",
     *          example="10",
     *          @OA\Schema(
     *              type="intval",
     *              default="20"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="offset",
     *          description="Позиция, начиная с которой, необходимо вернуть элементы",
     *          in="query",
     *          example="0",
     *          @OA\Schema(
     *              type="intval",
     *              default="0"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="sort_field",
     *          description="Поле по которому необходимо сортировать (id, name)",
     *          in="query",
     *          example="name",
     *          @OA\Schema(
     *              type="string",
     *              default="id"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="sort_direction",
     *          description="Направление сортировки(ASC, DESC)",
     *          in="query",
     *          example="ASC",
     *          @OA\Schema(
     *              type="string",
     *              default="DESC"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="search_string",
     *          description="Строка поиска по названию или описанию книги",
     *          in="query",
     *          example="Война за мир",
     *          @OA\Schema(
     *              type="string",
     *              default="null"
     *          )
     *      ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="offset",
     *                     type="intval"
     *                 ),
     *                 @OA\Property(
     *                     property="limit",
     *                     type="intval"
     *                 ),
     *                 example={"offset":0,"limit":2,"sort_field":"name","sort_direction":"DESC","search_string":""}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Вернет Объект с книгами а так же общее колличество книг в базе",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *              ),
     *              example={"count":100,"column":{"id","user_is","name","description","isbn","image"},"list":{{"id": 1,"user_is": 0,"name": "Война и мир","description": "Ex quia sint...", "isbn": "9795596952420","image": "https://img.net/image.png" },{"id": 2,"user_is": 0, "name": "Майн кампф","description": "Quo harum dolore et plicabo...","isbn":"9799515349964","image": "https://avatars.mds.yandex.net/get-kinopoisk-image.png"}}}
     *          )
     *      ),
     *     @OA\Response(
     *          response="409",
     *          description="Неудачный запрос",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="string",
     *                  default="Если произойдет ошибка. то вернет сообщение об ошибки"
     *              ),
     *              example={"error": "Sort field name is wrong"}
     *          )
     *      )
     * )
     * @return Response
     * @throws InvalidConfigException
     */
    public function actionBooksList() {

        $request = Yii::$app->request->getBodyParams();

        $offset = $request['offset']??0;
        $limit = $request['limit']??20;
        $sortField = $request['sort_field']??'id';

        $columns = Yii::$app->db->getTableSchema(Book::tableName())->columns;

        if (!array_key_exists($sortField, $columns)) {
            Yii::$app->response->statusCode = 409;
            $result = ['error' => 'Sort field name is wrong'];
            return Controller::asJson($result);
        }

        $sortDirection = $request['sort_direction']??'ASC';
        if(strtolower($sortDirection)=='desc'){
            $sortDirection = SORT_DESC;
        }else{
            $sortDirection = SORT_ASC;
        }

        $searchString = $request['search_string']??null;
        if($searchString){
            $bookSearch = new BookSearch();
            $searchString = $bookSearch->cleanSearchString($searchString);
            $query = $bookSearch->getQuerySearchResult($searchString);
        }else{
            $query = Book::find();
        }

        $countQuery = clone $query;
        $list = $query->offset(intval($offset))
            ->orderBy([$sortField => $sortDirection])
            ->limit(intval($limit))
            ->all();

        $result = [
            'count'=>$countQuery->count(),
            'column'=>array_keys($columns),
            'list'=>$list,
        ];

        return Controller::asJson($result);

    }
}
