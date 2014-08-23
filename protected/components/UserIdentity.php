<?php
/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	
	private $_id;
	
	/**
	 * Authenticates a user.
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		$reader_helper = new ReaderHelper();
        $user_id=$this->username;
        $password=$this->password;

		$reader = $reader_helper->find('user_id = ?', array($user_id));
		
		if(count($reader) != 0) {
            $reader_validate = $reader_helper->validate_password($password,$reader);
            if($reader_validate){
                $this->_id = $reader->user_id;
                $this->setState('name', $reader->name);
                $this->setState('defaultUrl', $this->getDefaultUrl());
                return true;
            }else{
                return false;
            }
	    }
       	else {
			return FALSE;
        }
	}

	public function getId()
	{
		return $this->_id;
	}
	
	public function getDefaultUrl() {
        return Yii::app()->getBaseUrl()."/library";
    }
}
