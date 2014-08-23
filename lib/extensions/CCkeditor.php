<?php
/**
 * Created by JetBrains PhpStorm.
 * User: xuxiaolong
 * Date: 13-7-28
 * Time: 上午10:06
 * To change this template use File | Settings | File Templates.
 */
require_once(dirname(__FILE__).'/../thirdparty/ckeditor/class/ckeditor.php');

class CCkeditor extends CKEditor{
    public function __construct() {
        parent::__construct();
    }

    public function init() { }
}