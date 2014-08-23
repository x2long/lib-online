<?php
/**
 * Created by JetBrains PhpStorm.
 * User: xuxiaolong
 * Date: 13-7-17
 * Time: 上午9:36
 * To change this template use File | Settings | File Templates.
 */
class BookHelper extends BookRecord{
    public $model;
    public function BookHelper(){
        $this->model=BookRecord::model();
    }

    public static function find_random_book($num){
        $connection = Yii::app()->db;
        //$sql = "select top ".$num." * from book order by newid()";
        $sql ="SELECT * from book ORDER BY RAND() limit ".$num;
        $command = $connection->createCommand($sql);
        $ret = $command->queryAll();
        return $ret;
    }

    /**
     * 根据图书ID获取图书信息
     *
     * @access public
     * @param int $bid 图书id
     * @return mixed {array | FALSE}   单条图书信息 | FALSE
     */
    public function find_book_by_id($book_id){
        $result = array();
        $book = $this->model->find("book_id='".$book_id."'");
        if(count($book) ==1){
            $result = $book;
        }else{
            $result =false;
        }
        return $result;
    }

    /**
     * 根据搜索类型和搜索内容获取图书信息
     *
     * @access public
     * @param int $num         遍历条数
     * @param int $offset      分页偏移量
     * @param string $content  图书搜索内容
     * @param boolean $flag    为真时：返回搜索图书信息，为假时，返回搜索图书数量
     * @return array - 搜索完成后的图书信息  否则返回FALSE
     */
	public function get_books ($num, $offset,$content,$flag){
        try{
            $books =array();
            $sidx = 'book_id';
            $sord = 'desc';
            $attribute = array(
                'order' => $sidx." ".$sord, //order by book_id desc
            );
            if($flag) {
                $attribute['limit']   = $num;
                $attribute['offset']  = $offset;
            }
            if($content != "allbooks")
                $attribute['condition']   = $this->createCondition($content);
            $criteria = $this->createCriteria($attribute);
            if($flag){
                $books = $this->model->findAll($criteria,"");
            }else{
                $books = $this->model->count($criteria,"");
            }
            return $books;
        }catch(Exception $e) {
            return false;
        }
    }

    /**
     * 根据isbn号查询book表的图书（查重）
     * @access public
     * @param int $isbn 推荐图书isbn号
     * @param int $batch 按期数查询，每期都要查book表
     * @return boolean 可用返回真，重复返回假
     */
	public function check_book_by_isbn($isbn){
    }

    /**
     * 根据查询内容设置where条件
     */
    public function createCondition($content){
        $condition  = "book_name LIKE '%".$content."%'";
        $condition .= "OR isbn LIKE '%".$content."%'";
        $condition .= "OR category LIKE '%".$content."%'";
        $condition .= "OR author LIKE '%".$content."%'";
        //$condition .= "OR publisher LIKE '%".$content."%'";
        return $condition;
    }
}