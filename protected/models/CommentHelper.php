<?php
/**
 * Created by JetBrains PhpStorm.
 * User: xuxiaolong
 * Date: 13-8-4
 * Time: 上午8:04
 * To change this template use File | Settings | File Templates.
 */
class CommentHelper extends CommentRecord{
    public $model;
    public function CommentHelper(){
        $this->model = CommentRecord::model();
    }

    public function get_an_comment_by_book_id($book_id){
        $result=null;
        $comments =$this->model->findAll("book_id='".$book_id."'");
        $comment_num =count($comments);
        if($comment_num >0){
            $sequence = rand(0, $comment_num-1);
            $result =$comments[$sequence];
        }
        return $result;
    }

    public function get_my_comment_on_book($book_id){
        $reader =ReaderRecord::model()->find("user_id=".Yii::app()->user->id);
        $result = $this->model->find('book_id = ? AND comment_email = ?', array($book_id, $reader->E_mail));
        return $result;
    }

    public function add_comment($comment){
        $comment_item=new CommentRecord();
        $comment_item->attributes = $comment;
        return ($comment_item->save()) ? "saved" :"Not save";
    }

    public function change_comment($content){
        $this->model->comment_content = $content;
        $this->model->save();
    }
}