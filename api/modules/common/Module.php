<?php

namespace api\modules\common;

/**
 * common module definition class
 */
class Module extends \yii\base\Module
{
  /**
   * {@inheritdoc}
   */
  public $controllerNamespace = 'api\modules\common\controllers';

  /**
   * {@inheritdoc}
   */
  public function init()
  {
    parent::init();

    // custom initialization code goes here
  }
}
