<?php
/**
 * Created by JetBrains PhpStorm.
 * User: xuxiaolong
 * Date: 13-7-9
 * Time: 上午11:45
 * To change this template use File | Settings | File Templates.
 */
class AboutController extends EbuptController{
    public $model;
    public $layout = '';
    public function filters() {
        return array();
    }

    public function actionIndex(){
        $pageselector='about';
        $this->renderSmarty('about/index.html',array('pageselector'=>$pageselector,'username'=>Yii::app()->user->name));
    }

    public function actionDetail(){
    }
}