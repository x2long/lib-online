<?php
$EBUPT_WEB_BASE_DIR = realpath(dirname(__FILE__).'/../../');
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
	'name'=>'ebupt',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>$import_config,

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'gii',
			'ipFilters' => array('127.0.0.1', '10.1.71.*', 'my.kaoqin.com'),
		),
	),

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
        'ckeditor' => $components_config['ckeditor'],
		'errorHandler'=>$components_config['errorHandler'],
		'log'=>$components_config['log'],
		'messenger' => $components_config['messenger'],
	),
	
	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
			'adminEmail'=>'username_email',
			'adminName' =>'',
			'batch' => '4',

	),
	'params'=> array_merge($mail_config),
);
