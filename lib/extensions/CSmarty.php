<?php
/**
 * file CSmarty.php
 *
 * @package lib
 */

require_once(dirname(__FILE__).'/../thirdparty/Smarty-2.6.26/libs/Smarty.class.php');

//smarty setting
define('SMARTY_LIFTTIME', 1800);
define('SMARTY_DLEFT', '{/');
define('SMARTY_DRIGHT', '/}');

class CSmarty extends Smarty
{
    private $_baseDir;
    public $templatePath;

    public function init() {
        $this->compile_check = true;
        $this->caching = 0;
        $this->left_delimiter  =  SMARTY_DLEFT;
        $this->right_delimiter =  SMARTY_DRIGHT;
        $this->cache_lifetime = SMARTY_LIFTTIME;
        $this->initTemplatePath($this->templatePath);
    }

    public function initTemplatePath($value) {
        $this->_baseDir = $value;
        $this->template_dir = $this->_baseDir;//SMARTY_TMPDIR;
        $this->compile_dir = $this->_baseDir.'/smarty/template_c/';
        $this->config_dir = $this->_baseDir.'/tpl_conf/';
        $this->cache_dir = $this->_baseDir.'/smarty/cache/';
    }
}


