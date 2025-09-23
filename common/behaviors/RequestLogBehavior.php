<?php

namespace common\behaviors;

use Yii;
use common\models\RequestLog;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\web\Controller;

class RequestLogBehavior extends Behavior
{
    public array $methods = ['POST'];

    public function events(): array
    {
        return [
            Controller::EVENT_BEFORE_ACTION  => 'log',
        ];
    }


    public function log($event)
    {
        try {
            $_method = $this->getMethod();
            $user = Yii::$app->user;
            if (!in_array($_method, $this->methods))
                return;
            $request = new RequestLog([
                'method' => $this->getMethod(),
                'url' => $this->getUrl(),
                'ip' => $this->getUserIP(),
                'user_id' => $user ? $user->getId() : null,
                'params' => json_encode($this->getParams(),JSON_UNESCAPED_UNICODE),
            ]);
            if (!$request->save())
                Yii::error($request->errors);
        } catch (\Throwable $e) {
            Yii::error($e->getMessage());
        }
    }

    private function getParams(): array
    {
        $method = $this->getMethod();
        if ($method === 'POST') {
            return Yii::$app->request->bodyParams;
        } else if ($method === 'GET') {
            return Yii::$app->request->queryParams;
        }
        return [];
    }

    public function getMethod(): string
    {
        return Yii::$app->request->getMethod();
    }

    /**
     * @throws InvalidConfigException
     */
    public function getUrl(): string
    {
        return Yii::$app->request->getUrl();
    }

    /**
     * @throws InvalidConfigException
     */
    public function getUserIP(): string
    {
        return Yii::$app->request->getUserIP();
    }
}