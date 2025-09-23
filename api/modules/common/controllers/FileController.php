<?php

namespace api\modules\common\controllers;

use api\components\ApiController;
use api\modules\common\filters\FileFilter;
use api\modules\common\forms\FileDeleteForm;
use api\modules\common\forms\FileForm;
use api\modules\common\forms\FileImageForm;
use api\modules\common\resources\FileResource;
use Yii;
use yii\web\NotFoundHttpException;

class FileController extends ApiController
{
    public function actionView($id)
    {
        return $this->sendResponse(
            new FileFilter(),
            Yii::$app->request->get()
        );
    }

    public function actionCreate()
    {
        return $this->sendResponse(
            new FileForm(new FileResource()),
            Yii::$app->request->bodyParams
        );
    }
    public function actionCreateImage()
    {
        return $this->sendResponse(
            new FileImageForm(new FileResource()),
            Yii::$app->request->bodyParams
        );
    }
    public function actionDownload($id)
    {
        $file = $this->findOne($id);
        if ($file) {
            $path = \Yii::getAlias('@storage') . '/web/source/' . $file->day .'/'. $file->path;
            if (file_exists($path)) {
                return Yii::$app->response->sendFile($path, $path);
            }
        }
        return null;
    }

    public function actionDownloadFile($id)
    {

        $file = $this->findOne($id);
        if ($file) {
            $path = \Yii::getAlias('@storage') . '/web/source/' . $file->day . '/' . $file->path;
            if (file_exists($path)) {
                return Yii::$app->response->sendFile($path, $path);
            }
        }

        return null;
    }

    //    public function actionUpdate($id) {
    //        return $this->sendResponse(
    //            new FileForm($this->findOne($id)),
    //            Yii::$app->request->bodyParams
    //        );
    //    }

//    public function actionDelete($id)
//    {
//        return $this->sendResponse(
//            new FileDeleteForm($this->findOne($id)),
//            Yii::$app->request->queryParams
//        );
//    }

    private function findOne($id)
    {
        $model = FileResource::findOne($id);

        if (!$model) throw new NotFoundHttpException("File not found");

        return $model;
    }
}
