<?php
/**
 * Created by JetBrains PhpStorm.
 * User: xuxiaolong
 * Date: 13-7-5
 * Time: 上午8:26
 * To change this template use File | Settings | File Templates.
 */
class LibraryController extends EbuptController
{
    public $model;
    public $layout = '';
    public function filters() {
        return array();
    }

    public function actionIndex(){
        if( isset($_GET["currentPage"]) && $_GET["currentPage"] ){
            $currnetPage = $_GET["currentPage"];
        }else{
            $currnetPage = 1;
        }
        $model = new ReaderLoginForm();
        $bookHelper = new BookHelper();
        // pages set
        $model->pagesize = 20;
        $model->totalNum = $bookHelper->get_books(0,0,"allbooks",false);
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
        //find books
        $offset = ($model->pagesize)*$currnetPage - ($model->pagesize);
        if($offset<0) $offset = 0;
        $popularBooks = $bookHelper->get_books($model->pagesize,$offset,"allbooks",true);
        $rankOffset = rand(0, 13);
        $rankBooks = array_slice($popularBooks,$rankOffset,6);
        $recommendOffset =rand(0, 15);
        $recommendBooks = array_slice($popularBooks,$recommendOffset,4);
        foreach($recommendBooks as $book){
            $book->description = mb_substr($book->description,0,110,'utf-8')."...";
        }
        $model->username = Yii::app()->user->name;
        $model->page_selector ='index';
        $this->renderSmarty('library/index.html',array('model'=>$model,'popularBooks'=>$popularBooks,'rankBooks'=>$rankBooks,'recommendBooks'=>$recommendBooks));
    }

    public function actionDetail(){
        if( isset($_GET["bookid"]) && $_GET["bookid"]){
            $book_id = $_GET["bookid"];
            $bookHelper = new BookHelper();
            $book = $bookHelper->find_book_by_id($book_id);
        }
        $this->renderSmarty('library/detail.html', array('book' => $book));
    }

    public function actionSearch(){
        if( isset($_GET["currentPage"]) && $_GET["currentPage"] ){
            $currnetPage = trim($_GET["currentPage"]);
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
        $bookHelper = new BookHelper();
        if (preg_match("/c\+\+/i",$searchContent)){
            $model->contentUrl = preg_replace("/c\+\+/i","cplusplus", $searchContent);
        }else{
            $model->contentUrl= $searchContent;
        }
        $model->content = $searchContent;
        //pages set
        $model->pagesize = 20;
        $model->totalNum = $bookHelper->get_books(0,0,$searchContent,false);
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
        //find books
        $offset = ($model->pagesize)*$currnetPage - ($model->pagesize);
        if($offset<0) $offset = 0;
        $searchBooks = $bookHelper->get_books($model->pagesize,$offset,$searchContent,true);
        $model->username = Yii::app()->user->name;
        $model->page_selector ='index';
        //var_dump($model->pagination);
        //var_dump($model->currentPage);
        //var_dump($model->username);
        $this->renderSmarty('library/search.html',array('model'=>$model,'searchBooks'=>$searchBooks,'rank'=>array_slice($searchBooks,0,6)));
    }

    public function actionQisiwole(){
        if( isset($_GET["currentPage"]) && $_GET["currentPage"] ){
            $currnetPage = $_GET["currentPage"];
        }else{
            $currnetPage = 1;
        }
        echo "test";
    }
    public function actionTest(){
        echo "test start";
        $bookHelper = new BookHelper();
        $attribute = array();
        $criteria = $bookHelper->createCriteria($attribute);
        $allbooks = $bookHelper->model->findAll($criteria,"");
        $test=array();
        foreach( $allbooks as $book){
            if(empty($book->description)){
                $values=$this->actionSearchByIsbn($book->isbn);
                {
                    if(!empty($values['description'])){
                        $book->description=$values['description'];
                        $book->save();
                    }
                }

                $test[$book->book_id]=$values['description'];
            }
        }
        //var_dump($this->get_books(1,20,"c++",false)) ;
        var_dump($test);
        echo "test";
    }

    public function get_books ($num, $offset,$content,$flag){
        try{
            $bookHelper = new BookHelper();
            $books =array();
            $sidx = 'book_id';
            $sord = 'desc';
            $attribute = array(
                'order' => $sidx." ".$sord, //order by FrmTime desc
            );
            if($flag) {
                $attribute['limit']   = $num;
                $attribute['offset']  = $offset;
            }
            if($content != "allbooks")
                $attribute['condition']   = $bookHelper->createCondition($content);
            $criteria = $bookHelper->createCriteria($attribute);
            if($flag){
                $books = $bookHelper->model->findAll($criteria,"");
            }else{
                $books = $bookHelper->model->count($criteria,"");
            }
            return $books;
        }catch(Exception $e) {
            return false;
        }
    }

    private function actionSearchByIsbn($isbn){
        $bookinfo=array();
        $book_img_urls = $this->searchUrlsByIsbn($isbn);
        $bookinfo = $this->searInfoByUrl($book_img_urls['book_url']);
        return $bookinfo;
    }
}