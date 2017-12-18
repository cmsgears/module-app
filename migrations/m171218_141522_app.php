<?php

class m171218_141522_app extends \yii\db\Migration {

	// Public Variables

	public $fk;
	public $options;

	// Private Variables

	private $prefix;

	public function init() {

		// Table prefix
		$this->prefix		= Yii::$app->migration->cmgPrefix;

		// Get the values via config
		$this->fk			= Yii::$app->migration->isFk();
		$this->options		= Yii::$app->migration->getTableOptions();

		// Default collation
		if( $this->db->driverName === 'mysql' ) {

			$this->options = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
		}
	}

	public function up() {

		// Application
		$this->upApp();

		if( $this->fk ) {

			$this->generateForeignKeys();
		}
	}

	private function upApp() {

		$this->createTable( $this->prefix . 'app', [
			'id' => $this->bigPrimaryKey( 20 ),
			'themeId' => $this->bigInteger( 20 ),
			'createdBy' => $this->bigInteger( 20 )->notNull(),
			'modifiedBy' => $this->bigInteger( 20 ),
			'name' => $this->string( Yii::$app->core->xLargeText )->notNull(),
			'slug' => $this->string( Yii::$app->core->xxLargeText )->notNull(),
			'title' => $this->string( Yii::$app->core->xxxLargeText )->defaultValue( null ),
			'description' => $this->string( Yii::$app->core->xtraLargeText )->defaultValue( null ),
			'status' => $this->smallInteger( 6 )->defaultValue( 0 ),
			'visibility' => $this->smallInteger( 6 )->defaultValue( 0 ),
			'createdAt' => $this->dateTime()->notNull(),
			'modifiedAt' => $this->dateTime(),
			// Cached content, JSON data and cached widget JSON data
			'content' => $this->text(),
			'data' => $this->text(),
			'widgetData' => $this->text()
		], $this->options );

		// Index for columns site, parent, creator and modifier
		$this->createIndex( 'idx_' . $this->prefix . 'app_theme', $this->prefix . 'app', 'themeId' );
		$this->createIndex( 'idx_' . $this->prefix . 'app_creator', $this->prefix . 'app', 'createdBy' );
		$this->createIndex( 'idx_' . $this->prefix . 'app_modifier', $this->prefix . 'app', 'modifiedBy' );
	}

	private function generateForeignKeys() {

		// App
		$this->addForeignKey( 'fk_' . $this->prefix . 'app_theme', $this->prefix . 'app', 'siteId', $this->prefix . 'core_theme', 'id', 'RESTRICT' );
		$this->addForeignKey( 'fk_' . $this->prefix . 'app_creator', $this->prefix . 'app', 'createdBy', $this->prefix . 'core_user', 'id', 'RESTRICT' );
		$this->addForeignKey( 'fk_' . $this->prefix . 'app_modifier', $this->prefix . 'app', 'modifiedBy', $this->prefix . 'core_user', 'id', 'SET NULL' );
	}

	public function down() {

		if( $this->fk ) {

			$this->dropForeignKeys();
		}

		$this->dropTable( $this->prefix . 'app' );
	}

	private function dropForeignKeys() {

		// App
		$this->dropForeignKey( 'fk_' . $this->prefix . 'app_theme', $this->prefix . 'app' );
		$this->dropForeignKey( 'fk_' . $this->prefix . 'app_creator', $this->prefix . 'app' );
		$this->dropForeignKey( 'fk_' . $this->prefix . 'app_modifier', $this->prefix . 'app' );
	}
}
