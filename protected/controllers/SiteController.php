<?php

class SiteController extends EbuptController
{
	public $model ;
	public $layout = '';

	//public 	$defaultAction = 'login';

	public function filters() {
		return array();
	}
	
	/**
	 * 网站主页
	 *
	 */
    public function actionIndex(){
        $this->redirect(Yii::app()->getBaseUrl().'/library');
    }
	public function actionIndex1() {
		//if (Yii::app()->user->isGuest) { http://book.douban.com/subject/1856285/
        //    $this->redirect("/site/login");
        //} else {
        //初始化curl
        $curl = curl_init();
        //设置URL参数
        curl_setopt($curl,CURLOPT_URL,"http://book.douban.com/subject/6847546/");
        //要求CURL返回数据
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        //执行请求
        $result = curl_exec($curl);
        //close curl
        curl_close($curl);

        $result=preg_replace('/<a[^>]*?>/',"",$result);
        $result=preg_replace('/<\/a>/',"",$result);

        $html =Yii::app()->simhtmldom;
        $html->load($result);
        $div=$html->find('div#info',0)->innertext;
        $pattern="/<span class=\"pl\">(.*?)<\/span>(.*?)<br\/>/i";
        preg_match_all($pattern,$div,$matches);
        $length=count($matches[1]);
        $book=array();
        for($i=0;$i<$length;$i++){
            $key=$this->preTreatment($matches[1][$i]);
            $value=$this->preTreatment($matches[2][$i]);
            $book[$key]=$value;
        }
        var_dump($book);
        $page = 6; // get the requested page
        $limit = 20; // get how many rows we want to have into the grid
        $start = $limit*$page - $limit; // do not put $limit*($page - 1)
        if ($start<0) {
            $start = 0;
        }
        $attribute = array(
            'limit' => $limit,
            'offset' => $start,
        );
        $criteria = BookRecord::model()->createCriteria($attribute);
        $model = BookRecord::model()->findAll($criteria,"");
        $nevar = BookRecord::model()->find("book_id='9115'");
        //var_dump($nevar);
		$this->renderSmarty('index.html', array('book' => $model,'html'=>$book));
        //$this->renderSmarty('detail.html', array('book' => $nevar));
        $html->clear();
	}
	
	/**
	 *
	 */
	public function actionTest()
	{
//		$model = new FormModel();
//		$this->renderSmarty('demo.htm', array('model' => $model));
        $book_id = $_GET["bookid"];
        $nevar = BookRecord::model()->find("book_id='".$book_id."'");
        $this->renderSmarty('detail.html', array('book' => $nevar));
	}
	
	/**
	 * Displays the login page
	 */
	public function actionLogin() {
		if (!Yii::app()->user->isGuest) {
            $this->redirect(Yii::app()->user->defaultUrl);
        } 
		
		$model = new LoginForm();
		$model->errorInfo = "";
		
		if(isset($_POST["username"])) {
			$model->attributes = $_POST;
			// validate user input and redirect to the previous page if valid
			if ($model->validate() && $model->getAdminId())	{
				// 按以下优先级决定检查地址：
  				// 1.如果Yii::app()->user的returnUrl不是登录页面，使用它；
				// 2.如果以上都不符合，则按照用户的权限决定其登录以后的页面。
				if (Yii::app()->user->returnUrl != Yii::app()->user->loginUrl) {
					$nextUrl = Yii::app()->user->returnUrl;
				}
				else {
					//$nextUrl = Yii::app()->user->defaultUrl;
					$nextUrl = "/";
				}

				// 重定向到上面决定的页面
				$this->redirect($nextUrl);
			}
			else {
				// 记录登录错误的次数(into $_SESSION)
				$errCount = Yii::app()->user->getState('loginError');
				++$errCount;
				Yii::app()->user->setState('loginError', $errCount);
				$model->errorInfo = "您输入的用户名或密码错误！";
			}
		} else {
			$model->errorInfo = "";
		}
	
		// display the login form 
		$this->renderSmarty('login.htm', array('model' => $model));
	}

	/**
	 * 管理员退出并注销session
	 *
	 */
	public function actionLogout() {
		Yii::app()->user->logout();//与Yii::app()->user->logout函数相对，注销session
		$this->redirect('/site/login');
	}

    public function preTreatment($ele,$iskey=true){
        if($iskey){
            $ele=preg_replace('/:/',"",$ele);
        }
        $ele=trim(strip_tags($ele));
        return $ele;
    }

    public function actionRelax(){
        $slideShow = array();
        $Filetype = array("bmp", "jpg", "png", "gif", "tiff", "pcx", "tga", "exif", "fpx", "svg", "psd", "cdr", "pcd", "dxf", "ufo", "eps", "ai", "raw");

        $dir = Yii::getPathOfAlias('webroot')."/images/imageslide/rnd/";
        if (is_dir($dir)) {
            $dh = opendir($dir);
            while(($fileName = readdir($dh))!==false){
                if ($fileName!="." && $fileName!="..") {
                    $ns = explode('.', $fileName);
                    $imageFormat = strtolower($ns[1]);
                    if(in_array($imageFormat,$Filetype)) { //如果格式合法
                        $slideShow[] = $fileName;
                    }
                }
            }
            closedir($dh);
        }
        $sequence = rand(1, 7);
        $this->renderSmarty("secret/index".$sequence.".html", array('model' => "hello"));
    }

    public function actionGetHello() {
        $hello = ',';
        $hour= date('G');
        if ($hour<11)
            $hello .= '早上好';
        else if ($hour<13)
            $hello .= '中午好';
        else if ($hour<18)
            $hello .= '下午好';
        else
            $hello .= '晚上好';
        return $this->renderJson($hello);
		}

		public function actionGetNotices(){
				$days =array('一','二','三','四','五','六','日');
				$notices =array();
				$notices []="(1/2)&nbsp;EB图书馆欢迎您，今天是".date("Y年n月j日 星期").$days[date('N')-1];
				$notices []=";&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(2/2)为了获得更好的观赏，建议使用非IE内核浏览器，如ff，chrome等";
				return $this->renderJson($notices);
		}
}
