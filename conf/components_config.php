<?php
return array(
        'user'=>array(
            // enable cookie-based authentication
            'allowAutoLogin'=>true,
            'stateKeyPrefix' => 'ebupt_lib-online_id_',
            'loginUrl' => '/index.php',
        ),
        'statePersister' => array(
            'stateFile' => $EBUPT_WEB_BASE_DIR.'/protected/runtime/state.bin',
        ),
        'securityManager' => array(
            'validationKey' => 'ebupt_validate_key@#>2012<#@ebupt.com'
        ),
        // uncomment the following to enable URLs in path-format
        'urlManager'=>array(
            'urlFormat'=>'path',
            'rules'=>array(
                '<controller:\w+>/<id:\d+>'=>'<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
            ),
            'showScriptName'=>false,
        ),
        'authManager'=>array(
            'class'=>'CDbAuthManager',
            'connectionID'=>'db',
        ),

        'smarty' => array(
            'class'=>'lib.extensions.CSmarty',
            'templatePath' => $EBUPT_WEB_BASE_DIR.'/view',
        ),

         'simhtmldom' => array(
             'class'=>'lib.extensions.CSimpleHtmlDom',
         ),

        'ckeditor'=>array(
             'class' => 'lib.extensions.CCkeditor',
            'basePath' => $EBUPT_WEB_BASE_DIR.'/lib/thirdparty/ckeditor/',
            'returnOutput' => true,
        ),

        'errorHandler'=>array(
            // use 'site/error' action to display errors
            'errorAction'=>'/',
        ),

        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
/* 				array(
					'class'=>'CWebLogRoute',  
					'levels'=>'profile, trace, info, error, warning',
				 ),
				array(
					'class' => 'CProfileLogRoute',
					'levels' => 'profile',
				), */
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error, warning',
                    'categories' => 'system',
                    'logPath' => $EBUPT_WEB_BASE_DIR.'/log',
                    'logFile' => 'kaoqin.system.wf.log',
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'profile,trace,error',
                    'categories' => 'application',
                    'logPath' => $EBUPT_WEB_BASE_DIR.'/log',
                    'logFile' => 'kaoqin.wf.log',
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'info',
                    'categories' => 'application',
                    'logPath' => $EBUPT_WEB_BASE_DIR.'/log',
                    'logFile' => 'lib-online.log',
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'debug, trace',
                    'categories' => 'application.*',
                    'logPath' => $EBUPT_WEB_BASE_DIR.'/log',
                    'logFile' => 'kaoqin.debug.log',
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'debug, trace',
                    'categories' => 'dto',
                    'logPath' => $EBUPT_WEB_BASE_DIR.'/log',
                    'logFile' => 'lib-online.dto.log',
                ),
            ),
        ),
		
		'messenger' => array(
			'class' => 'lib.common.EbuptMessenger',
			'mailer' => 'smtp',
			'options' => array(
				'auth' => true,
				'host' => 'smtp.exmail.qq.com',
				'username' => 'username',
				'password' => 'password',
			),
		),
);
