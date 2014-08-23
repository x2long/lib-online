<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property string $username
 * @property string $usergender
 * @property string $userNumber
 * @property integer $userID
 * @property string $userdepart
 * @property string $usermailbox
 * @property string $usermobile
 * @property string $userseat
 * @property string $usercity
 * @property integer $pwdd
 */
class user extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return user the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userID, pwdd', 'numerical', 'integerOnly'=>true),
			array('username, usergender, userdepart, usermailbox, userseat, usercity', 'length', 'max'=>255),
			array('userNumber, usermobile', 'length', 'max'=>11),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('username, usergender, userNumber, userID, userdepart, usermailbox, usermobile, userseat, usercity, pwdd', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'username' => 'Username',
			'usergender' => 'Usergender',
			'userNumber' => 'User Number',
			'userID' => 'User',
			'userdepart' => 'Userdepart',
			'usermailbox' => 'Usermailbox',
			'usermobile' => 'Usermobile',
			'userseat' => 'Userseat',
			'usercity' => 'Usercity',
			'pwdd' => 'Pwdd',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('username',$this->username,true);
		$criteria->compare('usergender',$this->usergender,true);
		$criteria->compare('userNumber',$this->userNumber,true);
		$criteria->compare('userID',$this->userID);
		$criteria->compare('userdepart',$this->userdepart,true);
		$criteria->compare('usermailbox',$this->usermailbox,true);
		$criteria->compare('usermobile',$this->usermobile,true);
		$criteria->compare('userseat',$this->userseat,true);
		$criteria->compare('usercity',$this->usercity,true);
		$criteria->compare('pwdd',$this->pwdd);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}