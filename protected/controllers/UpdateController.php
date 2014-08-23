<?php
/**
 * Created by JetBrains PhpStorm.
 * User: xuxiaolong
 * Date: 13-7-9
 * Time: 上午11:50
 * To change this template use File | Settings | File Templates.
 */
class UpdateController extends EbuptController{
    public $model;
    public $layout = '';
    const UPWEBROOT="/home/eblib/www/yiieblib/web/protected/databaseUpdate/";
    public function filters() {
        return array();
    }

    public function actionIndex(){
        $nametime=date('Y-m-d');
        $url = "http://hrad.ebupt.net/ldap/queryByDep.php?depname=ALL&basedn=ou=staff,dc=ebupt,dc=com";
        $result = $this->actionCurl($url);
        $result=preg_replace('/<a[^>]*?>/',"",$result);
        $result=preg_replace('/<\/a>/',"",$result);
        $pattern="/<td>.*?<\/td>(<td>.*?<\/td>)(<td>.*?<\/td>)(<td>.*?<\/td>)<td>.*?<\/td>(<td>.*?<\/td>)(<td>.*?<\/td>)<td>.*?<\/td><td>.*?<\/td>(<td>.*?<\/td>)(<td>.*?<\/td>)(<td>.*?<\/td>)(<td>.*?<\/td>)(<td>.*?<\/td>)(<td>.*?<\/td>)<td>.*?<\/td><td>.*?<\/td>/";
        preg_match_all($pattern,$result,$head);
        $head=array_slice($head,1);
        $head=$this->transpose($head);
        foreach($head as $i=>$cols)
        {
            $jieguo[$i]=implode(",", $cols);
        }
        $result=implode("\n", $jieguo);
        $result=preg_replace('/<td>|<\/td>/',"",$result);
        // $filename="reader{$nametime}.csv";   //取名字
        $filename=self::UPWEBROOT.$nametime.".csv";   //取名字
        $handle=fopen($filename,'w');//打开文件并确认可写
        fwrite($handle,$result);
        $update=ReaderHelper::updatedata($filename);
        var_dump($update);
    }

    public function actionDetail(){
    }

    public function actionUpdateReserve(){
        $reserveHelper =new ReserveHelper();
        $attribute = array();
        $criteria = $reserveHelper->createCriteria($attribute);
        $allreserves = $reserveHelper->model->findAll($criteria,"");
        foreach($allreserves as $reserve){
            if($reserve->info_time ==null || (time()->$reserve->info_time>30*86400)){
                $reserve->delete();
            }
        }
    }


    private function transpose($data){
        $result=array();
        foreach($data as $key=>$value)
        {
            foreach($value as $k=>$v)
            {
                if($key==2) $v="http://hrad.ebupt.net/ldap/temp/".$v.".jpg";
                $result[$k][$key]=$v;
            }
        }
        return $result;
	}

		public function actionGetUpdateReaderInfo(){
	      $url = "http://hrad.ebupt.net/ldap/queryByDep.php?depname=ALL&basedn=ou=staff,dc=ebupt,dc=com";
        $result = $this->actionCurl($url);
        $result=preg_replace('/<a[^>]*?>/i',"",$result);
        $result=preg_replace('/<\/a>/i',"",$result);
        $pattern="/<td>.*?<\/td>(<td>.*?<\/td>)(<td>.*?<\/td>)(<td>.*?<\/td>)<td>.*?<\/td>(<td>.*?<\/td>)(<td>.*?<\/td>)<td>.*?<\/td><td>.*?<\/td>(<td>.*?<\/td>)(<td>.*?<\/td>)(<td>.*?<\/td>)(<td>.*?<\/td>)(<td>.*?<\/td>)(<td>.*?<\/td>)<td>.*?<\/td><td>.*?<\/td>/i";
        preg_match_all($pattern,$result,$head);
        $head=array_slice($head,1);
        $head=$this->transpose($head);
        $connection = Yii::app()->db;
        //$test="";
        //$mysql = "UPDATE IGNORE reader_copy set image_url =".$test."band =".$test."where user_id =".$test;
        foreach($head as $reader){
            $reader[0]=preg_replace('/<td>|<\/td>/i',"",$reader[0]);
            $reader[2]=preg_replace('/<td>|<\/td>/i',"",$reader[2]);
            $reader[3]=preg_replace('/<td>|<\/td>/i',"",$reader[3]);
            $mysql = "UPDATE IGNORE reader set image_url ='".$reader[2]."',band ='".$reader[3]."' where user_id =".$reader[0];
            $command = $connection->createCommand($mysql);
            $command->execute();
			}
			return $this->renderJson(true);
        //var_dump($head);
	
	}



    public function actionGetUpdateBookInfo(){
        $bookHelper = new BookHelper();
        $attribute = array();
        $criteria = $bookHelper->createCriteria($attribute);
        $allbooks = $bookHelper->model->findAll($criteria,"");
        foreach( $allbooks as $book){
            if(empty($book->description) || empty($book->image_url)){
                $values=$this->updateByIsbn($book->isbn);
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

    public function actionNewBook(){
        $isbn =$_POST["isbn"];
        $bookinfo = $this->updateByIsbn($isbn);
        $model =new BookRecord();
        $book = array();
        $book['author']=$bookinfo['author'];
        $book['book_name']=$bookinfo['book_name'];
        $book['description']=$bookinfo['description'];
        $book['image_url']=$bookinfo['image_url'];
        $book['isbn']=$bookinfo['isbn'];
        $book['price']=$bookinfo['price'];
        $book['status'] ='1';
        $book['import_date'] =date("YmdHis");
        $book['publisher']=$bookinfo['publisher'];
        $book['pages']=$bookinfo['pages'];
        $book['publish_date']=$bookinfo['publish_date'];
        $model->attributes = $book;
        if($model->validate() &&$model->save()){  //
            echo "<script>alert('恭喜您，成功。');</script>";
            return;
        }else{
            echo "<script>alert('对不起，失败。');</script>";
            return;
        }
    }

    private function updateByIsbn($isbn){
        $bookinfo=array();
        $book_img_urls = $this->searchUrlsByIsbn($isbn);
        $bookinfo = $this->searInfoByUrl($book_img_urls['book_url']);
        return $bookinfo;
    }

    public function actionTestReader(){
        $nametime=date('Y-m-d');
        $url = "http://hrad.ebupt.net/ldap/queryByDep.php?depname=ALL&basedn=ou=staff,dc=ebupt,dc=com";
        $result = $this->actionCurl($url);
        $result=preg_replace('/<a[^>]*?>/i',"",$result);
        $result=preg_replace('/<\/a>/i',"",$result);
        $pattern="/<td>.*?<\/td>(<td>.*?<\/td>)(<td>.*?<\/td>)(<td>.*?<\/td>)<td>.*?<\/td>(<td>.*?<\/td>)(<td>.*?<\/td>)<td>.*?<\/td><td>.*?<\/td>(<td>.*?<\/td>)(<td>.*?<\/td>)(<td>.*?<\/td>)(<td>.*?<\/td>)(<td>.*?<\/td>)(<td>.*?<\/td>)<td>.*?<\/td><td>.*?<\/td>/i";
        preg_match_all($pattern,$result,$head);
        $head=array_slice($head,1);
        $head=$this->transpose($head);
        $connection = Yii::app()->db;
        //$test="";
        //$mysql = "UPDATE IGNORE reader_copy set image_url =".$test."band =".$test."where user_id =".$test;
        foreach($head as $reader){
            $reader[0]=preg_replace('/<td>|<\/td>/i',"",$reader[0]);
            $reader[2]=preg_replace('/<td>|<\/td>/i',"",$reader[2]);
            $reader[3]=preg_replace('/<td>|<\/td>/i',"",$reader[3]);
            $mysql = "UPDATE IGNORE reader set image_url ='".$reader[2]."',band ='".$reader[3]."' where user_id =".$reader[0];
            $command = $connection->createCommand($mysql);
            $command->execute();
        }
        var_dump($head);
    }
}
