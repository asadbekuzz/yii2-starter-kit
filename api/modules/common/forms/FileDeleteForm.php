<?php


namespace api\modules\common\forms;


use api\components\BaseRequest;
use api\modules\common\resources\FileResource;

class FileDeleteForm extends BaseRequest
{
  public FileResource $model;

  public function __construct(FileResource $model, $params = [])
  {
    $this->model = $model;

    parent::__construct($params);
  }

  public function getResult()
  {
    return $this->model->delete();
  }
}
