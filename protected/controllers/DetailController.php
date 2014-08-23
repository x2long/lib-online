<?php
/**
 * Created by JetBrains PhpStorm.
 * User: xuxiaolong
 * Date: 13-7-9
 * Time: 上午11:47
 * To change this template use File | Settings | File Templates.
 */
class DetailController extends EbuptController{
    public $model;
    public $layout = '';
    const Pages_Per_Hour=65.0;
    public function filters() {
        return array();
    }

	public function actionIndex(){
		$book_id = $_GET["bookid"];
        $book = BookRecord::model()->find("book_id='".$book_id."'");
        $model = new ReaderLoginForm();
        //book
        $description['base']=mb_substr($book->description,0,250,'utf-8');
        $description['extra']=mb_substr($book->description,250,mb_strlen($book->description,'utf-8')-250,'utf-8');
        $book->description=$description;
        $book_url =$book->image_url;
        $book_url=preg_replace("/spic/i","lpic", $book_url);
        $book->image_url =$book_url;
        //$model->params['score/book_url/hour/minute']
        $params=$this->actionSearchByIsbn($book->isbn);
        $params['hour']=floor(($book->pages)/self::Pages_Per_Hour);
        $params['minute']=ceil(((($book->pages)/self::Pages_Per_Hour)-$params['hour'])*60);
        $model->page_selector='index';
        $model->username =Yii::app()->user->name;
        $commentHelper=new CommentHelper();
        $comment =$commentHelper->get_an_comment_by_book_id($book_id);
        $ckeditor =Yii::app()->ckeditor;
        $ck =$ckeditor->editor('contents');
        $likebooks = BookHelper::find_random_book(4);
        $params['likebooks']=$likebooks;
        //var_dump($params);
        $this->renderSmarty('detail/index.html', array('model'=>$model,'params'=>$params,'book'=>$book,'comment'=>$comment,'ck'=>$ck));
    }

    public function actionAddComment(){
        date_default_timezone_set('Asia/Chongqing');
        $sdate=date("YmdHis");
        $comment_name="no";
        $div_StarRating=0;
        if(isset($_POST["comment_name"]) && $_POST["comment_name"]){
            $comment_name=$_POST["comment_name"];
        }
        if(isset($_POST["div_StarRating"]) && $_POST["div_StarRating"]){
            $div_StarRating=$_POST["div_StarRating"];
        }
        $reader = ReaderRecord::model()->find("user_id=".Yii::app()->user->id);
        if(isset($_POST["contents"]) && $_POST["contents"]) {
            $content=$_POST['contents'];
            $comment =array(
                'comment_id'=>'',
                'book_id'=>$_POST['book_id'],
                'comment_name'=>$comment_name,
                'comment_date'=>$sdate,
                'comment_content'=>$content,
                'comment_author'=>$reader->name,
                'comment_level'=>$div_StarRating,
                'comment_email'=>$reader->E_mail,);
            $commentHelper =new CommentHelper();
            $commentHelper->add_comment($comment);
        }
        $nextUrl = Yii::app()->baseUrl."/detail?bookid=".$_POST['book_id'];
        $this->redirect($nextUrl);
    }

    public function actionDetail(){
        $curl = curl_init();
        //设置URL参数
        curl_setopt($curl,CURLOPT_URL,"http://book.douban.com/subject/24540864/");
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
        //$score=$html->find('strong',0)->innertext;
        var_dump($html->find('a'));
    }

    private function actionSearchByIsbn($isbn){
        $bookinfo=array();
        $book_img_urls = $this->searchUrlsByIsbn($isbn);
        $bookinfo['book_url']=$book_img_urls['book_url'];
        $result = $this->actionCurl($book_img_urls['book_url']);
        $html =Yii::app()->simhtmldom;
        $html->load($result);
        $result=preg_replace('/<a[^>]*?>/',"",$result);
        $result=preg_replace('/<\/a>/',"",$result);
        $html->load($result);
        $bookinfo['score']=$html->find('strong',0)->innertext;
        return $bookinfo;
    }


	public function actionGetComment(){
		$book_id = $_GET["bookid"];
		$commentHelper=new CommentHelper();
        $comment =$commentHelper->get_an_comment_by_book_id($book_id);
		$data = array();
		$data['title'] = $comment->comment_name;
		$data['content'] = $comment->comment_content;
		$data['score'] = $comment->comment_level;
		$data['author']= $comment->comment_author;
		return $this->renderJson($data);
	}
    public function actionTest(){
        $book_id = $_GET["bookid"];
        $cbook=new BookHelper();
        $book=$cbook->find_book_by_id($book_id);
        $description['base']=mb_substr($book->description,0,250,'utf-8');
        $description['extra']=mb_substr($book->description,250,mb_strlen($book->description,'utf-8')-250,'utf-8');
        $book->description=$description;
        $values=$this->actionSearchByIsbn($book->isbn);
        $score=$values['score'];
        $book_url=$values['book_url'];
        $time['hour']=floor(($book->pages)/self::Pages_Per_Hour);
        $time['minute']=ceil(((($book->pages)/self::Pages_Per_Hour)-$time['hour'])*60);
        $this->renderSmarty('detail/index.html', array('book_url'=>$book_url,'score'=>$score,'time'=>$time,'book' => $book,'username'=>Yii::app()->user->name,'pageselector'=>'index'));
    }

    public function actionTesturi(){
        $testuri=$_GET["antest"];
        var_dump($testuri);
    }

    public function actionTestCkeditorls(){
        $ckeditor =Yii::app()->ckeditor;
        $ck =$ckeditor->editor('contents');
        $this->renderSmarty("detail/test.html",array('name'=>'xxl','ckeditor'=>$ck));
    }

    public function actionTestRandom(){
        $likebook=BookHelper::find_random_book(4);
        var_dump($likebook);
    }

    public function actionTestIndex(){
        $book_id = "9440";
        $book = BookRecord::model()->find("book_id='".$book_id."'");
        $model = new ReaderLoginForm();
        //book
        $description['base']=mb_substr($book->description,0,250,'utf-8');
        $description['extra']=mb_substr($book->description,250,mb_strlen($book->description,'utf-8')-250,'utf-8');
        $book->description=$description;
        $book_url =$book->image_url;
        $book_url=preg_replace("/spic/i","lpic", $book_url);
        $book->image_url =$book_url;
        //$model->params['score/book_url/hour/minute']
        $params=$this->actionSearchByIsbn($book->isbn);
        $params['hour']=floor(($book->pages)/self::Pages_Per_Hour);
        $params['minute']=ceil(((($book->pages)/self::Pages_Per_Hour)-$params['hour'])*60);
        $model->page_selector='index';
        $model->username =Yii::app()->user->name;
        $commentHelper=new CommentHelper();
        $comment =$commentHelper->get_an_comment_by_book_id($book_id);
        $ckeditor =Yii::app()->ckeditor;
        $ck =$ckeditor->editor('contents');
        $likebooks = BookHelper::find_random_book(4);
        $params['likebooks']=$likebooks;
        //var_dump($params);
        $this->renderSmarty('detail/index.html', array('model'=>$model,'params'=>$params,'book'=>$book,'comment'=>$comment,'ck'=>$ck));

    }

    public function actionTestFor(){
        $isbn = "9787111075660";
        $Book_img_urls=array();
        $search_url="http://book.douban.com/subject_search?search_text=".$isbn."&cat=1001";
        $result = $this->actionCurl($search_url);
        $html =Yii::app()->simhtmldom;
        $html->load($result);
        $a=$html->find('div.pic a',0);//->innertext;
        //$Book_img_urls['book_url']=$a->href;
        //$Book_img_urls['img_url']=$a->children(0)->src;
        $html->clear();
        echo $result;
    }
}
