<?php


namespace api\modules\common\resources;

use common\models\File;
use common\models\query\NotDeletedFromCompanyQuery;

class FileResource extends File
{
    public function fields()
    {
        return [
            'id',
            'title',
            'size',
            'type',
            'path',
            'day'
        ];
    }

    public function getSrc()
    {
        return env('STORAGE_URL', 'http://xarid-storage.ebirja.uz') . $this->path;
    }

    public static function find()
    {
        return new NotDeletedFromCompanyQuery(get_called_class());
    }
}
