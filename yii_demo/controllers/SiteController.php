<?php

namespace app\controllers;

use app\models\BookSearch;
use app\models\SignupForm;
use Yii;
use yii\base\InvalidParamException;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Book;
use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;

class SiteController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout','checkout'],
                'rules' => [
                    [
                        'actions' => ['logout','checkout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post', 'get'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
//        $query = Book::find();
//        $countQuery = clone $query;
//        $pages = new Pagination(['totalCount' => $countQuery->count(), 'forcePageParam' => false, 'pageSizeParam' => false]);
//        $models = $query->offset($pages->offset)
//            ->limit($pages->limit)
//            ->all();
        return $this->render('index'
//            , [
//            'models' => $models,
//            'pages' => $pages,
//        ]
        );
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin() {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout() {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact() {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout() {
        return $this->render('about');
    }

    public function actionSignup() {
        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset() {
        $model = new PasswordResetRequestForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token) {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');
            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model]);
    }

    public function actionBook() {
        $book = Book::findOne(yii::$app->request->get('id'));
        if(!$book){
            Yii::$app->session->setFlash('error', "Book ID not found");
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }
        return $this->render('bookInfo', ['book' => $book]);
    }

    public function actionSearch($query = '', $page = 1) {
        if (Yii::$app->request->isPost) {
            $query = Yii::$app->request->post('query');
            if (is_null($query)) {
                return $this->redirect(['site/search']);
            }
            $query = trim($query);
            if (empty($query)) {
                return $this->redirect(['site/search']);
            }
            $query = urlencode(Yii::$app->request->post('query'));
            return $this->redirect(['site/search/query/' . $query]);
        }

        $page = (int)$page;

        // результаты поиска с постраничной навигацией
        list($books, $pages) = (new BookSearch())->getSearchResult($query, $page);

        return $this->render(
            'search',
            compact('books', 'pages', 'query')
        );
    }

    public function actionCheckout() {
        $book = Book::findOne(yii::$app->request->get('id'));
        if(!$book){
            Yii::$app->session->setFlash('error', "Book ID not found");
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }
        return $this->render('checkout',['book' => $book]);
    }
}
