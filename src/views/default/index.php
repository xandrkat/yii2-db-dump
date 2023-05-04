<?php
use yii\helpers\Html;
use yii\grid\GridView;

$this->params ['breadcrumbs'][] = 'Резервные копии базы';?>
<div class="backup-default-index">
<?=Html::a('<i class="glyphicon glyphicon-plus"></i>  Создать', ['create'], ['class' => 'btn btn-success create-backup'])?>
    <div class="row">
        <div class="col-md-12">
            <?=GridView::widget([
                'id' => 'install-grid',
                'dataProvider' => $dataProvider,
                'columns' => [
                    'name',
                    'size:size',
                    'create_time',
                    'modified_time:relativeTime',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{restore} {delete}',
                        'buttons' => [
                            'restore' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-import"></span>', ['restore','file' => $model['name']], ['title' => 'Восстановить']);
                            },
                            'delete' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete','file' => $model['name']],['title' => 'Удалить']);
                            }
                        ],
                    ],
                ],
            ]);?>
        </div>
    </div>
</div>