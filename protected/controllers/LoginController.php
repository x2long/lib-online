<?php
/**
 * Created by JetBrains PhpStorm.
 * User: xuxiaolong
 * Date: 13-7-9
 * Time: 上午11:48
 * To change this template use File | Settings | File Templates.
 */
class LoginController extends EbuptController{
    public $model;
    public $layout = '';
    public function filters() {
        return array();
    }

    /**
     * login function and show page
     */
    public function actionIndex(){
        if(!Yii::app()->user->isGuest){
           // var_dump(Yii::app()->user->defaultUrl);
           $this->redirect(Yii::app()->user->defaultUrl);
        }
        $model = new ReaderLoginForm(); //Reader
        $model->errorInfo='';
        $useReturnUrl = false;
        if( isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] ){
            $model->upUrl = $_SERVER['HTTP_REFERER'];
            $useReturnUrl = true;
            if(!isset($_POST['userid'])){
                Yii::app()->user->returnUrl = $model->upUrl;
            }
        }
        if(isset($_POST["userid"])) {
            $model->attributes = $_POST;
            // validate user input and redirect to the previous page if valid
            if ( $model->validate() && $model->processLogin() ){
                // 按以下优先级决定检查地址：
                // 1.如果model->upUrl有效，则用之；
                // 2.如果以上都不符合，则按照用户的权限决定其登录以后的页面。
                if ( $useReturnUrl){
                    $nextUrl = Yii::app()->user->returnUrl;
                }
                else {
                    $nextUrl = Yii::app()->user->defaultUrl;
                }

                // 重定向到上面决定的页面
                if(Yii::app()->user->name =="Eblib-admin")
                    $nextUrl=Yii::app()->baseUrl."/admin";
                $this->redirect($nextUrl);
            }
            else {
                // 记录登录错误的次数(into $_SESSION)
                $errCount = Yii::app()->user->getState('loginError');
                ++$errCount;
                Yii::app()->user->setState('loginError', $errCount);
                $model->errorInfo = "您输入的用户名或密码错误！";
            }
        } else {
            $model->errorInfo = "";
        }
        $this->renderSmarty('login/index.html',array('model' => $model));
    }

    /**
     * logout function
     */
    public function actionLogout(){
        Yii::app()->user->logout();//注销session
        $this->redirect(Yii::app()->getBaseUrl().'/library');
    }

    /**
     * 重置密码
     */

    public function actionResetPassWord(){
        try {
            if(isset($_POST["email"]) && $_POST["email"]) {
                $url = "http://".$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"]."/yiieblib/web";
                $email = $_POST['email'];
                $reader = ReaderRecord::model()->find('E_mail = ?', array($email));

                if(empty($reader)){
                    echo "<script>alert('对不起，Reader表中不存在E-mail: ".$email."对应的信息，无法修改密码，请您确认E-mail，如果仍有问题，请联系管理员(houyihan@ebupt.com)添加您的记录到图书馆系统中！');window.location.href='".$url."';</script>";
                    return;
                }

                $reader->MapID = time();
                $user_id = $reader->user_id;
                if(!$reader->save()) {
                    echo "<script>alert('对不起，修改密码临时状态休息保存失败！请联系管理员(houyihan@ebupt.com)！')</script>";
                    return;
                }

                $model = new LoginForm;
                $model->url = $url."/login/resetPW/yojg/".$user_id."/micn/".md5($user_id.$reader->MapID.$reader->MapID);
                $model->readername = $reader->name;
                if($model->sendMail('resetPassword',$reader->E_mail)) {
                    echo "<script>alert('邮件发送成功！请查收邮件，点击重置链接完成密码重置操作。');window.location.href='".$url."';</script>";
                    return;
                } else {
                    echo "<script>alert('对不起，邮件发送失败！请联系管理员(houyihan@ebupt.com)！');window.location.href='".$url."/longin/resetPassword"."';</script>";
                    return;
                }
            }

            $this->renderSmarty('login/reset_password.html');
        } catch(Exception $e) {
            echo "<script>alert('对不起，".$e->getMessage()."，暂时不能修改密码！请联系管理员(houyihan@ebupt.com)！');</script>";
            return;
        }
    }

    /**
     * 根据url重置密码
     */
    public function actionResetPW() {
        $url = "http://".$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"]."/yiieblib/web";

        if(isset($_GET["yojg"]) && $_GET["yojg"] && isset($_GET["micn"]) && $_GET["micn"]) {
            $user_id = $_GET["yojg"];
            $reader = ReaderRecord::model()->find("user_id=".$user_id);

            //md5($user_id.$reader->MapID.$reader->MapID)
            $micn = md5($user_id.$reader->MapID.$reader->MapID);
            if($reader->MapID==null || (time()-$reader->MapID>86400) || $_GET["micn"]!=$micn) {
                echo "<script>alert('对不起，您的密码重置密钥已过期，请重新申请重置密码。');window.location.href='".$url."/login/resetPassword"."';</script>";
                return;
            }

            if($reader->MapID > 0) {
                $reader->password = "123456";
                $reader->MapID = null;
                Yii::app()->user->logout();
                if($reader->save()) {
                    echo "<script>alert('恭喜您，密码重置成功！请用密码123456及时登录系统修改新密码。');window.location.href='".$url."/login"."';</script>";
                    return;
                }
            }
        }
        echo "<script>alert('对不起，密码重置失败！请重试。必要时请联系管理员(houyihan@ebupt.com)！');window.location.href='".$url."/login/resetPassword"."';</script>";
        return;
    }

    /**
     * 检验原密码是否正确
     */
    public function actionValidatePassword($password) {
        $data = ($this->validate_password($password)) ? 'yes' : 'no';
        $this->renderJson($data);
    }

    /**
     * 修改密码
     */
    public function actionUpdatePassword($pre_password, $new_password) {
        try{
            $reader_helper = new ReaderHelper();
            $reader = $reader_helper->find("user_id=".Yii::app()->user->id);
            $data = "no";
            if(isset($reader->password)) {
                if($reader_helper->validate_password($pre_password,$reader)){
                    $connection = Yii::app()->db;
                    $new_password = $reader_helper->do_hash($new_password);
                    $sql = "UPDATE reader SET reader.password = '".$new_password."' where reader.user_id = '".$reader->user_id."'";
                    $command = $connection->createCommand($sql);
                    $command->execute();
                    $data = "yes";
                }
            }
            $this->renderJson($data);
        } catch(Exception $e) {
            var_dump($e->getMessage());
        }
    }

    private function validate_password($password) {
        $reader_helper = new ReaderHelper();
        $reader = $reader_helper->find("user_id=".Yii::app()->user->id);
        return $reader_helper->validate_password($password,$reader);
    }

}
