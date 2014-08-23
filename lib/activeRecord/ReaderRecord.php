<?php

/**
 * This is the model class for table "reader".
 *
 * The followings are the available columns in table 'reader':
 * @property integer $user_id
 * @property string $name
 * @property string $gender
 * @property string $E_mail
 * @property string $mobile
 * @property string $department
 * @property string $birthday
 * @property string $city
 * @property string $seat
 * @property string $reader
 * @property string $password
 * @property string $image_url
 * @property string $user_motto
 * @property string $band
 * @property integer $MapID
 */
class ReaderRecord extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ReaderRecord the static model class
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
		return 'reader';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id , MapID', 'numerical', 'integerOnly'=>true),
			array('name, birthday, city, seat', 'length', 'max'=>20),
			array('gender', 'length', 'max'=>2),
            array('user_motto', 'length', 'max'=>1000),
            array('image_url', 'length', 'max'=>100),
            array('band', 'length', 'max'=>10),
			array('E_mail, department, password', 'length', 'max'=>50),
			array('mobile', 'length', 'max'=>11),
			array('reader', 'length', 'max'=>1),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('user_id, name, gender, E_mail, mobile, department, birthday, city, seat, reader, password, image_url, band, user_motto, MapID', 'safe', 'on'=>'search'),
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
			'user_id' => 'User',
			'name' => 'Name',
			'gender' => 'Gender',
			'E_mail' => 'E Mail',
			'mobile' => 'Mobile',
			'department' => 'Department',
			'birthday' => 'Birthday',
			'city' => 'City',
			'seat' => 'Seat',
			'reader' => 'Reader',
			'password' => 'Password',
            'image_url'=> 'Image Url',
            'band' => 'Band',
            'user_motto' => 'User Motto',
            'MapID' => 'MapId',
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

		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('gender',$this->gender,true);
		$criteria->compare('E_mail',$this->E_mail,true);
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('department',$this->department,true);
		$criteria->compare('birthday',$this->birthday,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('seat',$this->seat,true);
		$criteria->compare('reader',$this->reader,true);
		$criteria->compare('password',$this->password,true);
        $criteria->compare('image_url',$this->image_url);
        $criteria->compare('band',$this->band);
        $criteria->compare('user_motto',$this->user_motto);
        $criteria->compare('MapID',$this->MapID);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}