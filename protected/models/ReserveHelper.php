<?php
/**
 * Created by JetBrains PhpStorm.
 * User: xuxiaolong
 * Date: 13-8-4
 * Time: 下午2:25
 * To change this template use File | Settings | File Templates.
 */

class ReserveHelper extends ReserveRecord{
    public $model;
    public function ReserveHelper(){
        $this->model =ReserveRecord::model();
    }

    /**
     * @static
     * @param $userid
     * @return mixed array if not empty
     */
    public static function get_reserved_books_by_userid($userid){
        $db = yii::app()->db;
        $sql ="SELECT * from book where book_id IN(SELECT book_id FROM reserve WHERE user_id=?)";
        $command = $db->createCommand($sql);
        $command->bindParam(1, $userid);
        $ret = $command->queryAll();
        return $ret;
    }

    /**
     * @static
     * @param $userid
     * @param $num
     * @param $offset
     * @param $flag 为真时：返回搜索图书信息，为假时，返回搜索图书数量
     * @return mixed
     */

    public static function get_all_reserve_book_for_user($content,$userid,$num,$offset,$flag){
        //$sql ="SELECT * from book WHERE `status`=1 and book_id not IN(SELECT book_id FROM reserve WHERE user_id=217) limit 0,5"
        //$dql ="SELECT count(*) from book WHERE `status`=1 and book_id not IN(SELECT book_id FROM reserve WHERE user_id=217)"
        $db = yii::app()->db;
        $sql ="SELECT * from book WHERE status=0 and book_id not IN(SELECT book_id FROM reserve WHERE book_id !=0 union all SELECT book_id FROM borrow WHERE user_id=?)";
        if($content != "allbooks"){
            $sql .="and (";
            $sql  .= "book_name LIKE '%".$content."%'";
            $sql .= "OR isbn LIKE '%".$content."%'";
            $sql .= "OR category LIKE '%".$content."%'";
            $sql .= "OR author LIKE '%".$content."%'";
            $sql .=")";
        }
        if($flag){
            $sql .=" limit ?,?";
            $command = $db->createCommand($sql);
            $command->bindParam(1, $userid);
            $command->bindParam(2, $offset);
            $command->bindParam(3, $num);
            $ret = $command->queryAll();
        }else{
            $command = $db->createCommand($sql);
            $command->bindParam(1, $userid);
            $ret = $command->queryAll();
            $ret=count($ret);
        }
        return $ret;
    }

    /*
    * 获取预约的所有图书id
    */
    public static function get_all_reserved_book_id (){
        $db = yii::app()->db;
        $mysql ="SELECT book_id from reserve WHERE book_id !=0";
        $command = $db->createCommand($mysql);
        $ret = $command->queryAll();
        return $ret;
    }

}
