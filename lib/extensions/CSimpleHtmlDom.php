<?php
/**
 * Created by JetBrains PhpStorm.
 * User: AlbertLy
 * Date: 13-5-8
 * Time: 下午2:18
 * To change this template use File | Settings | File Templates.
 */

require_once(dirname(__FILE__).'/../thirdparty/Simplehtmldom-1.5/simple_html_dom.php');

class CSimpleHtmlDom extends simple_html_dom{
    public function __construct() {
        parent::__construct();
    }

    public function init() { }

}