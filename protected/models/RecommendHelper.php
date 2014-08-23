<?php
/**
 * Created by JetBrains PhpStorm.
 * User: xuxiaolong
 * Date: 13-8-2
 * Time: 上午11:18
 * To change this template use File | Settings | File Templates.
 */
class RecommendHelper extends RecommendRecord{
    public $model;
    public function RecommendHelper(){
        $this->model=RecommendRecord::model();
    }

    /**
     * 根据搜索类型和搜索内容获取图书信息
     *
     * @access public
     * @param int $num         遍历条数
     * @param int $offset      分页偏移量
     * @param int $batch       期次
     * @param boolean $flag    为真时：返回搜索图书信息，为假时，返回搜索图书数量
     * @return array - 搜索完成后的图书信息  否则返回FALSE
     */
    public function get_books_by_batch($num, $offset,$batch,$flag){
        try{
            $books =array();
            $sidx = 'recommend_num';
            $sord = 'desc';
            $attribute = array(
                'order' => $sidx." ".$sord, //order by book_id desc
                'condition' =>"batch = '" .$batch ."'",
            );
            if($flag) {
                $attribute['limit']   = $num;
                $attribute['offset']  = $offset;
            }
            //$attribute['condition']   = $this->createCondition($batch);
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
     * 根据搜索类型和搜索内容获取图书信息
     *
     * @access public
     * @param int $num         遍历条数
     * @param int $offset      分页偏移量
     * @param int $batch       期次
     * @param int $userid      推荐者信息
     * @param boolean $flag    为真时：返回搜索图书信息，为假时，返回搜索图书数量
     * @return array - 搜索完成后的图书信息  否则返回FALSE
     */

    public function get_books_by_userid($num, $offset,$batch,$userid,$flag){
        try{
            $books =array();
            $reader = ReaderRecord::model()->find("user_id=".$userid);
            $sidx = 'recommend_num';
            $sord = 'desc';
            $attribute = array(
                'order' => $sidx." ".$sord, //order by book_id desc
                //'condition' =>"batch == ".$batch." and recommender_email == ".$reader->E_mail,
                'condition' =>"batch = '" .$batch ."' and recommender_email = '" .$reader->E_mail ."'",
            );
            if($flag) {
                $attribute['limit']   = $num;
                $attribute['offset']  = $offset;
            }
            //$attribute['condition']   = $this->createCondition($batch);
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

    public function get_books_by_content($num, $offset,$batch,$content,$flag){
        try{
            $books =array();
            $sidx = 'recommend_num';
            $sord = 'desc';
            $codition =$this->createCondition($content);
            $codition .=" and batch=".$batch;
            $attribute = array(
                'order' => $sidx." ".$sord,
                'condition' =>$codition,
            );
            if($flag) {
                $attribute['limit']   = $num;
                $attribute['offset']  = $offset;
            }
            //$attribute['condition']   = $this->createCondition($batch);
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

    public function createCondition($content){
        $condition  = "(book_name LIKE '%".$content."%'";
        $condition .= "OR isbn LIKE '%".$content."%'";
        //$condition .= "OR category LIKE '%".$content."%'";
        $condition .= "OR author LIKE '%".$content."%')";
        //$condition .= "OR publisher LIKE '%".$content."%'";
        return $condition;
    }
}