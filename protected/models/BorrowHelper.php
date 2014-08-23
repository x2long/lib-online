<?php
/**
 * Created by JetBrains PhpStorm.
 * User: xuxiaolong
 * Date: 13-8-4
 * Time: 下午6:02
 * To change this template use File | Settings | File Templates.
 */
class BorrowHelper extends BorrowRecord{
    const RENEW_DAY  = "15";
    public $model;
    public function BorrowHelper(){
        $this->model=BorrowRecord::model();
    }

    /**
     * 根据用户ID获取该ID下的所有借阅信息(含图书名称)
     *
     * @access public
     * @param int $uid 用户ID
     * @return array - 所有借阅信息(含图书名称)  否则返回FALSE
     *	edit by universe 2012/3/19
     */

    public static function get_mix_info_by_user_id($uid){
        $db = yii::app()->db;
        $mysql="SELECT *
				FROM book, borrow
				WHERE book.book_id = borrow.book_id
				AND borrow.user_id = '".$uid."'";
        $command = $db->createCommand($mysql);
        $ret = $command->queryAll();
        return $ret;
    }

    /**
     * 根据用户ID获取该ID下的所有借阅信息
     *
     * @access public
     * @param int $uid 用户ID
     * @return array - 所有借阅信息  否则返回FALSE
     */

    public static function get_borrow_info_by_user_id($uid){
        $db = yii::app()->db;
        $mysql="SELECT *
				FROM borrow
				WHERE user_id = '".$uid."'";
        $command = $db->createCommand($mysql);
        $ret = $command->queryAll();
        return $ret;
    }

    /**
     * 更新借阅时间，自助续借功能
     *
     * @access public
     * @param int $id 图书ID
     * @return boolean 成功或失败
     */

    public function update_renew_by_id($id)
    {
        /*
          *首先取出来return_time将其日期处理后（即推后RENEW_DAY天），返回更改后的日期$ymd_date
          *edit by universe 2012/3/20
          */
        $record = $this->model->find("book_id=".$id);
        $time=strtotime($record->return_date);
        $return_time=$time+(self::RENEW_DAY)*24*3600;
        $ymd_date=date("Y/m/d", $return_time);

        /*
          *更新表，即renew_num++;return_date+=RENEW_DAY
          *edit by universe 2012/3/20
          */

        $record ->return_date=$ymd_date;
        $record ->renew_num +=1;
        return $record ->save();
    }
}