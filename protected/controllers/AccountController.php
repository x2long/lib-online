<?php
/**
 * Created by JetBrains PhpStorm.
 * User: xuxiaolong
 * Date: 13-7-9
 * Time: 上午11:51
 * To change this template use File | Settings | File Templates.
 */
class AccountController extends EbuptController{
    const LIMIT_DAYS=10;
    const LIMIT_BOOKS=10;
    public $model;
    public $layout = '';
    public function filters() {
        return array();
    }

    public function actionIndex(){
        if(Yii::app()->user->isGuest){
            $url = "http://".$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"]."/yiieblib/web";
            echo "<script>alert('对不起，您还没有登录，请您登录。如果有问题，请联系管理员(houyihan@ebupt.com)！');window.location.href='".$url."/login"."';</script>";
            return;
        }
        if( isset($_GET["currentPage"]) && $_GET["currentPage"] ){
            $currnetPage = $_GET["currentPage"];
        }else{
            $currnetPage = 1;
        }
        if( isset($_GET["searchContent"]) && $_GET["searchContent"] ){
            $searchContent = $_GET["searchContent"];
        }else{
            $searchContent = "allbooks";
        }
        if (substr_count($searchContent,"cplusplus")>0)
            $searchContent=preg_replace("/cplusplus/i","c++", $searchContent);
        $model = new ReaderLoginForm();
        if (preg_match("/c\+\+/i",$searchContent)){
            $model->contentUrl = preg_replace("/c\+\+/i","cplusplus", $searchContent);
        }else{
            $model->contentUrl= $searchContent;
        }
        $model->content = $searchContent;
        $model->pagesize=16;
        $userid=Yii::app()->user->id;
        $model->totalNum=ReserveHelper::get_all_reserve_book_for_user($searchContent,$userid,0,0,false);
        if($model->totalNum>0){
            $total_pages = ceil($model->totalNum/$model->pagesize);
        }else{
            $total_pages = 0;
        }
        if ($currnetPage > $total_pages) {
            $currnetPage = $total_pages;
        }
        $model->currentPage = $currnetPage;
        //var_dump($model->pageSet());
        $model->pageSet();
        $offset = ($model->pagesize)*$currnetPage - ($model->pagesize);
        if($offset<0) $offset = 0;
        $accessible_books=ReserveHelper::get_all_reserve_book_for_user($searchContent,$userid,$model->pagesize,$offset,true);
        $reserved_books =ReserveHelper::get_reserved_books_by_userid($userid);
        $model->username = Yii::app()->user->name;
        $model->page_selector='account';
        $model->tab_selector='index';
        $reader = ReaderRecord::model()->find("user_id=".Yii::app()->user->id);
        $rankBooks=BookHelper::find_random_book(6);
        $params['rankBooks']=$rankBooks;
        $this->renderSmarty('account/index.html',array('model'=>$model,'reader'=>$reader,'params'=>$params,'accessible_books'=>$accessible_books,'reserved_books'=>$reserved_books));
    }

    public function actionRenew(){
        $model = new ReaderLoginForm();
        $reader = ReaderRecord::model()->find("user_id=".Yii::app()->user->id);
        $borrowHelper=new BorrowHelper();
        $borrowinfo =$borrowHelper->get_mix_info_by_user_id($reader->user_id);
        $descriptions=array();
        foreach($borrowinfo as $key=>$value){
            $description =null;
            $description['base']=mb_substr($value['description'],0,250,'utf-8');
            $description['extra']=mb_substr($value['description'],250,mb_strlen($value['description'],'utf-8')-250,'utf-8');
			$now=strtotime("now");
			$return_time=strtotime($value['return_date']);
			$days=round(($return_time-$now)/3600/24) ;
			if($days< 0){ $description['expired']='notallow';
			}elseif($value['renew_num']==2){ $description['expired']='max';
			}elseif($days>0 && $days<=self::LIMIT_DAYS){ $description['expired']='allow';
			}else{$description['expired']='ohter';}
			$value['description']=$description;
            $descriptions[$key]=$description;
        }
        $model->params=$descriptions;
        //var_dump($descriptions);
        $recommendHelper = new RecommendHelper();
        $model->username = Yii::app()->user->name;
        $model->page_selector='account';
        $model->tab_selector='renew';
        $this->renderSmarty('account/renew.html',array('model'=>$model,'renewBooks'=>$borrowinfo,'reader'=>$reader));
    }

	public function actionRecommend(){
        if( isset($_GET["currentPage"]) && $_GET["currentPage"] ){
            $currnetPage = $_GET["currentPage"];
        }else{
            $currnetPage = 1;
        }
        $model = new ReaderLoginForm();
        $reader = ReaderRecord::model()->find("user_id=".Yii::app()->user->id);
        $recommendHelper = new RecommendHelper();
        $model->pagesize = 10;
        $model->totalNum =$recommendHelper->get_books_by_userid(0,0,Yii::app()->params['BATCH'],$reader->user_id,false);
        if($model->totalNum>0){
            $total_pages = ceil($model->totalNum/$model->pagesize);
        }else{
            $total_pages = 0;
        }
        if ($currnetPage > $total_pages) {
            $currnetPage = $total_pages;
        }
        $model->currentPage = $currnetPage;
        $model->pageSet();
        $offset = ($model->pagesize)*$currnetPage - ($model->pagesize);
        if($offset<0) $offset = 0;
        $recommendBooks =$recommendHelper->get_books_by_userid($model->pagesize,$offset,Yii::app()->params['BATCH'],$reader->user_id,true);
        foreach($recommendBooks as $key=>$value){
            $description['base']=mb_substr($value->description,0,250,'utf-8');
            $description['extra']=mb_substr($value->description,250,mb_strlen($value->description,'utf-8')-250,'utf-8');
            $value->description=$description;
        }
        $model->username = Yii::app()->user->name;
        $model->page_selector ='account';
        $model->tab_selector='recommend';
        $this->renderSmarty('account/recommend.html',array('model'=>$model,'recommendBooks'=>$recommendBooks,'reader'=>$reader));
    }

    public function actionSet(){
        $model = new ReaderLoginForm();
        $reader = ReaderRecord::model()->find("user_id=".Yii::app()->user->id);
        $model->username =$reader->name;
        $model->page_selector='account';
        $model->tab_selector='set';
        $this->renderSmarty('account/set.html',array('model'=>$model,'reader'=>$reader));
    }

    public function actionChangeMotto(){
        $url =Yii::app()->baseUrl;
        if(isset($_POST["my_motto"]) && $_POST['my_motto']){
            $my_motto = $_POST['my_motto'];
            $reader_helper = new ReaderHelper();
            $reader = $reader_helper->find("user_id=".Yii::app()->user->id);
            $reader->user_motto = $my_motto;
            if($reader->save()) {
                echo "<script>alert('恭喜您，座右铭修改成功！更多的精彩期待您。');window.location.href='".$url."/account"."';</script>";
                return;
            }
        }
        $this->redirect($url);
    }

    /**
     * cancle a reserve
     */
    public function actionGetCancelResrve($book_id){
        $record =ReserveRecord::model()->find("book_id=".$book_id);
        $record->delete();
        $htmlstr =$this->format_reserved_books();
        return $this->renderJson($htmlstr);
    }

    /**
     * add a reserve
     */
    public function actionGetAddReserve($book_id){
        $reserved_books =ReserveHelper::get_reserved_books_by_userid(Yii::app()->user->id);
        $num =count($reserved_books);
        if($num>=self::LIMIT_BOOKS){
            $this->renderJson("no");
        }else{
            date_default_timezone_set('Asia/Chongqing');
            $sdate=date("Y/m/d H:i:s");
            $reserve=array(
                'reserve_id'=>'',
                'user_id'=>Yii::app()->user->id,
                'order_time'=>$sdate,
                'book_id'=>$book_id,
                'info_time'=>time(),
            );
            $record = new ReserveRecord();
            $record->attributes = $reserve;
            $record->save();
            $htmlstr =$this->format_reserved_books();
            return $this->renderJson($htmlstr);
        }
    }


    /**
     *续借的跳转
     *edit by xuxiaolong 2013/8/5
     */
    public function actionGetRenew($book_id){
        $all_reserved_book_id = ReserveHelper::get_all_reserved_book_id();
        $in=array("book_id"=>$book_id);
        $data = array();
        $isreserve=in_array($in,$all_reserved_book_id);
        $data['isreserve'] = $isreserve;
        if (!$isreserve){
            $borrowHelper =new BorrowHelper();
            $borrowHelper->update_renew_by_id($book_id);
            $newinfo =$borrowHelper->find("book_id=".$book_id);
            $data['newtime'] =$newinfo->return_date;
        }
        return $this->renderJson($data);
    }

    public function actionGetCancelRecommend($recommend_id){
        $recommend_record =RecommendRecord::model()->find("recommend_id=".$recommend_id);
        $user_id =Yii::app()->user->id;
        $stats_record =StatsRecord::model()->find('recommend_id = ? AND user_id = ?', array($recommend_id, $user_id));
        $total_num =$recommend_record->recommend_num;
        $recommender_num =1;
        if(!empty($stats_record))
            $recommender_num +=$stats_record->recommend_num;
        $data = ($total_num > $recommender_num) ? "no" :"yes";
        if($data =="yes"){
            $recommend_record->delete();
            if(!empty($stats_record))
                $stats_record->delete();
        }
        return $this->renderJson($data);
    }

    private function format_reserved_books(){
        $reserved_books =ReserveHelper::get_reserved_books_by_userid(Yii::app()->user->id);
        $htmlstr ="";
        foreach($reserved_books as $key=>$value){
            if(($key+1)%4==0){
                $htmlstr .="<li id='mbook-".$value['book_id']."' class='group nomarginright'>";
            }else{
                $htmlstr .="<li id='mbook-".$value['book_id']."' class='group'>";
            }
            $htmlstr .="<div class='mbook-card'><div class='mbook-inner-card'><div class='mbook-img'>";
            $htmlstr .="<a href='".Yii::app()->baseUrl."/detail?bookid=".$value['book_id']."'>";
            $htmlstr .="<img src='".$value['image_url']."'/></a></div><div class='large-cover-button'>";
            $htmlstr .="<a id='cancel-".$value['book_id']."' class='ui-widget ui-corner-all ajaxUrl-cancel'>";
            $htmlstr .="<span class='free'>&nbsp;取消预约&nbsp;</span></a></div>";
            $htmlstr .="<h5><a href='".Yii::app()->baseUrl."/detail?bookid=".$value['book_id']."'>".$value['book_name']." </a>".$value['author']."</h5></div></div></li>";
        }
        $htmlstr .="<a style='display:none;' id= 'ajaxCancelResrveUrl' href='".Yii::app()->baseUrl."/account/getCancelResrve/book_id/'></a>";
        return $htmlstr;
    }



    public function actionTestyijie(){
        $userid=Yii::app()->user->id;
        //$books=ReserveHelper::get_reserved_books_by_userid(1133);
        $books=ReserveHelper::get_all_reserve_book_for_user("c++",1109,10,8,false);
        var_dump($books);
    }

    public function actionTestCancel(){
        $all = ReserveHelper::get_reserved_books_by_userid(Yii::app()->user->id);
        $record =ReserveRecord::model()->find("book_id=9011");
        $record->delete();
        $allafter =ReserveHelper::get_reserved_books_by_userid(Yii::app()->user->id);
        var_dump($all);
        var_dump($record);
        var_dump($allafter);
    }
}
