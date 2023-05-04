<?php
namespace xandrkat\DbDump;

class Module extends \yii\base\Module{
    
    public $controllerNamespace = 'xandrkat\DbDump\controllers';
    public $path='@vendor/../_backup_db/';
    

    public function init(){
        parent::init();
    }
    
}