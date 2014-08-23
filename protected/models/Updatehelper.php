<?php
/**
 * Created by JetBrains PhpStorm.
 * User: xuxiaolong
 * Date: 13-8-1
 * Time: 下午8:09
 * To change this template use File | Settings | File Templates.
 */
class Uadatehelper{
    public static function updatedata($filename){
        $connection = Yii::app()->db;
        $sql = "load data infile ".$filename." IGNORE into table reader fields terminated by \",\" lines terminated by \"\n\" (user_id,name,image_url,band,department,mobile,E_mail,birthday,gender,city,seat)";
        $command = $connection->createCommand($sql);
        $command->execute();
    }
}