<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class FormModel extends CFormModel
{
    public $pagination;//分页相关的数据
    public $pagesize;//页面大小
    public $totalNum; //所有的记录数
    public $currentPage; //当前的页码
    public $totalPage; //所有的页数
    public $error;//错误信息
    public $logname;//日志中操作的名称
    public $logid;//日志id
	public $page_selector; // 页面选择index or recommend?
    public $tab_selector; // 标签页选择
    public $upUrl;//上一级的URL包括上一级的参数，通过$_SERVER['HTTP_REFERER']得到
    public $params;//一些零时的数据

	public function init(){
		if(isset(Yii::app()->user->id) && Yii::app()->user->id) {
		//还没想好，先空着
		}
	}
	
	/**
	 * auto trim all
	 */
	public function setAttributes($values,$safeOnly=true)
	{
		if (is_array($values)) {
			foreach ($values as $name => $val) {
				if (is_string($val)) {
					$values[$name] = trim($val);
				}
			}
		}
		return parent::setAttributes($values,$safeOnly);
	}

	/**
	 * auto trim all
	 */
	public function beforeValidate()
	{
		if (is_array($this->attributes)) {
			foreach ($this->attributes as $name => $val) {
				if (is_string($val)) {
					$this->$name = trim($val);
				}
			}
		}
		return parent::beforeValidate();
	}

    /**
     * 设置分页的具体数据，逻辑后续实现
     * @access public
     * 主要是更新$this->pagination     *
     */
    public function pageSet(){
        if($this->pagesize < $this->totalNum){
            $totalPage=ceil($this->totalNum/$this->pagesize);
            $this->totalPage=$totalPage;
            $pagination=array();
            if($totalPage>5){
                $pagination["previous"]= ($this->currentPage<2) ? 1 :($this->currentPage-1);
                $infinum = floor(($this->currentPage-1)/5)*5;
                $pagination['first']= ($infinum<2) ? null : 1;
                $pagination['infimum']=$infinum;
                $pagination['ltomission']= ($infinum<2) ? null : true;
                for($i=0;$i<5 && $infinum+$i<$totalPage;$i++){
                    $pagination['pageNums'][$i]=$infinum+$i+1;;
                }
                $pagination['gtomission']=($infinum+6<$totalPage) ? true : null;
                $pagination['last']= ($infinum+6<$totalPage) ? $totalPage : null ;
                $pagination['next']= ($this->currentPage>=$totalPage) ? $totalPage : ($this->currentPage+1);
                $this->pagination = $pagination;
            }else{
                $pagination["previous"]= ($this->currentPage<2) ? 1 :($this->currentPage-1);
                $pagination['first']=null;
                $pagination['infimum']=null;
                $pagination['ltomission']=null;
                for($i=0;$i<$totalPage;$i++){
                    $pagination['pageNums'][]=($i+1);
                }
                $pagination['gtomission']=null;
                $pagination['last']=null;
                $pagination['next']= ($this->currentPage>=$totalPage) ? $totalPage : ($this->currentPage+1);
                $this->pagination=$pagination;
            }
        }else{
            $this->pagination=null;
        }
        return "test kuai haole";
    }

	/**
	 * 邮件发送函数
	 * @access public
     * @param string - $settingName
     * @param string - $TO
     * @param string - $CC
     * @param int - $setPage
	 */
	public function sendMail($settingName = 'reminderStaff', $TO = null, $CC = null ,$setPage=1) {
		$mailSettings = Yii::app()->params['MAIL_SETTINGS'];
		if(!isset($mailSettings[$settingName])){
			echo "mail setting for $settingName not found!";
			die();
		}		
		$mailSetting = $mailSettings[$settingName];
		
		if(isset($mailSetting['disabled']) && $mailSetting['disabled'] == true){
			return true;
		}

		$attachments = array();		
		if (isset($mailSetting['attachments']) && $setPage == 1) {
			foreach($mailSetting['attachments'] as $key=>$value) {
				$attachments[]=$value;
			}
		}
		
		if($TO) { //如果指定收件人
			$to = $TO;
		} else {
			$to = $mailSetting['to'];
		}
		
		if($CC) { //如果指定抄送人
			$cc = $CC;
		} else {
			$cc = $mailSetting['cc'];
		}

		if( isset($mailSetting['vipcc'])
				&& strlen($mailSetting['vipcc']) > 0){
			$vipcc = $mailSetting['vipcc'];
			$cc = $cc . "," . $vipcc;
		}	
		
		$subject = $mailSetting['subject'];
		$from = Yii::app()->params['MAIL_FROM']?Yii::app()->params['MAIL_FROM']:'kaoqin@ebupt.com';
		$name = Yii::app()->params['MAIL_FROM_NAME']?Yii::app()->params['MAIL_FROM_NAME']:'EB图书馆';
		$html = true;
		
		$data = array(
			'model' => $this,
		);
		$ret = Yii::app()->messenger->sendSmartyMail($to, $cc, $subject, $mailSetting['template'], $data, $from, $name, $html, $attachments);
		
		if ($ret == false) {
			$this->addError('email', '发送邮件失败');
			return false;
		} else {
			Yii::log('发送邮件成功， subject: ' . $subject, 'info');
		}
		return true;
	}
	
	/**
	 *
	 *@param array 要转码的字符数组
	 *@return array() 转码后的字符数组
	 */
	public function iconv_array($array) {
		foreach($array as &$value) {
			$temp = $value;
			try {
				$value = iconv('UTF-8','GBK//ignore',$value);
			} catch(Exception $e) {
				$value = $temp;
			}
		}
		return $array;
	}
	
    /**
     *错误处理
     */
    public function error()
    {
        $temp=array_values($this->errors);
        echo "<h1>".$temp[0][0]."</h1>";
    }
	
	/**
	 * 判断字符串是否是utf8编码
	 */
	// Returns true if $string is valid UTF-8 and false otherwise. 
	function is_utf8($word) 
	{ 
		if (preg_match("/^([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}/",$word) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}$/",$word) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){2,}/",$word) == true) 
		{ 
			return true; 
		} 
		else 
		{ 
			return false; 
		} 
	} // function is_utf8 
	
	/**
	 * 对字符串进行处理，如果不是utf8编码，转成utf8编码
	 */
	public function string_detect_format($string)
	{
		if($this->is_utf8($string)) {
			return $string;
		} else {
			return iconv('GBK','UTF-8//ignore', $string);  //GBK转成utf8编码
		}
	}
}
