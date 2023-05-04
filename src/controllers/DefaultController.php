<?php
namespace xandrkat\DbDump\controllers;

use Yii;
use yii\data\ArrayDataProvider;
use xandrkat\DbDump\components\DumpDb;

class DefaultController extends \yii\web\Controller{
    
    public $model;
    public $path;
    
    public function init(){
        if (isset($this->module->path))
            $this->path = Yii::getAlias($this->module->path);
        else
            $this->path = Yii::$app->basePath . '/_backup_db/';
        if (!file_exists($this->path)) {
            mkdir($this->path);
            chmod($this->path, '777');
        }
        
        $this->model= new DumpDb(['path'=>$this->path]);
    }
    
    public function actionIndex() {
        
        $path = $this->model->path;
        $dataArray = array();
        $list_files = glob($path . '*.sql');
        if ($list_files) {
            $list = array_map('basename', $list_files);
            sort($list);
            foreach ($list as $id => $filename) {
                $columns = array();
                $columns['id'] = $id;
                $columns['name'] = basename($filename);
                $columns['size'] = filesize($path . $filename);
                $columns['create_time'] = date('Y-m-d H:i:s', filectime($path . $filename));
                $columns['modified_time'] = date('Y-m-d H:i:s', filemtime($path . $filename));
                $dataArray[] = $columns;
            }
        }
        \yii\helpers\ArrayHelper::multisort($dataArray,['create_time'],[SORT_DESC]);
        $dataProvider = new ArrayDataProvider(['allModels' => $dataArray]);
        return $this->render('index',['dataProvider' => $dataProvider]);
    }
    
    public function actionDelete($file) {
        $sqlFile = $this->model->path . basename($file);
        if (file_exists($sqlFile)) {
            unlink($sqlFile);
            $flashError = 'success';
            $flashMsg = 'Файл ' . $sqlFile . ' был успешно удален.';
        } else {
            $flashError = 'error';
            $flashMsg = 'Файл ' . $sqlFile . ' не был найден.';
        }
        \Yii::$app->getSession()->setFlash($flashError, $flashMsg);
        $this->redirect(['index']);
    }
    
    public function actionRestore($file) {
        $sqlFile = $this->model->path . basename($file);
        $message=$this->execSqlFile($sqlFile);
        if($message!='ok')
            Yii::$app->getSession()->setFlash('error', $message);
        else
            Yii::$app->getSession()->setFlash('success', 'База обновленна');
        $this->redirect(array('index'));
    }
    
    public function actionCreate(){
        if(!$this->model->StartBackup()){
            Yii::$app->getSession()->setFlash('error', 'Ошибка при создании файла.');
            return $this->redirect('index');
        }
        $tables = $this->model->getTables();
        foreach ($tables as $tableName) {
            $this->model->getColumns($tableName);
            $this->model->getData($tableName);
        }
        $this->model->EndBackup();
        Yii::$app->getSession()->setFlash('success', 'Резервая копия базы была создана!!!');
        $this->redirect(array('index'));
    }
    
}