<?php
/**
 * Created by JetBrains PhpStorm.
 * User: AlbertLy
 * Date: 13-5-9
 * Time: 上午9:10
 * To change this template use File | Settings | File Templates.
 */
//初始化curl
include "simple_html_dom.php" ;
$ch = curl_init();
//设置URL参数
curl_setopt($ch,CURLOPT_URL,"http://book.douban.com/subject/6847546/");
//要求CURL返回数据
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
//执行请求
$result = curl_exec($ch);
$result=preg_replace('/<a[^>]*?>/',"",$result);
$result=preg_replace('/<\/a>/',"",$result);
$html = new simple_html_dom();
$html->load($result);
$div=$html->find('div#info',0)->innertext;
$pattern="/<span class=\"pl\">(.*?)<\/span>(.*?)<br\/>/i";
//preg_match_all($pattern,$result,$matches);
//curl_close($ch);
//strip_tags(string,allow)
//trim(string,charlist)
$matches = array();
var_dump($div);

if(preg_match_all($pattern,$div,$matches)){
    var_dump($matches);
}
$testarray=array("作者"=>"糯米橙子","定价"=>"26.80元");
$book=array("a"=>$testarray,"b"=>"B");
var_dump($book);
$v="作者";
echo $testarray[$v];

echo "\n";