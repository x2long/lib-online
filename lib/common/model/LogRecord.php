<?php

/**
 * This is the model class for table "log".
 *
 * The followings are the available columns in table 'log':
 * @property string $id
 * @property string $action
 * @property string $owner
 * @property string $time
 * @property string $ip
 * @property string $info
 *
 * The followings are the available model relations:
 */
class LogRecord extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return LogRecord the static model class
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
		return 'log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('action', 'length', 'max'=>30),
			array('owner, ip', 'length', 'max'=>50),
			array('info', 'length', 'max'=>1024),
			array('time', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, action, owner, time, ip, info', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'action' => 'Action',
			'owner' => 'Owner',
			'time' => 'Time',
			'ip' => 'Ip',
			'info' => 'Info',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('action',$this->action,true);
		$criteria->compare('owner',$this->owner,true);
		$criteria->compare('time',$this->time,true);
		$criteria->compare('ip',$this->ip,true);
		$criteria->compare('info',$this->info,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}
