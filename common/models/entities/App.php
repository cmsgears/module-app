<?php
namespace cmsgears\app\common\models\entities;

// Yii Imports
use Yii;
use yii\helpers\ArrayHelper;
use yii\behaviors\SluggableBehavior;

// CMG Imports
use cmsgears\core\common\config\CoreGlobal;

use cmsgears\app\common\models\base\AppTables;

use cmsgears\core\common\models\traits\NameTrait;
use cmsgears\core\common\models\traits\SlugTrait;
use cmsgears\core\common\models\traits\resources\DataTrait;
use cmsgears\core\common\models\traits\resources\VisualTrait;

/**
 * App Entity
 *
 * @property long $id
 * @property long $themeId
 * @property string $name
 * @property string $slug
 * @property string $title
 * @property string $description
 * @property int $status
 * @property int $visibility
 * @property datetime $createdAt
 * @property datetime $modifiedAt
 * @property string $content
 * @property string $data
 * @property string $widgetData
 */
class App extends \cmsgears\core\common\models\base\Entity {

	// Variables ---------------------------------------------------

	// Globals -------------------------------

	// Constants --------------

	const STATUS_NEW		=  0;
	const STATUS_ACTIVE		= 10;
	const STATUS_DISABLED	= 20;

	// Public -----------------

	public static $statusMap = array(
		self::STATUS_NEW  => 'New',
		self::STATUS_ACTIVE => 'Active',
		self::STATUS_DISABLED => 'Disabled'
	);

	// Protected --------------

	// Variables -----------------------------

	// Public -----------------

	public $modelType	= CoreGlobal::TYPE_APP;

	// Protected --------------

	// Private ----------------

	// Traits ------------------------------------------------------

	use DataTrait;
	use NameTrait;
	use SlugTrait;
	use VisualTrait;

	// Constructor and Initialisation ------------------------------

	// Instance methods --------------------------------------------

	// Yii interfaces ------------------------

	// Yii parent classes --------------------

	// yii\base\Component -----

	/**
	 * @inheritdoc
	 */
	public function behaviors() {

		return [
			'sluggableBehavior' => [
				'class' => SluggableBehavior::className(),
				'attribute' => 'name',
				'slugAttribute' => 'slug',
				'immutable' => true,
				'ensureUnique' => true
			]
		];
	}

	// yii\base\Model ---------

	/**
	 * @inheritdoc
	 */
	public function rules() {

		// model rules
		$rules = [
			// Required, Safe
			[ [ 'name' ], 'required' ],
			[ [ 'id', 'data', 'content', 'widgetData' ], 'safe' ],
			// Unique
			[ 'name', 'unique' ],
			// Text Limit
			[ 'name', 'string', 'min' => 1, 'max' => Yii::$app->core->xLargeText ],
			[ 'slug', 'string', 'min' => 1, 'max' => Yii::$app->core->xxLargeText ],
			[ 'title', 'string', 'min' => 1, 'max' => Yii::$app->core->xxxLargeText ],
			[ 'description', 'string', 'min' => 1, 'max' => Yii::$app->core->xtraLargeText ],
			// Other
			[ [ 'status', 'visibility' ], 'number', 'integerOnly' => true, 'min' => 0 ],
			[ [ 'themeId' ], 'number', 'integerOnly' => true, 'min' => 1 ],
			[ [ 'createdAt', 'modifiedAt' ], 'date', 'format' => Yii::$app->formatter->datetimeFormat ]
		];

		// trim if required
		if( Yii::$app->core->trimFieldValue ) {

			$trim[] = [ [ 'name' ], 'filter', 'filter' => 'trim', 'skipOnArray' => true ];

			return ArrayHelper::merge( $trim, $rules );
		}

		return $rules;
	}

	/**attributes
	 * @inheritdoc
	 */
	public function attributeLabels() {

		return [
			'themeId' => Yii::$app->coreMessage->getMessage( CoreGlobal::FIELD_THEME ),
			'name' => Yii::$app->coreMessage->getMessage( CoreGlobal::FIELD_NAME ),
			'slug' => Yii::$app->coreMessage->getMessage( CoreGlobal::FIELD_SLUG ),
			'title' => Yii::$app->coreMessage->getMessage( CoreGlobal::FIELD_TITLE ),
			'status' => Yii::$app->coreMessage->getMessage( CoreGlobal::FIELD_STATUS ),
			'visibility' => Yii::$app->coreMessage->getMessage( CoreGlobal::FIELD_VISIBILITY ),
			'content' => Yii::$app->coreMessage->getMessage( CoreGlobal::FIELD_DATA ),
			'data' => Yii::$app->coreMessage->getMessage( CoreGlobal::FIELD_DATA ),
			'widgetData' => Yii::$app->coreMessage->getMessage( CoreGlobal::FIELD_DATA )
		];
	}

	// CMG interfaces ------------------------

	// CMG parent classes --------------------

	// Validators ----------------------------

	// App -----------------------------------

	public function getTheme() {

		return $this->hasOne( Theme::className(), [ 'id' => 'themeId' ] );
	}

	/**
	 * @return string representation of status
	 */
	public function getStatusStr() {

		return Yii::$app->formatter->asBoolean( $this->active );
	}

	// Static Methods ----------------------------------------------

	// Yii parent classes --------------------

	// yii\db\ActiveRecord ----

	/**
	 * @inheritdoc
	 */
	public static function tableName() {

		return AppTables::TABLE_APP;
	}

	// CMG parent classes --------------------

	// App -----------------------------------

	// Read - Query -----------

	public static function queryWithHasOne( $config = [] ) {

		$modelTable				= AppTables::TABLE_APP;
		$relations				= isset( $config[ 'relations' ] ) ? $config[ 'relations' ] : [ 'theme' ];
		$config[ 'relations' ]	= $relations;
		$config[ 'groups' ]		= isset( $config[ 'groups' ] ) ? $config[ 'groups' ] : [ "$modelTable.id" ];

		return parent::queryWithAll( $config );
	}

	public static function queryWithTheme( $config = [] ) {

		$config[ 'relations' ]	= [ 'avatar', 'banner', 'theme' ];

		return parent::queryWithAll( $config );
	}

	// Read - Find ------------

	// Create -----------------

	// Update -----------------

	// Delete -----------------

}
