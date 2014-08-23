<?php
$EBUPT_WEB_BASE_DIR = realpath(dirname(__FILE__).'/../../../');
// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
Yii::setPathOfAlias('lib',$EBUPT_WEB_BASE_DIR.'/lib');
$db_config = require_once($EBUPT_WEB_BASE_DIR.'/conf/db_config.php');
$import_config = require_once($EBUPT_WEB_BASE_DIR.'/conf/import_config.php');
$components_config = require_once($EBUPT_WEB_BASE_DIR.'/conf/components_config.php');
$mail_config = require_once($EBUPT_WEB_BASE_DIR.'/conf/mail_config.php');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'admin',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
        'application.models.*',
        'application.components.*',
        'lib.common.*',
        'lib.activeRecord.*',
		'application.modules.srbac.controllers.SBaseController', 
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'gii',
			'ipFilters' => array('127.0.0.1', '192.168.1.*'),
		),
		'srbac' => array( 
		  'userclass'=>'EmployeeRecord', //可选,默认是 User 
		  'userid'=>'EmployeeCode', //可选,默认是 userid 
		  'username'=>'EmployeeName', //可选，默认是 username 
		  'debug'=>true, //暂时调成true。。。 
		  'pageSize'=>10, //可选，默认是 15 
		  'superUser' =>'super', //可选，默认是 Authorizer 
		  'css'=>'srbac.css', //可选，默认是 srbac.css 
		  'layout'=> 
			'application.views.layouts.main', //可选,默认是 
							 // application.views.layouts.main, 必须是一个存在的路径别名 
		  'alwaysAllowed'=>array(    //可选,默认是 gui 
			 'SiteLogin','SiteLogout','SiteIndex','SiteAdmin', 
			 'SiteError', 'SiteContact'), 
		  'userActions'=>array(//可选,默认是空数组 
			 'Show','View','List'), 
		  'listBoxNumberOfLines' => 15, //可选,默认是 10 
		  'imagesPath' => 'srbac.images', //可选,默认是 srbac.images 
		  'imagesPack'=>'noia', //可选,默认是 noia 
		  'iconText'=>true, //可选,默认是 false 
		  'header'=>'srbac.views.authitem.header', //可选,默认是 
								  // srbac.views.authitem.header, 必须是一个存在的路径别名 
		  'footer'=>'srbac.views.authitem.footer', //可选,默认是 
								  // srbac.views.authitem.footer, 必须是一个存在的路径别名 
		  'showHeader'=>true, //可选,默认是 false 
		  'showFooter'=>true, //可选,默认是 false 
		  
		  'alwaysAllowedPath'=>'srbac.components', //可选,默认是 srbac.components 
											// 必须是一个存在的路径别名 
		),
	),

	'language'=>'zh',

	// application components
	'components'=>array(
		'user'=>$components_config['user'],
		'statePersister' => $components_config['statePersister'],
		'securityManager' => $components_config['securityManager'],
		// uncomment the following to enable URLs in path-format
		'urlManager'=>$components_config['urlManager'],
		'db' => $db_config['test'],
		'authManager'=>$components_config['authManager'],
		'smarty' => $components_config['smarty'],
        'simhtmldom' => $components_config['simhtmldom'],
		'errorHandler'=>$components_config['errorHandler'],
		'log'=>$components_config['log'],
		'messenger' => $components_config['messenger'],
	),
	
	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'',
	),
	'params'=> array_merge($mail_config),
);
