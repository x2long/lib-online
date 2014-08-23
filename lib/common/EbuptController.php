<?php
/**
 * class EbuptController file.
 * 
 * @package lib.common
 * @author xuxiaolong <xuxiaolong@ebupt.com>
 */

/**
 * EbuptController is the customized base controller class.
 * All controller classes for domob application should extend from this base class.
 */
class EbuptController extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='';
	public $smartyLayout='';
	
	public $breadcrumbs = array();
	
	public function init() {
		header("Content-Type: text/html; charset=utf-8"); 
	}
	
	/**
     * 渲染一个smarty模板
     *
     * 使用smarty替换Yii本身自带的view层
     *
     * @param string $view 模板文件名
     * @param array $data 模板变量
     * @param boolean $return 是否显示结果，或者返回结果字符串
     * @return string 渲染结果. 如果不需要则返回null
     */
	public function renderSmarty($view, $data=null, $return=false) {
		$smarty = Yii::app()->smarty;  // 这里，就会用到main.php中配置的components.smarty
		if (!is_array($data)) {
			$data = array();
		}
		$data['user'] = Yii::app()->user;

        $data['base_url'] = Yii::app()->request->baseUrl;

		if ($this->layout != '') {
			$data['__view__'] = $view;
			$view = $this->layout;
		}
		$smarty->assign($data);
		if ($return) {
			return $smarty->fetch($view);
		}
		else {
			$smarty->display($view);
			return null;
		}
	}
    
    /**
     * 检查必要的 $_GET 参数
     *
     * 在参数缺失时，如果是ajax请求，返回一个json串并结束程序。否则抛出 CHttpException 404
     *
     * @param array $args 必要的GET参数名称
     * @param boolean $ajax 是否是ajax请求
     */
    public function requireGet($args, $ajax=false) {
        if (is_string($args)) {
        	$args = explode(',', $args);
        }
        foreach ($args as $arg) {
        	$arg = trim($arg);
            if (isset($_GET[$arg])) {
                continue;
            }
            if ($ajax) {
                $ret = array('success'=>false, 'message'=>'参数错误,丢失了必须的参数：'.$arg);
                $this->renderJson($ret);
                Yii::app()->end();
            }
            else {
                throw new CHttpException(404, '参数错误,丢失了必须的参数：'.$arg);
            }
        }
    }

    /**
     * 渲染ajaxModel
     *
     * @param CModel $model
     */
    public function renderAjaxModel($model) {
        $success = !$model->hasErrors();
        if (isset($model->message)) {
        	$message = $model->message;
        	$data = array(
            	'success' => $success,
			 	'message' => $message,
            	'data' => $model->data,
	        );
        }
        else {
        	$data = array(
            	'success' => $success,
			 	'messages' => $model->errors,
            	'data' => $model->data,
	        );
        }
        $this->renderJson($data);
    }

    /**
     * @param mixed 要被返回的json数据，用于ajax调用
     */
    public function renderJson($data) {
        print(CJSON::encode($data));
    }

    /**
     * 增加基于登录的访问控制过滤器
     */
 	public function filters() {
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

    /**
     * 访问控制
     *
     * 默认情况下，每个页面只允许登录用户访问
     */
    public function accessRules() {
        return array(
            array('allow',
                'users' => array('@'),
            ),
            array('deny',
                'users' => array('*'),
            ),
        );
    }

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError(){
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->renderSmarty('system/500.htm', $error);
		}
	}

    public function searInfoByUrl($url){
        $info =null;
        $result = $this->actionCurl($url);
        $html =Yii::app()->simhtmldom;
        $html->load($result);
        $intro =$html->find('.intro',0);//->innertext;
        if(empty($intro)){
            $info['description'] ="";
        }else{
            $p=$intro->children(0);
            $info['description'] =$p->innertext;
        }
        $info['book_name']=$html->find('span[property=v:itemreviewed]',0)->innertext;
        //$span_a=$html->find('div#info span a',0)->innertext;
        $meta =$html->find('meta[name=keywords]',0);
        $meta= $meta->attr['content'];
        $meta =explode(",",$meta);
        $info['author']=$meta[1];
        //$info['span_a']=$span_a;
        $result=preg_replace('/<a[^>]*?>/',"",$result);
        $result=preg_replace('/<\/a>/',"",$result);
        $html->load($result);
        $info['score']=$html->find('strong',0)->innertext;
        $div_book_about=$html->find('div#info',0)->innertext;
        $html->clear();
        $info['isbn'] = $this->findContentFromString("ISBN",$div_book_about,false);
        $info['version']="不详";
        $publisher=$this->findContentFromString("出版社",$div_book_about);  //$book["出版社"];
        //$publisher=preg_replace('/-/',"&",$publisher);
        $info['publisher']=$publisher;
        $info['price']=$this->findContentFromString("定价",$div_book_about); //$book["定价"];
        $info['pages']=$this->findContentFromString("页数",$div_book_about); //$book["页数"];
        $info['publish_date']=$this->findContentFromString("出版年",$div_book_about); //$book["页数"];
        $Book_img_urls=$this->searchUrlsByIsbn($info['isbn']);
        $info['book_url']= $Book_img_urls['book_url'];
        $info['image_url']= $Book_img_urls['img_url'];
        return $info;
    }

    public function searchUrlsByIsbn($isbn){
        $Book_img_urls=array();
        $search_url="http://book.douban.com/subject_search?search_text=".$isbn."&cat=1001";
        $result = $this->actionCurl($search_url);
        $html =Yii::app()->simhtmldom;
        $html->load($result);
        $a=$html->find('div.pic a',0);//->innertext;
        $Book_img_urls['book_url']=$a->href;
        $Book_img_urls['img_url']=$a->children(0)->src;
        $html->clear();
        return $Book_img_urls;
    }

    public function actionCurl($url){
        $curl = curl_init();
        //设置URL参数
        curl_setopt($curl,CURLOPT_URL,$url);
        //要求CURL返回数据
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        //执行请求
        $result = curl_exec($curl);
        //close curl
        curl_close($curl);
        return $result;
    }

    public function preTreatment($ele,$iskey=true){
        if($iskey){
            $ele=preg_replace('/:/',"",$ele);
        }
        $ele=trim(strip_tags($ele));
        return $ele;
    }
    public function findContentFromString($content,$string,$flag=true){
		if($flag){
			$pattern1="/<span\s*class=\"pl\">\s*".$content."\s*:\s*<\/span>\s*(.*?)\s*</i";
		}else{
			$pattern1="/<span\s*class=\"pl\">\s*".$content."\s*:\s*<\/span>\s*(\d{13})\s*</i";
		}
        preg_match_all($pattern1,$string,$result);
        if(is_array($result[1]) && (!empty($result[1]))){
            return $result[1][0];
        }else{
         return "Not Found";
        }
    }
}
