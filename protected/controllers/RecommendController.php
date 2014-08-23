<?php
/**
 * Created by JetBrains PhpStorm.
 * User: xuxiaolong
 * Date: 13-5-16
 * Time: 上午11:38
 * To change this template use File | Settings | File Templates.
 */
class RecommendController extends EbuptController
{
    
    const Max_Like=5;
    public $model;
    public $layout = '';
    public function filters() {
        return array();
    }
    /**
     * list all the recommended books
     */
    public function actionIndex(){
        if( isset($_GET["currentPage"]) && $_GET["currentPage"] ){
            $currnetPage = $_GET["currentPage"];
        }else{
            $currnetPage = 1;
        }
        $model = new ReaderLoginForm();
        $model->username = Yii::app()->user->name;
        $model->page_selector ='recommend';
        $model->tab_selector='index';
        $model->pagesize = 10;
        //test books
        $recommendHelper = new RecommendHelper();
        $model->totalNum = $recommendHelper->get_books_by_batch(0,0,Yii::app()->params['BATCH'],false);
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
        //find books
        $offset = ($model->pagesize)*$currnetPage - ($model->pagesize);
        if($offset<0) $offset = 0;
        $book_model = $recommendHelper->get_books_by_batch($model->pagesize,$offset,Yii::app()->params['BATCH'],true);
        $cookies = Yii::app()->request->getCookies();
        $recommends =null;
        foreach($book_model as $key=>$value){
            $description['base']=mb_substr($value->description,0,250,'utf-8');
            $description['extra']=mb_substr($value->description,250,mb_strlen($value->description,'utf-8')-250,'utf-8');
            $value->description=$description;
            $recommends[$key]["book"]=$value;
            $recommends[$key]["reader"]=ReaderRecord::model()->find("E_mail= '".$value->recommender_email."'");
            $recommends[$key]['coockie']=false;
            if(isset($cookies[$value->recommend_id]) && $cookies[$value->recommend_id]->value=='yes'){
                $recommends[$key]['coockie']=true;
            }
        }
        //$cookies = Yii::app()->request->getCookies();
        $this->renderSmarty('recommend/index.html',array('model'=>$model,'recommends'=>$recommends ,'coockies'=>Yii::app()->request->getCookies()));
    }
    /**
     * 自主推荐
     */

    public function actionNormal(){
        if(Yii::app()->user->isGuest){
            $url = "http://".$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"]."/yiieblib/web";
            echo "<script>alert('对不起，您还没有登录，请您登录。如果有问题，请联系管理员(houyihan@ebupt.com)！');window.location.href='".$url."/login"."';</script>";
            return;
        }
        $model = new ReaderLoginForm();
        $confirmbooks = $this->makeConfirmBooks(true);
        $recommendHelper = new RecommendHelper();
        $user_id=Yii::app()->user->id;
        $model->totalNum =$recommendHelper->get_books_by_userid(0,0,Yii::app()->params['BATCH'],$user_id,false);
        //var_dump($confirmbooks);
        $model->username = Yii::app()->user->name;
        $model->page_selector ='recommend';
        $model->tab_selector='normal';
        $this->renderSmarty('recommend/normal.html',array('model'=>$model,'confirmbooks'=>$confirmbooks,"reader"=>$reader = ReaderRecord::model()->find("user_id=".$user_id)));
    }
    /**
     * 豆瓣推荐
     */

    public function actionDouban(){
        if(Yii::app()->user->isGuest){
            $url = "http://".$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"]."/yiieblib/web";
            echo "<script>alert('对不起，您还没有登录，请您登录。如果有问题，请联系管理员(houyihan@ebupt.com)！');window.location.href='".$url."/login"."';</script>";
            return;
        }
        $model = new ReaderLoginForm();
        $confirmbooks = $this->makeConfirmBooks(false);
        $recommendHelper = new RecommendHelper();
        $user_id=Yii::app()->user->id;
        $model->totalNum =$recommendHelper->get_books_by_userid(0,0,Yii::app()->params['BATCH'],$user_id,false);
        $testbooks=array();
        $testbooks['book_name']="book_name";
        $testbooks['author']="author";
        $testbooks['isbn']="isbn";
        $testbooks['douban_url']="douban_url";
        $model->username = Yii::app()->user->name;
        $model->page_selector ='recommend';
        $model->tab_selector='douban';
        $this->renderSmarty('recommend/douban.html',array('model'=>$model,'confirmbooks'=>$confirmbooks,"reader"=>$reader = ReaderRecord::model()->find("user_id=".$user_id)));
    }

    /**
     * list all the my recommended books
     */

    public function actionMine(){
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
        $model->page_selector='recommend';
        $model->tab_selector='mine';
        $this->renderSmarty('recommend/user.html',array('model'=>$model,'recommendBooks'=>$recommendBooks,'reader'=>$reader,'urltype'=>"mine"));
    }

    public function actionSearch(){
        if( isset($_GET["searchType"]) && $_GET["searchType"] && isset($_GET["searchContent"]) && $_GET["searchContent"]){
            $searchType =trim($_GET['searchType']);
            $searchContent =trim($_GET['searchContent']);
        }else{
            $searchType ="any";
            $searchContent ="allbooks";
        }
        if($searchType =="recommender"){
            $readerHelper=new ReaderHelper();
            $recommender =$readerHelper->find_user_by_content($searchContent);
            if(empty($recommender)){
                echo "<script>alert('对不起，您要查询的推荐人不在数据库中，如果您确认输入无误却依然有问题，请联系管理员(houyihan@ebupt.com)！');window.location.href='".Yii::app()->baseUrl."/site/resetPassword"."';</script>";
                return;
            }else{
                $recommender=$recommender[0];
                $searchContent="1";
                if(isset($recommender->user_id) && $recommender->user_id){
                    $searchContent =$recommender->user_id;
                    if($searchContent==Yii::app()->user->id){
                        $nextUrl = Yii::app()->baseUrl."/recommend/mine";
                        $this->redirect($nextUrl);
                    }
                }
            }
        }
        if( isset($_GET["currentPage"]) && $_GET["currentPage"] ){
            $currnetPage = $_GET["currentPage"];
        }else{
            $currnetPage = 1;
        }
        $model = new ReaderLoginForm();
        $recommendHelper = new RecommendHelper();
        $model->pagesize = 10;
        if($searchType =="recommender"){
            $model->totalNum =$recommendHelper->get_books_by_userid(0,0,Yii::app()->params['BATCH'],$searchContent,false);
        }else{
            $model->totalNum =$recommendHelper->get_books_by_content(0,0,Yii::app()->params['BATCH'],$searchContent,false);
        }
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
        $model->content =$searchContent;
        $model->username = Yii::app()->user->name;
        $model->page_selector='recommend';
        $model->tab_selector='index';
        $offset = ($model->pagesize)*$currnetPage - ($model->pagesize);
        if($offset<0) $offset = 0;
        if($searchType =="recommender"){
            $searchBooks =$recommendHelper->get_books_by_userid($model->pagesize,$offset,Yii::app()->params['BATCH'],$searchContent,true);
            foreach($searchBooks as $key=>$value){
                $description['base']=mb_substr($value->description,0,250,'utf-8');
                $description['extra']=mb_substr($value->description,250,mb_strlen($value->description,'utf-8')-250,'utf-8');
                $value->description=$description;
            }
            $reader = ReaderRecord::model()->find("user_id=".$searchContent);
            $this->renderSmarty('recommend/search.html',array('model'=>$model,'searchBooks'=>$searchBooks,'reader'=>$reader,'searchType'=>$searchType));
        } else{
            $book_model =$recommendHelper->get_books_by_content($model->pagesize,0,Yii::app()->params['BATCH'],$searchContent,true);
            $searchBooks =null;
            foreach($book_model as $key=>$value){
                $description['base']=mb_substr($value->description,0,250,'utf-8');
                $description['extra']=mb_substr($value->description,250,mb_strlen($value->description,'utf-8')-250,'utf-8');
                $value->description=$description;
                $searchBooks[$key]["book"]=$value;
                $searchBooks[$key]["reader"]=ReaderRecord::model()->find("E_mail= '".$value->recommender_email."'");
            }
            $this->renderSmarty('recommend/search.html',array('model'=>$model,'searchBooks'=>$searchBooks,'searchType'=>$searchType));
        }
    }

    public function actionShowUserbooks(){
        if( isset($_GET["userid"]) && $_GET["userid"] ){
            $userid =trim($_GET['userid']);
            if($userid == Yii::app()->user->id){
                $nextUrl = Yii::app()->baseUrl."/recommend/mine";
                $this->redirect($nextUrl);
            }
            $reader = ReaderRecord::model()->find("user_id=".$userid);
        }
        if( isset($_GET["currentPage"]) && $_GET["currentPage"] ){
            $currnetPage = $_GET["currentPage"];
        }else{
            $currnetPage = 1;
        }
        $model = new ReaderLoginForm();
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
        $model->page_selector='recommend';
        $model->tab_selector='index';
        $this->renderSmarty('recommend/user.html',array('model'=>$model,'recommendBooks'=>$recommendBooks,'reader'=>$reader,'urltype'=>"showUserbooks/userid/".$reader->user_id));
    }



    public function actionGetAddLike($recommend_id){
        $data=($this->add_recommend_num($recommend_id)) ? "yes" :"no";
        $this->renderJson($data);
    }

    private function add_recommend_num($recommend_id){
        $user_id =Yii::app()->user->id;
        $stats_item=StatsRecord::model()->find('user_id = ? AND recommend_id = ?', array($user_id,$recommend_id));
        $data=true;
        if(count($stats_item)==0){
            $item=new StatsRecord;
            $item->stats_id='';
            $item->user_id=$user_id;
            $item->recommend_id=$recommend_id;
            $item->recommend_num=1;
            $item->save();
        }elseif($stats_item->recommend_num>=self::Max_Like) {
            $data=false;
        }else{
            $stats_item->recommend_num +=1;
            $stats_item->save();
        }
        if($data){
            $recommend_item=RecommendRecord::model()->find('recommend_id ='.$recommend_id);
            $num=$recommend_item->recommend_num;
            $num +=1;
            $recommend_item->recommend_num =$num;
            $recommend_item->save();
        }
        return $data;
    }

    private function addRecommendBook($recommendbook){
        date_default_timezone_set('Asia/Chongqing');
        $model = new RecommendRecord ;
        $book = array();
        $book['recommend_num']=1;
		$book['version']=1;
        $reader = ReaderRecord::model()->find("user_id=".Yii::app()->user->id);
        $book['recommender'] = $reader->name;
        $book['recommender_email']=$reader->E_mail;
        $book['recommend_date']=date("YmdHis");
        $book['batch']=Yii::app()->params['BATCH'];
        $book['recommend_id']='';
		$book['isbn']=$recommendbook['isbn'];
        $book['book_name']=$recommendbook['book_name'];
		$book['author']=$recommendbook['author'];
		$book['price']=$recommendbook['price'];
		$book['publisher']=$recommendbook['publisher'];
		$book['book_url']=$recommendbook['book_url'];
		$book['image_url']=$recommendbook['image_url'];
		$book['description']=$recommendbook['description'];
        $model->attributes = $book;
//        var_dump($model->validate());
//        var_dump($book);
        if($model->validate() &&$model->save()){  //
            echo "<script>alert('恭喜您，推荐成功。');window.location.href='".Yii::app()->baseUrl."/recommend/mine"."';</script>";
            return;
        }else{
            echo "<script>alert('对不起，出了点小意外，请联系管理员(houyihan@ebupt.com)！');window.location.href='".Yii::app()->baseUrl."/recommend/"."';</script>";
            return;
        }
    }

    private function parseByUrl($url){
        $book_info = $this->searInfoByUrl($url);
        if(is_null($book_info['isbn'])){
            return null;
        }else{
            return $book_info;
        }
    }

    private function parseByIsbn($isbn){
        $book_img_urls = $this->searchUrlsByIsbn($isbn);
        return $this->parseByUrl($book_img_urls['book_url']);
    }

    /**
     * @access private
     * @param boolean $flag    为假时：处理“豆瓣推荐”，为真时，处理“自主推荐”
     * @return mixed
     */
    private function makeConfirmBooks($flag){
        $confirmbooks =null;
        if((isset($_POST["douban_url"]) && $_POST["douban_url"]) ||(isset($_POST["isbn"]) && $_POST["isbn"])){
			if(isset($_POST["isbn"]) && $_POST["isbn"]){
				$isbn = trim($_POST["isbn"]);
				$book_img_urls =$this->searchUrlsByIsbn($isbn);
                $douban_url=$book_img_urls['book_url'];
			}else{
				$douban_url = trim($_POST["douban_url"]);
			}            
            if(isset($_POST["checked"]) && (trim($_POST["checked"])=="yes")){
                //tuijianmoban
                $recommend_books = $this->parseByUrl($douban_url);
                $this->addRecommendBook($recommend_books);
            }else{
                $confirmbooks = $this->parseByUrl($douban_url);
                if( is_null($confirmbooks) || is_null($confirmbooks['isbn']) ){
                    if($flag){
                        echo "<script>alert('灰常抱歉，根据您提供的信息我们推荐失败，麻烦您请联系管理员(houyihan@ebupt.com)！');window.location.href='".Yii::app()->baseUrl."/recommend/normal"."';</script>";
                        return;
                    }else{
                        echo "<script>alert('灰常抱歉，根据您提供的URL我们没有找到图书，请您确认，建议您选择自主推荐，如果有问题，请联系管理员(houyihan@ebupt.com)！');window.location.href='".Yii::app()->baseUrl."/recommend/normal"."';</script>";
                        return;
                    }
                }else{
                    $isbn =$confirmbooks['isbn'];
                    $temp = BookRecord::model()->find("isbn='".$isbn."'");
                    if(count($temp) ==1){
                        echo "<script>alert('您要推荐的书图书馆已有，您可以联系管理员(houyihan@ebupt.com)借阅！');window.location.href='".Yii::app()->baseUrl."/detail?bookid=".$temp->book_id."';</script>";
                        return;
                    }
                    $temp =RecommendRecord::model()->find('isbn = ? AND batch = ?', array($isbn, Yii::app()->params['BATCH']));
                    if(count($temp) ==1){
                        echo "<script>alert('您要推荐的书这次已被推荐，您可以去顶顶哟！');window.location.href='".Yii::app()->baseUrl."/recommend"."';</script>";
                        return;
                    }
                    $confirmbooks["douban_url"] = $douban_url;
                }
            }
        }
        return $confirmbooks;
    }

    public function actionTest(){
        $url ="http://book.douban.com/subject/5288326/";
        $result = $this->actionCurl($url);
        $html =Yii::app()->simhtmldom;
        $html->load($result);
        $result=preg_replace('/<a[^>]*?>/',"",$result);
        $result=preg_replace('/<\/a>/',"",$result);
        $html->load($result);
        $div_book_about=$html->find('div#info',0)->innertext;
        $html->clear();
        $pattern="/<span class=\"pl\">(.*?)<\/span>(.*?)<br\/>/i";
        preg_match_all($pattern,$div_book_about,$matches);
        $pattern1="/<span\s*class=\"pl\">\s*ISBN\s*:\s*<\/span>\s*(\d{13})\s*<br\/>/i";
        preg_match_all($pattern1,$div_book_about,$isbns);
        $isbn=$isbns[1][0];
        $length=count($matches[1]);
        $book=array();
        for($i=0;$i<$length;$i++){
            $key=$this->preTreatment($matches[1][$i]);
            $value=$this->preTreatment($matches[2][$i]);
            $book[$key]=$value;
        }
        $book['is']=$isbn;
        var_dump($book);
    }

    public function actionTesturl(){
        $url ="http://book.douban.com/subject/24853106/";
        $book = $this->searInfoByUrl($url);
        var_dump($book);
    }
	
	public function actionTestAdd(){
		$url = "http://book.douban.com/subject/24853106/";
		$recommendbook = $this->searInfoByUrl($url);
        date_default_timezone_set('Asia/Chongqing');
        $model = new RecommendRecord ;
        $book = array();
        $book['recommend_num']=1;
		$book['version']=1;
        $reader = ReaderRecord::model()->find("user_id=".Yii::app()->user->id);
        $book['recommender'] = $reader->name;
        $book['recommender_email']=$reader->E_mail;
        $book['recommend_date']=date("YmdHis");
        $book['batch']=Yii::app()->params['BATCH'];
        $book['recommend_id']='';
        $book['book_name']=$recommendbook['book_name'];
		$book['isbn']=$recommendbook['isbn'];
		$book['author']=$recommendbook['author'];
		$book['price']=$recommendbook['price'];
		$book['publisher']=$recommendbook['publisher'];
		//$book['book_url']=$recommendbook['book_url'];
		$book['image_url']=$recommendbook['image_url'];
		$book['description']=$recommendbook['description'];
        var_dump($book);
        $model->attributes = $book;
		//var_dump($model);
		var_dump($model->validate() );
		var_dump($model->save() );
	}

    public function actionTestHelper(){
        $sidx = 'recommend_num';
        $sord = 'desc';
        $email="houyihan@ebupt.com";
        $attribute = array(
            'order' => $sidx." ".$sord, //order by book_id desc
            //'condition' =>"batch =" .Yii::app()->params['BATCH'] ,
           // 'condition' =>"batch = '" .Yii::app()->params['BATCH'] ."'",
            'condition' =>"batch = '" .Yii::app()->params['BATCH'] ."' and recommender_email = '" .$email ."'",
        );
        $criteria =ActiveRecord::createCriteria($attribute);
        $books = RecommendRecord::model()->count($criteria,"");  //model()->count($criteria,"");
        $rechelp =new RecommendHelper();
        $books = $rechelp->get_books_by_userid(10,0,Yii::app()->params['BATCH'],'1016',true);
        echo "test1";
        var_dump($books);
    }

    public function actionTestcookie(){
        $cookie = new CHttpCookie('mycookie','this is my cookie');
        $cookie->expire = time()+60*60*24*30;  //有限期30天
        Yii::app()->request->cookies['mycookie']=$cookie;
        $cookie = Yii::app()->request->getCookies();
        var_dump($cookie['167']->value);
    }

}
