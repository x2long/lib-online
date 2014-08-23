<?php
/**
 * Created by JetBrains PhpStorm.
 * User: xuxiaolong
 * Date: 13-7-9
 * Time: 上午11:49
 * To change this template use File | Settings | File Templates.
 */
class ReserveController extends EbuptController{
    public $model;
    public $layout = '';
    public function filters() {
        return array();
    }

    public function actionIndex(){
    }

    public function actionDetail(){
    }

    public function actionInformReserver(){
        $isbn=trim($_POST["isbn"]);
        $book =BookRecord::model()->find("isbn=".$isbn);
        var_dump($book->book_id);
        $reserve_item  = ReserveRecord::model()->find("book_id=".$book->book_id);
        $reverser = ReaderRecord::model()->find("user_id=".$reserve_item->user_id);
        // update info_time of reserve table
        $reserve_item->info_time =time()+7*86400;
        $reserve_item->save();
        //send the inform mail
        $model =new RemindReserveForm();
        $model->reverserName = $reverser->name;
        $model->reveredBook =$book;
        if($model->sendMail('remindReverser',$reverser->E_mail)) {
            echo "<script>alert('邮件发送成功！请查收邮件。');</script>";
            return;
        } else {
            echo "<script>alert('对不起，邮件发送失败！请联系管理员(houyihan@ebupt.com)！');</script>";
            return;
        }

    }
}