<?php

/**
 * This is the model class for table "reserve".
 *
 * The followings are the available columns in table 'reserve':
 * @property integer $reserve_id
 * @property integer $user_id
 * @property string $order_time
 * @property integer $book_id
 * @property string $info_time
 */
class ReserveRecord extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ReserveRecord the static model class
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
		return 'reserve';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, book_id', 'numerical', 'integerOnly'=>true),
			array('order_time, info_time', 'length', 'max'=>60),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('reserve_id, user_id, order_time, book_id, info_time', 'safe', 'on'=>'search'),
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
			'reserve_id' => 'Reserve',
			'user_id' => 'User',
			'order_time' => 'Order Time',
			'book_id' => 'Book',
			'info_time' => 'Info Time',
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

		$criteria->compare('reserve_id',$this->reserve_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('order_time',$this->order_time,true);
		$criteria->compare('book_id',$this->book_id);
		$criteria->compare('info_time',$this->info_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}