<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Xuxiaolong
 * Date: 13-8-6
 * Time: 下午3:21
 * To change this template use File | Settings | File Templates.
 */
class AdminLoginForm extends FormModel
{
    public $userid;
    public $username;
    public $password;
    public $common_url;
    public $action;

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
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'verifyCode'=>'Verification Code',
        );
    }
}
