<?php
/**
 * Created by JetBrains PhpStorm.
 * User: xuxiaolong
 * Date: 13-7-26
 * Time: 下午8:01
 * To change this template use File | Settings | File Templates.
 */
class ReaderLoginForm extends FormModel
{
    public $userid;
    public $username;
    public $password;
    public $message;
    public $returnUrl;
    public $contentUrl;
    public $content;
    public $homepage;
    public $errorInfo = "";

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            // username, password are required
            array('userid, password', 'required'),
            array('userid, returnUrl','safe')
        );
    }


    /**
     *处理登录过程
     */
    public function processLogin(){
        if($this->userid != NULL && $this->password != NULL)
        {
            $authenticate = new UserIdentity($this->userid,$this->password);
            if ($authenticate->authenticate())//如果验证通过
            {
                Yii::app()->user->login($authenticate, 3600*24*7);//存入session
                return true;
            }
            else
            {
                $this->message = '用户名密码错误';
                return false;
            }
        }
    }

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels(){
        return array(
            'verifyCode'=>'Verification Code',
        );
    }
}
