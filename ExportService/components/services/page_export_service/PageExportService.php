<?php

namespace frontend\components\services\page_export_service;

use \LogicException;
use Yii;
use yii\base\Component;

/**
 * Class PageExportService
 * @package frontend\components\services\page_export_service
 *
 * @property array $exportEntities
 */
class PageExportService extends Component
{
    /** @var string */
    protected $format;

    /** @var string */
    protected $fileName = '';

    /** @var array */
    protected $params = [];

    /** @var string */
    protected $template;

    /** @var IExportEntity */
    protected $exportEntity;

    const DOC_TYPE = 'doc';
    const PDF_TYPE = 'pdf';

    public function init()
    {
        Yii::setAlias('@page_export', __DIR__ . '/views/');
        if (!$this->template) {
            throw new LogicException('template is required');
        }

        if (!$this->format) {
            throw new LogicException('format is required');
        }

        if (!isset($this->getExportEntities()[$this->format])) {
            throw new LogicException('format is not supported');
        }

        $this->exportEntity = $this->getExportEntities()[$this->format];
    }

    public function setFormat(string $value)
    {
        $this->format = $value;
    }

    public function setFileName(string $value)
    {
        $this->fileName = $value;
    }

    public function setTemplate(string $value)
    {
        $this->template = $value . '.php';
    }

    public function setParams(array $value)
    {
        $this->params = $value;
    }

    /**
     * @return array
     */
    protected function getExportEntities(): array
    {
        return [
            self::DOC_TYPE => new DocExportEntity(),
            self::PDF_TYPE => new PdfExportEntity()
        ];
    }

    public function export()
    {
        $this->exportEntity->export($this->fileName, $this->getRenderedFile());
    }

    /**
     * @return string
     */
    protected function getRenderedFile(): string
    {
        $content = Yii::$app->view->renderFile($this->template, $this->params);
        $wholeText = Yii::$app->view->renderFile(Yii::getAlias('@page_export/_default_template.php'), [
            'content' => $content
        ]);
        return preg_replace("/\s{2,}/", '', $wholeText);
    }
}
