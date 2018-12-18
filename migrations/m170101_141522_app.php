<?php
/**
 * This file is part of CMSGears Framework. Please view License file distributed
 * with the source code for license details.
 *
 * @link https://www.cmsgears.org/
 * @copyright Copyright (c) 2015 VulpineCode Technologies Pvt. Ltd.
 */

// CMG Imports
use cmsgears\core\common\models\base\Meta;

/**
 * The app migration inserts the database tables of app module. It also insert the foreign
 * keys if FK flag of migration component is true.
 *
 * @since 1.0.0
 */
class m170101_141522_app extends \cmsgears\core\common\base\Migration {

	// Public Variables

	public $fk;
	public $options;

	// Private Variables

	private $prefix;

	public function init() {

		// Table prefix
		$this->prefix = Yii::$app->migration->cmgPrefix;

		// Get the values via config
		$this->fk		= Yii::$app->migration->isFk();
		$this->options	= Yii::$app->migration->getTableOptions();

		// Default collation
		if( $this->db->driverName === 'mysql' ) {

			$this->options = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
		}
	}

	public function up() {

		// Application
		$this->upApp();
		$this->upAppMeta();
		$this->upAppFollower();

		if( $this->fk ) {

			$this->generateForeignKeys();
		}
	}

	private function upApp() {

		$this->createTable( $this->prefix . 'app', [
			'id' => $this->bigPrimaryKey( 20 ),
			'siteId' => $this->bigInteger( 20 ),
			'themeId' => $this->bigInteger( 20 ),
			'createdBy' => $this->bigInteger( 20 )->notNull(),
			'modifiedBy' => $this->bigInteger( 20 ),
			'name' => $this->string( Yii::$app->core->xLargeText )->notNull(),
			'slug' => $this->string( Yii::$app->core->xxLargeText )->notNull(),
			'type' => $this->smallInteger( 6 )->defaultValue( 0 ),
			'icon' => $this->string( Yii::$app->core->largeText )->defaultValue( null ),
			'title' => $this->string( Yii::$app->core->xxxLargeText )->defaultValue( null ),
			'description' => $this->string( Yii::$app->core->xtraLargeText )->defaultValue( null ),
			'authType' => $this->smallInteger( 6 )->defaultValue( 0 ),
			'status' => $this->smallInteger( 6 )->defaultValue( 0 ),
			'createdAt' => $this->dateTime()->notNull(),
			'modifiedAt' => $this->dateTime(),
			'content' => $this->mediumText(),
			'data' => $this->mediumText(),
			'gridCache' => $this->longText(),
			'gridCacheValid' => $this->boolean()->notNull()->defaultValue( false ),
			'gridCachedAt' => $this->dateTime()
		], $this->options );

		// Index for columns site, theme, creator and modifier
		$this->createIndex( 'idx_' . $this->prefix . 'app_site', $this->prefix . 'app', 'siteId' );
		$this->createIndex( 'idx_' . $this->prefix . 'app_theme', $this->prefix . 'app', 'themeId' );
		$this->createIndex( 'idx_' . $this->prefix . 'app_creator', $this->prefix . 'app', 'createdBy' );
		$this->createIndex( 'idx_' . $this->prefix . 'app_modifier', $this->prefix . 'app', 'modifiedBy' );
	}

	private function upAppMeta() {

		$this->createTable( $this->prefix . 'app_meta', [
			'id' => $this->bigPrimaryKey( 20 ),
			'modelId' => $this->bigInteger( 20 )->notNull(),
			'name' => $this->string( Yii::$app->core->xLargeText )->notNull(),
			'label' => $this->string( Yii::$app->core->xxLargeText )->notNull(),
			'type' => $this->string( Yii::$app->core->mediumText ),
			'active' => $this->boolean()->defaultValue( false ),
			'order' => $this->smallInteger( 6 )->defaultValue( 0 ),
			'valueType' => $this->string( Yii::$app->core->mediumText )->notNull()->defaultValue( Meta::VALUE_TYPE_TEXT ),
			'value' => $this->text(),
			'data' => $this->mediumText()
		], $this->options );

		// Index for columns parent
		$this->createIndex( 'idx_' . $this->prefix . 'app_meta_parent', $this->prefix . 'app_meta', 'modelId' );
	}

	private function upAppFollower() {

        $this->createTable( $this->prefix . 'app_follower', [
			'id' => $this->bigPrimaryKey( 20 ),
			'modelId' => $this->bigInteger( 20 )->notNull(),
			'parentId' => $this->bigInteger( 20 )->notNull(),
			'type' => $this->smallInteger( 6 )->defaultValue( 0 ),
			'active' => $this->boolean()->notNull()->defaultValue( false ),
			'createdAt' => $this->dateTime()->notNull(),
			'modifiedAt' => $this->dateTime(),
			'data' => $this->mediumText()
        ], $this->options );

        // Index for columns user and model
		$this->createIndex( 'idx_' . $this->prefix . 'app_follower_user', $this->prefix . 'app_follower', 'modelId' );
		$this->createIndex( 'idx_' . $this->prefix . 'app_follower_parent', $this->prefix . 'app_follower', 'parentId' );
	}

	private function generateForeignKeys() {

		// App
		$this->addForeignKey( 'fk_' . $this->prefix . 'app_site', $this->prefix . 'app', 'siteId', $this->prefix . 'core_site', 'id', 'RESTRICT' );
		$this->addForeignKey( 'fk_' . $this->prefix . 'app_theme', $this->prefix . 'app', 'siteId', $this->prefix . 'core_theme', 'id', 'RESTRICT' );
		$this->addForeignKey( 'fk_' . $this->prefix . 'app_creator', $this->prefix . 'app', 'createdBy', $this->prefix . 'core_user', 'id', 'RESTRICT' );
		$this->addForeignKey( 'fk_' . $this->prefix . 'app_modifier', $this->prefix . 'app', 'modifiedBy', $this->prefix . 'core_user', 'id', 'SET NULL' );

		// App meta
		$this->addForeignKey( 'fk_' . $this->prefix . 'app_meta_parent', $this->prefix . 'app_meta', 'modelId', $this->prefix . 'app', 'id', 'CASCADE' );

		// App Follower
        $this->addForeignKey( 'fk_' . $this->prefix . 'app_follower_user', $this->prefix . 'app_follower', 'modelId', $this->prefix . 'core_user', 'id', 'CASCADE' );
		$this->addForeignKey( 'fk_' . $this->prefix . 'app_follower_parent', $this->prefix . 'app_follower', 'parentId', $this->prefix . 'app', 'id', 'CASCADE' );
	}

	public function down() {

		if( $this->fk ) {

			$this->dropForeignKeys();
		}

		$this->dropTable( $this->prefix . 'app' );
		$this->dropTable( $this->prefix . 'app_meta' );
		$this->dropTable( $this->prefix . 'app_follower' );
	}

	private function dropForeignKeys() {

		// App
		$this->dropForeignKey( 'fk_' . $this->prefix . 'app_theme', $this->prefix . 'app' );
		$this->dropForeignKey( 'fk_' . $this->prefix . 'app_creator', $this->prefix . 'app' );
		$this->dropForeignKey( 'fk_' . $this->prefix . 'app_modifier', $this->prefix . 'app' );

		// App meta
		$this->dropForeignKey( 'fk_' . $this->prefix . 'app_meta_parent', $this->prefix . 'app_meta' );

		// App Follower
        $this->dropForeignKey( 'fk_' . $this->prefix . 'app_follower_user', $this->prefix . 'app_follower' );
		$this->dropForeignKey( 'fk_' . $this->prefix . 'app_follower_parent', $this->prefix . 'app_follower' );
	}

}
