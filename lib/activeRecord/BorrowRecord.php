<?php

/**
 * This is the model class for table "borrow".
 *
 * The followings are the available columns in table 'borrow':
 * @property integer $book_id
 * @property integer $user_id
 * @property string $borrow_date
 * @property string $return_date
 * @property integer $renew_num
 */
class BorrowRecord extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return borrowRecord the static model class
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
		return 'borrow';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('book_id, user_id, renew_num', 'numerical', 'integerOnly'=>true),
			array('borrow_date, return_date', 'length', 'max'=>60),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('book_id, user_id, borrow_date, return_date, renew_num', 'safe', 'on'=>'search'),
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
			'book_id' => 'Book',
			'user_id' => 'User',
			'borrow_date' => 'Borrow Date',
			'return_date' => 'Return Date',
			'renew_num' => 'Renew Num',
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

		$criteria->compare('book_id',$this->book_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('borrow_date',$this->borrow_date,true);
		$criteria->compare('return_date',$this->return_date,true);
		$criteria->compare('renew_num',$this->renew_num);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
