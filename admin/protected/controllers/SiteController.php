<?php

class SiteController extends EbuptController
{
	public  $model ;
	public  $layout = '';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', //perform access control for CRUD operations
		);
	}
	
	/**
	 * Specifies the access control rules.
	 * @return array access control rules.
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array(''),
				'users'=>array('*'),
			),
			array('allow',
				'users'=>array('@'),
			),
			array('deny', //deny all users
				'actions'=>array('*'),
			),
		);
	}
	
	/**
	 * 网站主页
	 *
	 */
	public function actionIndex() {
		//$this->redirect("/admin/system/publishNotice");
        $url = "http://".$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"]."/yiieblib/web";
		if (Yii::app()->user->isGuest) {
            $this->redirect($url.'/login');
        }else{
            if(Yii::app()->user->name != "Eblib-admin"){
						  Yii::app()->user->logout();//注销session
              $this->redirect($url.'/login');
						 }
						$model = new AdminLoginForm();
            $model->common_url =Yii::app()->baseUrl."/..";

            $model->action ="http://10.1.70.149:9002/mobile/jiehuan.php";
            $this->renderSmarty('EbAdmin/index.html', array('model' => $model));
        }
	}

    public function actionGetUpdateBookInfo(){
        $bookHelper = new BookHelper();
        $attribute = array();
        $criteria = $bookHelper->createCriteria($attribute);
        $allbooks = $bookHelper->model->findAll($criteria,"");
        foreach( $allbooks as $book){
            if(empty($book->description) || empty($book->image_url)){
                $values=$this->actionSearchByIsbn($book->isbn);
                {
                    if(!empty($values['description']) ){
                        $book->description=$values['description'];
                    }
                    if(!empty($values['image_url']) ){
                        $book->image_url=$values['image_url'];
                    }
                    $book->save();
                }
            }
        }
        //var_dump($this->get_books(1,20,"c++",false)) ;
        //echo "test";
        return $this->renderJson(true);
    }

    private function updateByIsbn($isbn){
        $bookinfo=array();
        $book_img_urls = $this->searchUrlsByIsbn($isbn);
        $bookinfo = $this->searInfoByUrl($book_img_urls['book_url']);
        return $bookinfo;
    }
}
