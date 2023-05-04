<?php
namespace xandrkat\DbDump\controllers;

use Yii;
use xandrkat\DbDump\components\DumpDb;

class ConsoleController extends \yii\console\Controller{
    
    public $model;
    public $path;
    
    public function init(){
        if (isset($this->module->path))
            $this->path = Yii::getAlias($this->module->path);
        else
            $this->path = Yii::getAlias('@vendor/../_backup_db/');
        if (!file_exists($this->path)) {
            mkdir($this->path);
            chmod($this->path, '777');
        }
        
        $this->model= new DumpDb(['path'=>$this->path]);
    }
    
    public function actionCreate(){
        if(!$this->model->StartBackup()){
            $this->stderr('Ошибка при создании файла.');
            return 1;
        }
        $tables = $this->model->getTables();
        foreach ($tables as $tableName) {
            $this->model->getColumns($tableName);
            $this->model->getData($tableName);
        }
        $this->model->EndBackup();
        $this->stdout('Резервая копия базы была создана!!!');
        return 0;
    }
    
}