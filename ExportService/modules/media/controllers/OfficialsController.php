<?php

namespace frontend\modules\media\controllers;

use frontend\components\services\page_export_service\PageExportAction;
use frontend\components\services\page_export_service\PageExportService;
use Yii;
use frontend\components\Controller;
use common\models\officials\OfficialsSearch;
use common\models\officials\Officials;
use yii\web\NotFoundHttpException;

class OfficialsController extends Controller
{
    ...

    /**
     * @param string $type
     * @param string|null $officialId
     */
    public function actionPageExport(string $type, string $officialId = null)
    {
        $searchModel = new OfficialsSearch();
        if ($officialId) {
            $searchModel->ID = (int)$officialId;
        }
        $dataProvider = $searchModel->search([]);
        $dataProvider->query->visible();
        $officials = $dataProvider->getModels();

        $title = 'Департамент финансов в лицах';
        $pageExportService = new PageExportService([
            'template' => Yii::getAlias('@frontend/modules/media/views/officials/export-list'),
            'format' => $type,
            'fileName' => $title,
            'params' => [
                'officials' => $officials,
                'title' => $title
            ]
        ]);
        $pageExportService->export();
    }
}