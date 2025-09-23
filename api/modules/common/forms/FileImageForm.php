<?php


namespace api\modules\common\forms;


use api\components\BaseRequest;
use api\modules\common\resources\FileResource;
use common\enums\StatusEnum;
use Yii;
use yii\base\Exception;

class FileImageForm extends BaseRequest
{
    public FileResource $model;

    public $title;
    public $fayl;

    public function __construct(FileResource $model, $params = [])
    {
        $this->model = $model;

        parent::__construct($params);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required', 'message' => t('{attribute} yuborish majburiy')],
            [['fayl'], 'file', 'extensions' => ['jpg', 'jpeg', 'png'], 'wrongExtension' => t("Yuklangan fayl formati noto'g'ri"),
                'message' => t("Yuklangan fayl formati noto'g'ri")],
            [['fayl'], 'file', 'maxSize' => 1024 * 1024, 'tooBig' => t("1 mb dan katta fayl yukladingiz")],
            ['title', 'string']
        ];
    }

    //fayl yuklash kerak

    /**
     * @throws Exception
     */
    public function getResult()
    {

        $fayl = \yii\web\UploadedFile::getInstanceByName('fayl');
        $this->fayl = $fayl;

        if ($fayl) {

            if (!$this->validate()) {
                return false;
            }
            $currentDate = date('Y-m-d');

            $folderPath = \Yii::getAlias('@storage') . '/web/image/' . $currentDate;

            if (!is_dir($folderPath)) {
                mkdir($folderPath, 0777, true);
            }

            $fayl_filename = '/' . str_replace('.' . $fayl->extension, '', $fayl->name) . '_' . (int)microtime(true) . '.' . $fayl->extension;

            $fayl->saveAs($folderPath . $fayl_filename);

            $this->model->path = $fayl_filename;
            $this->model->day = $currentDate;
            $this->model->title = $this->title;
            $this->model->size = $fayl->size;
            $this->model->type = $fayl->extension;
        } else {
            throw new Exception("Fayl yuborilmagan");
        }

        $this->model->status = StatusEnum::STATUS_ACTIVE;
        if (!$this->model->save()) {
            unlink($folderPath . $fayl_filename);
            $this->addErrors($this->model->errors);
            return false;
        }
        return $this->model->id;
    }
}
