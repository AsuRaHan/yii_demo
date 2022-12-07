<?php

namespace app\controllers;

//use yii\console\Controller;
use yii\web\Controller;
use Yii;

class SwaggerController extends Controller
{

    public function actionGo()
    {
        $openApi = \OpenApi\Generator::scan([Yii::getAlias('@app/controllers')]);
        $file = Yii::getAlias('@app/web/doc/swagger.yaml');
        $handle = fopen($file, 'wb');
        fwrite($handle, $openApi->toYaml());
        fclose($handle);
        return 'finish';//$this->redirect('/doc');
    }

}