<?php

$defaultCc = '';
$defaultVipCc = '';

return array(
		'BATCH'=>'5',
	'LARGE_WITHDRAW_AMOUNT' => 5000,
	'MAIL_FROM' => 'admin_username',
	'MAIL_FROM_NAME' => 'EB图书馆',
	'REMIND_SETTINGS' => array (
		'remindToGetInvoice' => array(
			'pendingTime' => 7 * 24 * 3600,
		),		
	),
	'MAIL_SETTINGS' => array (
		'remindReverser' => array(
     		//'disabled' => false,
			'to'=>'test_user_name',
			'cc'=>$defaultCc,
			'vipcc'=>$defaultVipCc,
			'subject'=>'预约告知',
			'template'=>'remindReverser.htm',
			//'attachments'=>array(),
		),
		'resetPassword' => array(
     		//'disabled' => false,
			'to'=>'test_user_name',
			'cc'=>$defaultCc,
			'vipcc'=>$defaultVipCc,
			'subject'=>'title',
			'template'=>'resetPassword.htm',
			//'attachments'=>array(),
		),
	),
);
