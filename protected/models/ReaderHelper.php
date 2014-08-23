<?php
/**
 * Created by JetBrains PhpStorm.
 * User: xuxiaolong
 * Date: 13-7-27
 * Time: 下午2:36
 * To change this template use File | Settings | File Templates.
 * 对数据库的操作封装在这里
 */
class ReaderHelper extends ReaderRecord{
    public $model;

    public function ReaderHelper(){
        $this->model = ReaderRecord::model();
    }

    public function do_hash($string){
        $salt = substr(md5($string), 0, 9);
        return $salt . sha1($salt . $string);
    }

    public function hash_Validate( $source, $target ){
        return ( $this->do_hash($source) == $target);
    }

    public function validate_password($password,$reader){
        $validate = false;
        if ( ($password == '123456') && ($reader->password =='123456') ){
            $validate = true;
        }
        if($this->hash_Validate($password,$reader->password)){
            $validate = true;
        }
        return $validate;
    }

    public function find_user_by_content($content){
        try{
            $books =array();
            $sidx = 'user_id';
            $sord = 'desc';
            $codition =$this->createCondition($content);
            $attribute = array(
                'order' => $sidx." ".$sord,
                'condition' =>$codition,
            );

            //$attribute['condition']   = $this->createCondition($batch);
            $criteria = $this->createCriteria($attribute);
            $books = $this->model->findAll($criteria,"");
            return $books;
        }catch(Exception $e) {
            return false;
        }
    }

    public static function updatedata($filename){
        $connection = Yii::app()->db;
        $sql = "load data infile '".$filename."' IGNORE into table reader fields terminated by \",\" lines terminated by \"\n\" (user_id,name,image_url,band,department,mobile,E_mail,birthday,gender,city,seat)";
        $command = $connection->createCommand($sql);
        $command->execute();
        return true;
    }

    public function createCondition($content){
        $condition  = "(name LIKE '%".$content."%'";
        $condition .= "OR E_mail LIKE '%".$content."%')";
        return $condition;
    }
}