<?php

/**
 * This is the model class for table "book".
 *
 * The followings are the available columns in table 'book':
 * @property integer $book_id
 * @property string $book_name
 * @property string $isbn
 * @property string $author
 * @property string $category
 * @property string $pages
 * @property string $publisher
 * @property string $publish_date
 * @property string $import_date
 * @property string $price
 * @property string $status
 * @property string $description
 * @property string $image_url
 */
class BookRecord extends ActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return BookRecord the static model class
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
		return 'book';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('book_name, author, category, pages, publisher, publish_date, import_date', 'length', 'max'=>60),
			array('isbn', 'length', 'max'=>13),
			array('price', 'length', 'max'=>10),
			array('status', 'length', 'max'=>1),
			array('description', 'length', 'max'=>1000),
			array('image_url', 'length', 'max'=>100),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('book_id, book_name, isbn, author, category, pages, publisher, publish_date, import_date, price, status, description, image_url', 'safe', 'on'=>'search'),
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
			'book_name' => 'Book Name',
			'isbn' => 'Isbn',
			'author' => 'Author',
			'category' => 'Category',
			'pages' => 'Pages',
			'publisher' => 'Publisher',
			'publish_date' => 'Publish Date',
			'import_date' => 'Import Date',
			'price' => 'Price',
			'status' => 'Status',
			'description' => 'Description',
			'image_url' => 'Image Url',
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
		$criteria->compare('book_name',$this->book_name,true);
		$criteria->compare('isbn',$this->isbn,true);
		$criteria->compare('author',$this->author,true);
		$criteria->compare('category',$this->category,true);
		$criteria->compare('pages',$this->pages,true);
		$criteria->compare('publisher',$this->publisher,true);
		$criteria->compare('publish_date',$this->publish_date,true);
		$criteria->compare('import_date',$this->import_date,true);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('image_url',$this->image_url,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}