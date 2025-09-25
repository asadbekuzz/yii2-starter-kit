<?php

namespace api\components;

use Yii;
use common\behaviors\RequestLogBehavior;
use yii\base\Model;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\rest\Controller;
use yii\rest\OptionsAction;
use yii\web\Response;

abstract class ApiController extends Controller
{

    public function behaviors(): array
    {
        return parent::behaviors() + [
                'corsFilter' => [
                    'class' => Cors::class,
                    'cors' => [
                        // restrict access to
                        'Origin' => ['http://localhost:3000', 'http://xarid-storage.ebirja.uz', 'http://dxp.uz', 'https://dxp.uz', 'https://xarid.ebirja.uz', 'http://xarid.ebirja.uz/', 'xarid.ebirja.uz', 'ebirja.uz', 'http://dxp.uz/', 'https://dxp.uz/', 'http://tezkorxarid.uz', 'https://tezkorxarid.uz', 'http://dxp.uz/common/file/create', 'http://ebirja.uz', 'http://ebirja.uz/', 'https://test-xarid-api.ebirja.uz', 'http://test-xarid-api.ebirja.uz', 'https://test-xarid-app.ebirja.uz', 'http://test-xarid-app.ebirja.uz',],
                        // Allow only POST and PUT methods
                        'Access-Control-Request-Method' => ['GET', 'HEAD', 'POST', 'PUT'],
                        // Allow only headers 'X-Wsse'
                        'Access-Control-Request-Headers' => ['Origin', 'Content-Type', 'X-Auth-Token', 'Authorization', 'Accept', 'Referer', 'User-Agent', 'Headers'],
                        // Allow credentials (cookies, authorization headers, etc.) to be exposed to the browser
                        'Access-Control-Allow-Credentials' => true,
                        // Allow OPTIONS caching
                        'Access-Control-Max-Age' => 3600,
                        // Allow the X-Pagination-Current-Page header to be exposed to the browser.
                        // 'Access-Control-Expose-Headers' => ['Origin', 'Content-Type', 'X-Auth-Token', 'Authorization'],
                    ],
                ],
                [
                    'class' => ContentNegotiator::class,
                    'languages' => ['uzk','uz', 'ru'],
                    'formats' => [
                        'application/json' => Response::FORMAT_JSON,
                    ],
                ],

                'bearerAuth' => [
                    'class' => HttpBearerAuth::class,
                    'except' => [],
                    'optional' => []
                ],
                [
                    'class' => RequestLogBehavior::class,
                ]
            ];
    }

    public $enableCsrfValidation = false;


    public function actionOptions(): true
    {
        return true;
    }

    public function actions(): array
    {
        return [
            'options' => [
                'class' => OptionsAction::class
            ]
        ];
    }

    protected function sendResponse(Model $model, $params = []): array
    {
        $model->load($params, '');

        if ($model->validate()) {
            $result = $model->getResult();

            if ($result === false && !is_array($result)) {
                Yii::$app->response->statusCode = 422;
            }

            return [
                'result' => $result,
                'errors' => $model->errors
            ];
        } else {

            Yii::$app->response->statusCode = 422;

            return [
                'result' => null,
                'errors' => $model->errors,
            ];
        }
    }


    protected function sendResponsePost(Model $model, $params = [], $pksc7 = null, $pkcs_type = null): array
    {
        try {
            $model->load($params, '');

            if ($model->validate()) {
                $result = $model->getResult();


                if ($result == false && !is_array($result)) {
                    Yii::$app->response->statusCode = 422;
                } else {
                    Pkcs7Log::create($result,$pksc7, $params,$pkcs_type);
                }

                return [
                    'result' => $result,
                    'errors' => $model->errors
                ];
            } else {
                Yii::$app->response->statusCode = 422;

                return [
                    'result' => null,
                    'errors' => $model->errors,
                ];
            }
        } catch (\Throwable $exception) {
            Yii::$app->response->statusCode = 422;
            return [
                'result' => null,
                'errors' => [
                    'error' => [$exception->getMessage()]
                ],
            ];
        }
    }

    protected function sendModel($model)
    {
        return [
            'result' => $model,
            'errors' => null
        ];
    }

    protected function verifyPkcs7($params = [])
    {
        $model = new Pkcs7Form();
        $model->load($params, '');

        if ($model->validate()) {
            return $model->getResult();
        } else {

            Yii::$app->response->statusCode = 422;

            return [
                'result' => null,
                'errors' => $model->errors,
            ];
        }
    }


    protected function sendFile(Model $model, ...$params)
    {
        $attributes = [];
        foreach ($params as $param) {
             $attributes = array_merge($attributes, $param);
        }
        $model->load($attributes, '');

        // validate fails → 422
        if (!$model->validate()) {
            Yii::$app->response->statusCode = 422;
            return ['result' => null, 'errors' => $model->errors];
        }

        return $model->generateFile();
    }
}
