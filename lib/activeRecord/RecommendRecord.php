<?php

/**
 * This is the model class for table "recommend".
 *
 * The followings are the available columns in table 'recommend':
 * @property integer $recommend_id
 * @property string $isbn
 * @property string $book_name
 * @property string $version
 * @property string $author
 * @property string $publisher
 * @property string $price
 * @property integer $recommend_num
 * @property string $image_url
 * @property string $book_url
 * @property string $recommender
 * @property string $recommender_email
 * @property string $description
 * @property string $recommend_date
 * @property integer $batch
 */
class RecommendRecord extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return recommendRecord the static model class
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
		return 'recommend';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('recommend_num, batch', 'numerical', 'integerOnly'=>true),
			array('isbn', 'length', 'max'=>13),
			array('book_name, author, price, recommender, recommender_email', 'length', 'max'=>60),
			array('version', 'length', 'max'=>1),
            array('publisher,image_url,book_url', 'length', 'max'=>100),
            array('recommend_date', 'length', 'max'=>14),
			array('description', 'length', 'max'=>10000),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('recommend_id, isbn, book_name, version, author, publisher, price, recommend_num, recommender, recommender_email, description, recommend_date, batch, image_url, book_url', 'safe', 'on'=>'search'),
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
			'recommend_id' => 'Recommend',
			'isbn' => 'Isbn',
			'book_name' => 'Book Name',
			'version' => 'Version',
			'author' => 'Author',
			'publisher' => 'Publisher',
			'price' => 'Price',
			'recommend_num' => 'Recommend Num',
			'recommender' => 'Recommender',
			'recommender_email' => 'Recommender Email',
			'description' => 'Description',
			'recommend_date' => 'Recommend Date',
			'batch' => 'Batch',
            'image_url' => 'Image Url',
            'book_url' => 'Book Url',
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

		$criteria->compare('recommend_id',$this->recommend_id);
		$criteria->compare('isbn',$this->isbn,true);
		$criteria->compare('book_name',$this->book_name,true);
		$criteria->compare('version',$this->version,true);
		$criteria->compare('author',$this->author,true);
		$criteria->compare('publisher',$this->publisher,true);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('recommend_num',$this->recommend_num);
		$criteria->compare('recommender',$this->recommender,true);
		$criteria->compare('recommender_email',$this->recommender_email,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('recommend_date',$this->recommend_date,true);
		$criteria->compare('batch',$this->batch);
        $criteria->compare('image_url',$this->image_url,true);
        $criteria->compare('book_url',$this->book_url,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
