<?php

/**
 * This is the model class for table "comment".
 *
 * The followings are the available columns in table 'comment':
 * @property integer $comment_id
 * @property integer $book_id
 * @property string $comment_name
 * @property string $comment_date
 * @property string $comment_content
 * @property string $comment_author
 * @property string $comment_level
 * @property string $comment_email
 */
class CommentRecord extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return commentRecord the static model class
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
		return 'comment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('book_id', 'numerical', 'integerOnly'=>true),
			array('comment_name, comment_author, comment_email', 'length', 'max'=>60),
			array('comment_date', 'length', 'max'=>14),
			array('comment_content', 'length', 'max'=>10000),
			array('comment_level', 'length', 'max'=>1),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('comment_id, book_id, comment_name, comment_date, comment_content, comment_author, comment_level, comment_email', 'safe', 'on'=>'search'),
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
			'comment_id' => 'Comment',
			'book_id' => 'Book',
			'comment_name' => 'Comment Name',
			'comment_date' => 'Comment Date',
			'comment_content' => 'Comment Content',
			'comment_author' => 'Comment Author',
			'comment_level' => 'Comment Level',
			'comment_email' => 'Comment Email',
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

		$criteria->compare('comment_id',$this->comment_id);
		$criteria->compare('book_id',$this->book_id);
		$criteria->compare('comment_name',$this->comment_name,true);
		$criteria->compare('comment_date',$this->comment_date,true);
		$criteria->compare('comment_content',$this->comment_content,true);
		$criteria->compare('comment_author',$this->comment_author,true);
		$criteria->compare('comment_level',$this->comment_level,true);
		$criteria->compare('comment_email',$this->comment_email,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
