<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Category;

class SiteController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
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
        return $this->render('index');
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

    public function actionCategory() {
        $id = Yii::$app->request->get('category_id');
        $cats = [];
        $crumbs = [];
        $firstSort = false;
        if (!$id) {
            $cats = $this->getParentCat();
            $firstSort = false;
        } else {
            $cats = Category::find()->with(['children', 'parent'])->where(['id' => $id])->all();
            $crumbs = $this->generateBreadcrumbs($cats[0]);
            $cats = $cats[0]->children;
            $firstSort = true;
        }

        return $this->render('category', compact('cats', 'crumbs', 'firstSort'));
    }

    private function getParentCat() {
        $catsCash = yii::$app->cache->get('parentCategory');
        if ($catsCash) {
            return $catsCash;
        }
        $cats = Category::find()->with(['children', 'parent'])->where(['parent_id' => null])->all();
        yii::$app->cache->set('parentCategory', $cats, 60 * 60);
        return $cats;
    }

    private function generateBreadcrumbs($cats) {
        if (isset($cats->parent)) {
            $curArr = [];
            $curArr = array_merge($curArr, $this->generateBreadcrumbs($cats->parent));
            $curArr = array_merge($curArr, [['label' => $cats->name, 'url' => $cats->url]]);
            return $curArr;
        } else {
            return [['label' => $cats->name, 'url' => $cats->url]];
        }
    }

}
