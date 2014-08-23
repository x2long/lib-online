<?php

/**
 * This is the model class for table "history".
 *
 * The followings are the available columns in table 'history':
 * @property integer $history_id
 * @property integer $book_id
 * @property integer $user_id
 * @property string $borrow_date
 * @property string $is_return
 */
class HistoryRecord extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return HistoryRecord the static model class
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
		return 'history';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('book_id, user_id', 'numerical', 'integerOnly'=>true),
			array('borrow_date', 'length', 'max'=>60),
			array('is_return', 'length', 'max'=>1),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('history_id, book_id, user_id, borrow_date, is_return', 'safe', 'on'=>'search'),
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
			'history_id' => 'History',
			'book_id' => 'Book',
			'user_id' => 'User',
			'borrow_date' => 'Borrow Date',
			'is_return' => 'Is Return',
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

		$criteria->compare('history_id',$this->history_id);
		$criteria->compare('book_id',$this->book_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('borrow_date',$this->borrow_date,true);
		$criteria->compare('is_return',$this->is_return,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}