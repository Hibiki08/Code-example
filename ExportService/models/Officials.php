<?php

namespace common\models\officials;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\behaviors\BlameableBehavior;
use yii\db\Expression;
use common\components\ThumbHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseFileHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%OFFICIALS}}".
 *
 * @property integer $ID
 * @property string $FIO
 * @property string $POSITION
 * @property integer $WEIGHT
 * @property string $PHONE
 * @property string $EMAIL
 * @property string $BIOGRAPHY
 * @property string $PREVIEW
 * @property boolean $VISIBILITY
 * @property string $CREATED
 * @property string $UPDATED
 * @property integer $AUTHOR_ID
 * @property integer $UPDATER_ID
 *
 * @property-read OfficialsFiles[] $officialsFiles
 * @property-read OfficialsFiles[] $sortedFiles
 * @property-read array $filePaths
 * @property-read array $fileConfig
 *
 */
class Officials extends ActiveRecord
{
    /** @var UploadedFile */
    public $previewFile;

    /** @var UploadedFile[] */
    public $files = [];

    /** @var string */
    public $filesSort;

    const FILES_FOLDER = 'files/officials/';

    const RATIO_ERROR_MESSAGE = 'Загружен файл с неверным разрешением сторон изображения. Допустимое минимальное 
    разрешение - 438 × 527 px. Пропорции соотношения сторон должны быть 3:4';
    const EXTENSION_ERROR_MESSAGE = 'Загружен файл в неверном формате. Допустимые форматы: {extensions}';
    const TOO_BIG_ERROR_MESSAGE = 'Файл "{name}" ({size} KB) превышает максимальный размер {maxSize} KB.';

    public static function tableName()
    {
        return '{{%OFFICIALS}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $imageRules = [
            'image',
            'checkExtensionByMimeType' => false,
            'extensions' => ['png', 'jpg', 'jpeg'],
            'wrongExtension' => self::EXTENSION_ERROR_MESSAGE,
            'maxSize' => 5 * 1024 * 1024, // 5MB
            'tooBig' => 'Файл "{file}" превышает максимальный размер {formattedLimit}.'
        ];
        return [
            [['FIO', 'POSITION', 'WEIGHT'], 'required'],
            [['FIO', 'POSITION', 'PREVIEW', 'PHONE', 'EMAIL'], 'string', 'max' => 200],
            ['EMAIL', 'email', 'enableIDN' => true,],
            ['VISIBILITY', 'number'],
            ['VISIBILITY', 'in', 'range' => [0, 1]],
            ['VISIBILITY', 'default', 'value' => 0],
            ['BIOGRAPHY', 'string', 'max' => 4095],
            ['WEIGHT', 'integer'],
            array_merge(['previewFile'], $imageRules, [
                'minWidth' => 527,
                'minHeight' => 438,
                'underHeight' => self::RATIO_ERROR_MESSAGE,
                'underWidth' => self::RATIO_ERROR_MESSAGE,
            ]),
            ['files', 'each', 'rule' => $imageRules],
            ['filesSort', 'string', 'max' => 500],
            [
                ['WEIGHT'], 'unique', 'targetAttribute' => ['WEIGHT'],
            ],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'FIO' => 'ФИО',
            'VISIBILITY' => 'Видимость',
            'BIOGRAPHY'=> 'Биография',
            'POSITION'=> 'Должность',
            'PHONE'=> 'Номер телефона',
            'EMAIL'=> 'Адрес электронной почты',
            'PREVIEW' => 'Превью',
            'WEIGHT' => 'Очередность отображения на портале',
            'CREATED' => 'Создано',
            'UPDATED' => 'Обновлено',
            'files' => 'Медиафайлы',
            'previewFile' => 'Превью',
        ];
    }

    public static function find()
    {
        return new OfficialsQuery(get_called_class());
    }

    public function getOfficialsFiles(): \yii\db\ActiveQuery
    {
        return $this->hasMany(OfficialsFiles::class, ['ID_OWN' => 'ID']);
    }

    public function getSortedFiles()
    {
        return $this->getOfficialsFiles()->orderBy('WEIGHT');
    }
    
    /**
     * @return string
     */
    public function getPreviewPath(): string
    {
        if ($this->PREVIEW) {
            return self::getAbsoluteUploadPath() . "{$this->ID}/{$this->PREVIEW}";
        }
        return '';
    }

    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    public function uploadPreview(): bool
    {
        if ($this->previewFile) {
            return $this->upload($this->previewFile);
        }
        return false;
    }

    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    public function uploadFiles(): bool
    {
        if ($this->files) {
            foreach ($this->files as $file) {
                $this->upload($file);
            }
            return true;
        }
        return false;
    }

    /**
     * @param UploadedFile $file
     * @return bool
     * @throws \yii\base\Exception
     */
    public function upload(UploadedFile $file): bool
    {
        $folderPath = self::getAbsoluteUploadPath() . "{$this->ID}/";
        if (!file_exists($folderPath)) {
            BaseFileHelper::createDirectory($folderPath);
        }
        return $file->saveAs($folderPath . $file->name);
    }

    /**
     * @return bool
     */
    public function deletePreview(): bool
    {
        $fileName = $this->PREVIEW;
        $this->PREVIEW = null;
        if ($this->save()) {
            $filePath = self::getAbsoluteUploadPath() . "{$this->ID}/{$fileName}";
            if (file_exists($filePath)) {
                return BaseFileHelper::unlink($filePath);
            }
            return true;
        }
        return false;
    }

    /**
     * @param OfficialsFiles $file
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function deleteFile(OfficialsFiles $file): bool
    {
        if ($file->delete()) {
            $filePath = self::getAbsoluteUploadPath() . $this->ID . '/' . $file->FILE_NAME;
            if (file_exists($filePath)) {
                return BaseFileHelper::unlink($filePath);
            }
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public static function getAbsoluteUploadPath(): string
    {
        return Yii::getAlias('@' . self::FILES_FOLDER);
    }

    /**
     * @return string
     */
    public static function getFolderPath(): string
    {
        return self::FILES_FOLDER;
    }
}
