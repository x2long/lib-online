<?php

/**
 * This is the model class for table "stats".
 *
 * The followings are the available columns in table 'stats':
 * @property integer $stats_id
 * @property integer $user_id
 * @property integer $recommend_id
 * @property integer $recommend_num
 */
class StatsRecord extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return StatsRecord the static model class
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
		return 'stats';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('stats_id, user_id, recommend_id, recommend_num', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('stats_id, user_id, recommend_id, recommend_num', 'safe', 'on'=>'search'),
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
			'stats_id' => 'Stats',
			'user_id' => 'User',
			'recommend_id' => 'Recommend',
			'recommend_num' => 'Recommend Num',
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

		$criteria->compare('stats_id',$this->stats_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('recommend_id',$this->recommend_id);
		$criteria->compare('recommend_num',$this->recommend_num);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}