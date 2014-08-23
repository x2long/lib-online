<?php
/**
 * class EbuptMessenger file.
 *
 * @package lib.common
 * @author xuxiaolong <xuxiaolong@ebupt.com>
 */

//Yii::import('Pear.Mail', true);
//Yii::import('Pear.Mail.mime', true);
require_once('Mail.php');
require_once('Mail/mime.php');

/**
 * class EbuptMessenger.
 * 
 * 发送邮件,短信
 * 
 * 支持html格式和纯文本格式；
 * 支持使用应用对应的smarty模版生成邮件内容；
 * 支持发送附件
 * 
 * 所有的邮件内容，变量使用utf-8编码
 */
class EbuptMessenger
{
	public $mailer;
	public $options;

    public function init() {
	    if (empty($this->mailer)) {
			$this->mailer = 'mail';
		}
    }

    /**
     * 发送使用smarty模板的邮件
     *
     * 使用系统配置的smarty模板路径下的 mail/$template.html作为模板文件
     *
     * @param string $to 逗号分隔的收件人列表
     * @param string $subject 邮件的主题
     * @param string $template 模版名称
     * @param array $data 需要替换的模版变量数组($template_var => $template_value)
     * @param string $from 发件人的email
     * @param string $name 发件人的显示名
     * @param boolean $html 邮件是否是html格式，默认为true
     * @param array $attachments 附件文件路径列表，默认为空
     */
    public function sendSmartyMail($to, $cc, $subject, $template, $data, $from, $name, $html=true, $attachments=array()) {
        if (!isset($to) || ($to == "")) {
            return;
        }
        if (!isset($cc) || ($cc == "")) {
        	$cc = '';
        }
        
        // 可以配置将所有邮件都发到一个邮件地址或列表
        // 用于测试时，避免发出垃圾邮件骚扰大家
        $sendAllMailTo = Yii::app()->params['DEBUG_SEND_ALL_MAIL_TO'];
        if (!empty($sendAllMailTo)) {
            $to = $sendAllMailTo;
            $cc = $sendAllMailTo;
        }

        $smarty = Yii::app()->smarty;
        $smarty->assign($data);
        $content = $smarty->fetch('mail/'.$template);


        $hdrs = array(
                'Return-Path' => $from,
                'From'    => "$name <$from>",
                'To'    => $to,
                'Subject' => "=?UTF-8?B?".base64_encode($subject)."?=",
                'Date' => date("r"),
        );
        if($cc != ""){
            $ccHeader = array('Cc' => $cc);
            $hdrs = array_merge($hdrs, $ccHeader);
        }
        $param = array(
                'text_charset' => 'utf-8',
                'head_charset' => 'utf-8',
                'html_charset' => 'utf-8',
        );


        $mime = new Mail_mime();
        if ($html){
            $mime->setHtmlBody($content);
        }
        else{
            $mime->setTXTBody($content);
        }
        foreach ($attachments as $att) {
            $mime->addAttachment($att);
        }
        $body = @$mime->get($param);
        $hdrs = @$mime->headers($hdrs);

       // $email = & Mail::factory($this->mailer, $this->options);
        $email = @ Mail::factory($this->mailer, $this->options);
        
		/** 
		 * 2011年3月的Net_SMTP有个bug，对timeout的处理有问题，而PEAR的Mail_smtp类并没有正确的初始化Net_SMTP
         * 导致Net_SMTP对timeout的bug传递出来。如果在smtp对象上调用connect方法后(getSMTPObject以后),
		 * 再调用setTimeout(新版Net_SMTP才有)，则问题可以绕过。
		 * 该问题只在PEAR Net_SMTP v1.5.0和v1.5.1上存在
		 * see https://pear.php.net/bugs/bug.php?id=18335&edit=12&patch=Net_SMTP-bug18335.diff&revision=latest
		 */
        $result = &$email->getSMTPObject();
        if (@PEAR::isError($result)) {
			Yii::log('初始化SMTP对象失败：'.$result->getMessage(), 'error');
            return false;
        }
		// workaround Net_SMTP bug
		if (method_exists($result, 'setTimeout')) {
			$result->setTimeout(3600); // 1 hour
		}

		//只有放到recipients里，才会收到邮件。至于显示到To还是Cc由header决定。
        $recipients = isset($hdrs['Cc'])? $hdrs['To'].','.$hdrs['Cc']:$hdrs['To'];
        $ret = $email->send($recipients, $hdrs, $body);
        if (@PEAR::isError($ret)) {
            Yii::log('发送邮件错误:'.$ret->getMessage(), 'error');
            return false;
        } else {
        	Yii::log('发送邮件成功，Subject: ' . $subject, 'info');
        }
        return true;
    }

    /**
     * 发送邮件
     *
     * @param string $to 逗号分隔的收件人列表
     * @param string $subject 邮件的主题
     * @param array $content 邮件内容
     * @param boolean $html 邮件是否是html格式，默认为否
     * @param string $from 发件人的email
     * @param string $name 发件人的显示名
     * @param array $attachments 附件文件路径列表
     */
    public function sendMail($to, $subject, $content, $html=false, $from="xuxiaolong@ebupt.com", $name="domob", $attachments=array()) {
        if (!isset($to) || ($to == "")) {
            return;
        }

        // 可以配置将所有邮件都发到一个邮件地址或列表
        // 用于测试时，避免发出垃圾邮件骚扰大家
        $sendAllMailTo = Yii::app()->params['DEBUG_SEND_ALL_MAIL_TO'];
        if (!empty($sendAllMailTo)) {
            $ori = $to;
            $to = $sendAllMailTo;
            $content  = "原邮件接收人：".$ori."\n 现接收人：".$sendAllMailTo." \n 内容是:\n ".$content;
        }
        $hdrs = array(
                'Return-Path' => $from,
                'From'    => "$name <$from>",
                'Subject' => $subject,
                );
        $param = array(
                'text_charset' => 'UTF-8',
                'head_charset' => 'UTF-8',
                'html_charset' => 'UTF-8',
                );

        $mime = new Mail_mime();
        if ($html) {
            $mime->setHtmlBody($content);
        }
        else {
            $mime->setTXTBody($content);
        }
        foreach ($attachments as $att) {
            $mime->addAttachment($att);
        }
        $body = @$mime->get($param);
        $hdrs = @$mime->headers($hdrs);

        //$email = &Mail::factory($this->mailer, $this->options);
        $email = @Mail::factory($this->mailer, $this->options);
        $ret = $email->send($to, $hdrs, $body);
        if (@PEAR::isError($ret)) {
        	Yii::log('发送邮件错误:'.$ret->getMessage(), 'error');
        	return false;
        }
        return true;
    }

    /**
     * 发送短消息
     *
     * @param mixed $mobileNumber 手机号码，可以是单个，也可以是数组
     * @param string $message 要发送的消息内容
     * @return bool 是否正确执行了gsmsend，但不意味一定发送成功。
     */
    public function sendGsm($mobileNumber, $message, $encode='UTF-8') {
    	throw new Exception('NOT IMPLEMENT YET.');
        if (is_string($mobileNumber)) {
            $mobileNumbers = split(',', $mobileNumber);
        }
        elseif (is_array($mobileNumber)) {
            $mobileNumbers = $mobileNumber;
        }
        else {
            throw Exception('mobileNumber format error. use array or string');
        }

        $serverPort = Yii::app()->params['gsmServerPort'];
        if ($serverPort == '') {
            throw new Exception('没有配置 gsmServerPort');
        }

        if ($encode != 'GBK') {
            //$message = mb_convert_encoding($message, 'gbk', $encode);
            $message = iconv($encode, 'GBK', $message);
        }
        foreach ($mobileNumbers as $mobileNumber) {
            if (preg_match('/^[0-9+]+$/', $mobileNumber) == 0) {
                // skip invalid number
                continue;
            }
            $shell = new System_Command();
            $shell->pushCommand('gsmsend', '-s', $serverPort, "$mobileNumber@$message");
            $shell->execute();
        }
    }
}
