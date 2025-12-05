<?php

namespace Yoast\WP\Lib;

use Exception;
use JsonSerializable;
use ReturnTypeWillChange;

/**
 * Make Model compatible with WordPress.
 *
 * Model base class. Your model objects should extend
 * this class. A minimal subclass would look like:
 *
 * class Widget extends Model {
 * }
 */
class Model implements JsonSerializable {

	/**
	 * Default ID column for all models. Can be overridden by adding
	 * a public static $id_column property to your model classes.
	 *
	 * @var string
	 */
	public const DEFAULT_ID_COLUMN = 'id';

	/**
	 * Default foreign key suffix used by relationship methods.
	 *
	 * @var string
	 */
	public const DEFAULT_FOREIGN_KEY_SUFFIX = '_id';

	/**
	 * Set a prefix for model names. This can be a namespace or any other
	 * abitrary prefix such as the PEAR naming convention.
	 *
	 * @example Model::$auto_prefix_models = 'MyProject_MyModels_'; //PEAR
	 * @example Model::$auto_prefix_models = '\MyProject\MyModels\'; //Namespaces
	 *
	 * @var string
	 */
	public static $auto_prefix_models = '\Yoast\WP\SEO\Models\\';

	/**
	 * Set true to to ignore namespace information when computing table names
	 * from class names.
	 *
	 * @example Model::$short_table_names = true;
	 * @example Model::$short_table_names = false; // default
	 *
	 * @var bool
	 */
	public static $short_table_names = false;

	/**
	 * The ORM instance used by this model instance to communicate with the database.
	 *
	 * @var ORM
	 */
	public $orm;

	/**
	 * The table name for the implemented Model.
	 *
	 * @var string
	 */
	public static $table;

	/**
	 * Whether or not this model uses timestamps.
	 *
	 * @var bool
	 */
	protected $uses_timestamps = false;

	/**
	 * Which columns contain boolean values.
	 *
	 * @var array
	 */
	protected $boolean_columns = [];

	/**
	 * Which columns contain int values.
	 *
	 * @var array
	 */
	protected $int_columns = [];

	/**
	 * Which columns contain float values.
	 *
	 * @var array
	 */
	protected $float_columns = [];

	/**
	 * Hacks around the Model to provide WordPress prefix to tables.
	 *
	 * @param string $class_name   Type of Model to load.
	 * @param bool   $yoast_prefix Optional. True to prefix the table name with the Yoast prefix.
	 *
	 * @return ORM Wrapper to use.
	 */
	public static function of_type( $class_name, $yoast_prefix = true ) {
		// Prepend namespace to the class name.
		$class = static::$auto_prefix_models . $class_name;

		// Set the class variable to the custom value based on the WPDB prefix.
		$class::$table = static::get_table_name( $class_name, $yoast_prefix );

		return static::factory( $class_name, null );
	}

	/**
	 * Creates a model without the Yoast prefix.
	 *
	 * @param string $class_name Type of Model to load.
	 *
	 * @return ORM
	 */
	public static function of_wp_type( $class_name ) {
		return static::of_type( $class_name, false );
	}

	/**
	 * Exposes method to get the table name to use.
	 *
	 * @param string $table_name   Simple table name.
	 * @param bool   $yoast_prefix Optional. True to prefix the table name with the Yoast prefix.
	 *
	 * @return string Prepared full table name.
	 */
	public static function get_table_name( $table_name, $yoast_prefix = true ) {
		global $wpdb;

		// Allow the use of WordPress internal tables.
		if ( $yoast_prefix ) {
			$table_name = 'yoast_' . $table_name;
		}

		return $wpdb->prefix . \strtolower( $table_name );
	}

	/**
	 * Sets the table name for the given class name.
	 *
	 * @param string $class_name The class to set the table name for.
	 *
	 * @return void
	 */
	protected function set_table_name( $class_name ) {
		// Prepend namespace to the class name.
		$class = static::$auto_prefix_models . $class_name;

		$class::$table = static::get_table_name( $class_name );
	}

	/**
	 * Retrieve the value of a static property on a class. If the
	 * class or the property does not exist, returns the default
	 * value supplied as the third argument (which defaults to null).
	 *
	 * @param string     $class_name    The target class name.
	 * @param string     $property      The property to get the value for.
	 * @param mixed|null $default_value Default value when property does not exist.
	 *
	 * @return mixed|null The value of the property.
	 */
	protected static function get_static_property( $class_name, $property, $default_value = null ) {
		if ( ! \class_exists( $class_name ) || ! \property_exists( $class_name, $property ) ) {
			return $default_value;
		}

		if ( ! isset( $class_name::${$property} ) ) {
			return $default_value;
		}

		return $class_name::${$property};
	}

	/**
	 * Static method to get a table name given a class name.
	 * If the supplied class has a public static property
	 * named $table, the value of this property will be
	 * returned.
	 *
	 * If not, the class name will be converted using
	 * the class_name_to_table_name() method.
	 *
	 * If Model::$short_table_names == true or public static
	 * property $table_use_short_name == true then $class_name passed
	 * to class_name_to_table_name() is stripped of namespace information.
	 *
	 * @param string $class_name The class name to get the table name for.
	 *
	 * @return string The table name.
	 */
	protected static function get_table_name_for_class( $class_name ) {
		$specified_table_name = static::get_static_property( $class_name, 'table' );
		$use_short_class_name = static::use_short_table_name( $class_name );
		if ( $use_short_class_name ) {
			$exploded_class_name = \explode( '\\', $class_name );
			$class_name          = \end( $exploded_class_name );
		}

		if ( $specified_table_name === null ) {
			return static::class_name_to_table_name( $class_name );
		}

		return $specified_table_name;
	}

	/**
	 * Should short table names, disregarding class namespaces, be computed?
	 *
	 * $class_property overrides $global_option, unless $class_property is null.
	 *
	 * @param string $class_name The class name to get short name for.
	 *
	 * @return bool True when short table name should be used.
	 */
	protected static function use_short_table_name( $class_name ) {
		$class_property = static::get_static_property( $class_name, 'table_use_short_name' );

		if ( $class_property === null ) {
			return static::$short_table_names;
		}

		return $class_property;
	}

	/**
	 * Convert a namespace to the standard PEAR underscore format.
	 *
	 * Then convert a class name in CapWords to a table name in
	 * lowercase_with_underscores.
	 *
	 * Finally strip doubled up underscores.
	 *
	 * For example, CarTyre would be converted to car_tyre. And
	 * Project\Models\CarTyre would be project_models_car_tyre.
	 *
	 * @param string $class_name The class name to get the table name for.
	 *
	 * @return string The table name.
	 */
	protected static function class_name_to_table_name( $class_name ) {
		$find         = [
			'/\\\\/',
			'/(?<=[a-z])([A-Z])/',
			'/__/',
		];
		$replacements = [
			'_',
			'_$1',
			'_',
		];

		$class_name = \ltrim( $class_name, '\\' );
		$class_name = \preg_replace( $find, $replacements, $class_name );

		return \strtolower( $class_name );
	}

	/**
	 * Return the ID column name to use for this class. If it is
	 * not set on the class, returns null.
	 *
	 * @param string $class_name The class name to get the ID column for.
	 *
	 * @return string|null The ID column name.
	 */
	protected static function get_id_column_name( $class_name ) {
		return static::get_static_property( $class_name, 'id_column', static::DEFAULT_ID_COLUMN );
	}

	/**
	 * Build a foreign key based on a table name. If the first argument
	 * (the specified foreign key column name) is null, returns the second
	 * argument (the name of the table) with the default foreign key column
	 * suffix appended.
	 *
	 * @param string $specified_foreign_key_name The keyname to build.
	 * @param string $table_name                 The table name to build the key name for.
	 *
	 * @return string The built foreign key name.
	 */
	protected static function build_foreign_key_name( $specified_foreign_key_name, $table_name ) {
		if ( $specified_foreign_key_name !== null ) {
			return $specified_foreign_key_name;
		}

		return $table_name . static::DEFAULT_FOREIGN_KEY_SUFFIX;
	}

	/**
	 * Factory method used to acquire instances of the given class.
	 * The class name should be supplied as a string, and the class
	 * should already have been loaded by PHP (or a suitable autoloader
	 * should exist). This method actually returns a wrapped ORM object
	 * which allows a database query to be built. The wrapped ORM object is
	 * responsible for returning instances of the correct class when
	 * its find_one or find_many methods are called.
	 *
	 * @param string $class_name The target class name.
	 *
	 * @return ORM Instance of the ORM wrapper.
	 */
	public static function factory( $class_name ) {
		$class_name = static::$auto_prefix_models . $class_name;
		$table_name = static::get_table_name_for_class( $class_name );
		$wrapper    = ORM::for_table( $table_name );
		$wrapper->set_class_name( $class_name );
		$wrapper->use_id_column( static::get_id_column_name( $class_name ) );

		return $wrapper;
	}

	/**
	 * Internal method to construct the queries for both the has_one and
	 * has_many methods. These two types of association are identical; the
	 * only difference is whether find_one or find_many is used to complete
	 * the method chain.
	 *
	 * @param string      $associated_class_name                    The associated class name.
	 * @param string|null $foreign_key_name                         The foreign key name in the associated table.
	 * @param string|null $foreign_key_name_in_current_models_table The foreign key in the current models table.
	 *
	 * @return ORM Instance of the ORM.
	 *
	 * @throws Exception When ID of current model has a null value.
	 */
	protected function has_one_or_many( $associated_class_name, $foreign_key_name = null, $foreign_key_name_in_current_models_table = null ) {
		$base_table_name  = static::get_table_name_for_class( static::class );
		$foreign_key_name = static::build_foreign_key_name( $foreign_key_name, $base_table_name );

		/*
		 * Value of foreign_table.{$foreign_key_name} we're looking for. Where foreign_table is the actual
		 * database table in the associated model.
		 */
		if ( $foreign_key_name_in_current_models_table === null ) {
			// Matches foreign_table.{$foreign_key_name} with the value of "{$this->table}.{$this->id()}".
			$where_value = $this->id();
		}
		else {
			// Matches foreign_table.{$foreign_key_name} with "{$this->table}.{$foreign_key_name_in_current_models_table}".
			$where_value = $this->{$foreign_key_name_in_current_models_table};
		}

		return static::factory( $associated_class_name )->where( $foreign_key_name, $where_value );
	}

	/**
	 * Helper method to manage one-to-one relations where the foreign
	 * key is on the associated table.
	 *
	 * @param string      $associated_class_name                    The associated class name.
	 * @param string|null $foreign_key_name                         The foreign key name in the associated table.
	 * @param string|null $foreign_key_name_in_current_models_table The foreign key in the current models table.
	 *
	 * @return ORM Instance of the ORM.
	 *
	 * @throws Exception  When ID of current model has a null value.
	 */
	protected function has_one( $associated_class_name, $foreign_key_name = null, $foreign_key_name_in_current_models_table = null ) {
		return $this->has_one_or_many( $associated_class_name, $foreign_key_name, $foreign_key_name_in_current_models_table );
	}

	/**
	 * Helper method to manage one-to-many relations where the foreign
	 * key is on the associated table.
	 *
	 * @param string      $associated_class_name                    The associated class name.
	 * @param string|null $foreign_key_name                         The foreign key name in the associated table.
	 * @param string|null $foreign_key_name_in_current_models_table The foreign key in the current models table.
	 *
	 * @return ORM Instance of the ORM.
	 *
	 * @throws Exception When ID has a null value.
	 */
	protected function has_many( $associated_class_name, $foreign_key_name = null, $foreign_key_name_in_current_models_table = null ) {
		$this->set_table_name( $associated_class_name );

		return $this->has_one_or_many( $associated_class_name, $foreign_key_name, $foreign_key_name_in_current_models_table );
	}

	/**
	 * Helper method to manage one-to-one and one-to-many relations where
	 * the foreign key is on the base table.
	 *
	 * @param string      $associated_class_name                       The associated class name.
	 * @param string|null $foreign_key_name                            The foreign key in the current models table.
	 * @param string|null $foreign_key_name_in_associated_models_table The foreign key in the associated table.
	 *
	 * @return $this|null Instance of the foreign model.
	 */
	protected function belongs_to( $associated_class_name, $foreign_key_name = null, $foreign_key_name_in_associated_models_table = null ) {
		$this->set_table_name( $associated_class_name );

		$associated_table_name = static::get_table_name_for_class( static::$auto_prefix_models . $associated_class_name );
		$foreign_key_name      = static::build_foreign_key_name( $foreign_key_name, $associated_table_name );
		$associated_object_id  = $this->{$foreign_key_name};

		if ( $foreign_key_name_in_associated_models_table === null ) {
			/*
			 * Comparison: "{$associated_table_name}.primary_key = {$associated_object_id}".
			 *
			 * NOTE: primary_key is a placeholder for the actual primary key column's name in $associated_table_name.
			 */
			return static::factory( $associated_class_name )->where_id_is( $associated_object_id );
		}

		// Comparison: "{$associated_table_name}.{$foreign_key_name_in_associated_models_table} = {$associated_object_id}".
		return static::factory( $associated_class_name )
			->where( $foreign_key_name_in_associated_models_table, $associated_object_id );
	}

	/**
	 * Helper method to manage many-to-many relationships via an intermediate model. See
	 * README for a full explanation of the parameters.
	 *
	 * @param string      $associated_class_name   The associated class name.
	 * @param string|null $join_class_name         The class name to join.
	 * @param string|null $key_to_base_table       The key to the the current models table.
	 * @param string|null $key_to_associated_table The key to the associated table.
	 * @param string|null $key_in_base_table       The key in the current models table.
	 * @param string|null $key_in_associated_table The key in the associated table.
	 *
	 * @return ORM Instance of the ORM.
	 */
	protected function has_many_through( $associated_class_name, $join_class_name = null, $key_to_base_table = null, $key_to_associated_table = null, $key_in_base_table = null, $key_in_associated_table = null ) {
		$base_class_name = static::class;

		/*
		 * The class name of the join model, if not supplied, is formed by
		 * concatenating the names of the base class and the associated class,
		 * in alphabetical order.
		 */
		if ( $join_class_name === null ) {
			$base_model      = \explode( '\\', $base_class_name );
			$base_model_name = \end( $base_model );
			if ( \strpos( $base_model_name, static::$auto_prefix_models ) === 0 ) {
				$base_model_name = \substr( $base_model_name, \strlen( static::$auto_prefix_models ), \strlen( $base_model_name ) );
			}
			// Paris wasn't checking the name settings for the associated class.
			$associated_model      = \explode( '\\', $associated_class_name );
			$associated_model_name = \end( $associated_model );
			if ( \strpos( $associated_model_name, static::$auto_prefix_models ) === 0 ) {
				$associated_model_name = \substr( $associated_model_name, \strlen( static::$auto_prefix_models ), \strlen( $associated_model_name ) );
			}
			$class_names = [ $base_model_name, $associated_model_name ];
			\sort( $class_names, \SORT_STRING );
			$join_class_name = \implode( '', $class_names );
		}

		// Get table names for each class.
		$base_table_name       = static::get_table_name_for_class( $base_class_name );
		$associated_table_name = static::get_table_name_for_class( static::$auto_prefix_models . $associated_class_name );
		$join_table_name       = static::get_table_name_for_class( static::$auto_prefix_models . $join_class_name );

		// Get ID column names.
		$base_table_id_column       = ( $key_in_base_table === null ) ? static::get_id_column_name( $base_class_name ) : $key_in_base_table;
		$associated_table_id_column = ( $key_in_associated_table === null ) ? static::get_id_column_name( static::$auto_prefix_models . $associated_class_name ) : $key_in_associated_table;

		// Get the column names for each side of the join table.
		$key_to_base_table       = static::build_foreign_key_name( $key_to_base_table, $base_table_name );
		$key_to_associated_table = static::build_foreign_key_name( $key_to_associated_table, $associated_table_name );

		/* phpcs:ignore Squiz.PHP.CommentedOutCode.Found -- Reason: This is commented out code.
			"   SELECT {$associated_table_name}.*
				FROM {$associated_table_name} JOIN {$join_table_name}
					ON {$associated_table_name}.{$associated_table_id_column} = {$join_table_name}.{$key_to_associated_table}
				WHERE {$join_table_name}.{$key_to_base_table} = {$this->$base_table_id_column} ;"
		*/

		return static::factory( $associated_class_name )
			->select( "{$associated_table_name}.*" )
			->join(
				$join_table_name,
				[
					"{$associated_table_name}.{$associated_table_id_column}",
					'=',
					"{$join_table_name}.{$key_to_associated_table}",
				]
			)
			->where( "{$join_table_name}.{$key_to_base_table}", $this->{$base_table_id_column} );
	}

	/**
	 * Set the wrapped ORM instance associated with this Model instance.
	 *
	 * @param ORM $orm The ORM instance to set.
	 *
	 * @return void
	 */
	public function set_orm( $orm ) {
		$this->orm = $orm;
	}

	/**
	 * Magic getter method, allows $model->property access to data.
	 *
	 * @param string $property The property to get.
	 *
	 * @return mixed The value of the property
	 */
	public function __get( $property ) {
		$value = $this->orm->get( $property );

		if ( $value !== null && \in_array( $property, $this->boolean_columns, true ) ) {
			return (bool) $value;
		}
		if ( $value !== null && \in_array( $property, $this->int_columns, true ) ) {
			return (int) $value;
		}
		if ( $value !== null && \in_array( $property, $this->float_columns, true ) ) {
			return (float) $value;
		}

		return $value;
	}

	/**
	 * Magic setter method, allows $model->property = 'value' access to data.
	 *
	 * @param string $property The property to set.
	 * @param string $value    The value to set.
	 *
	 * @return void
	 */
	public function __set( $property, $value ) {
		if ( $value !== null && \in_array( $property, $this->boolean_columns, true ) ) {
			$value = ( $value ) ? '1' : '0';
		}
		if ( $value !== null && \in_array( $property, $this->int_columns, true ) ) {
			$value = (string) $value;
		}
		if ( $value !== null && \in_array( $property, $this->float_columns, true ) ) {
			$value = (string) $value;
		}

		$this->orm->set( $property, $value );
	}

	/**
	 * Magic unset method, allows unset($model->property)
	 *
	 * @param string $property The property to unset.
	 *
	 * @return void
	 */
	public function __unset( $property ) {
		$this->orm->__unset( $property );
	}

	/**
	 * JSON serializer.
	 *
	 * @return array The data of this object.
	 */
	#[ReturnTypeWillChange]
	public function jsonSerialize() {
		return $this->orm->as_array();
	}

	/**
	 * Strips all nested dependencies from the debug info.
	 *
	 * @return array
	 */
	public function __debugInfo() {
		if ( $this->orm ) {
			return $this->orm->as_array();
		}

		return [];
	}

	/**
	 * Magic isset method, allows isset($model->property) to work correctly.
	 *
	 * @param string $property The property to check.
	 *
	 * @return bool True when value is set.
	 */
	public function __isset( $property ) {
		return $this->orm->__isset( $property );
	}

	/**
	 * Getter method, allows $model->get('property') access to data
	 *
	 * @param string $property The property to get.
	 *
	 * @return string The value of a property.
	 */
	public function get( $property ) {
		return $this->orm->get( $property );
	}

	/**
	 * Setter method, allows $model->set('property', 'value') access to data.
	 *
	 * @param string|array $property The property to set.
	 * @param string|null  $value    The value to give.
	 *
	 * @return static Current object.
	 */
	public function set( $property, $value = null ) {
		$this->orm->set( $property, $value );

		return $this;
	}

	/**
	 * Setter method, allows $model->set_expr('property', 'value') access to data.
	 *
	 * @param string|array $property The property to set.
	 * @param string|null  $value    The value to give.
	 *
	 * @return static Current object.
	 */
	public function set_expr( $property, $value = null ) {
		$this->orm->set_expr( $property, $value );

		return $this;
	}

	/**
	 * Check whether the given property has changed since the object was created or saved.
	 *
	 * @param string $property The property to check.
	 *
	 * @return bool True when field is changed.
	 */
	public function is_dirty( $property ) {
		return $this->orm->is_dirty( $property );
	}

	/**
	 * Check whether the model was the result of a call to create() or not.
	 *
	 * @return bool True when is new.
	 */
	public function is_new() {
		return $this->orm->is_new();
	}

	/**
	 * Wrapper for Idiorm's as_array method.
	 *
	 * @return array The models data as array.
	 */
	public function as_array() {
		$args = \func_get_args();

		return \call_user_func_array( [ $this->orm, 'as_array' ], $args );
	}

	/**
	 * Save the data associated with this model instance to the database.
	 *
	 * @return bool True on success.
	 */
	public function save() {
		if ( $this->uses_timestamps ) {
			if ( ! $this->created_at ) {
				$this->created_at = \gmdate( 'Y-m-d H:i:s' );
			}
			$this->updated_at = \gmdate( 'Y-m-d H:i:s' );
		}

		return $this->orm->save();
	}

	/**
	 * Delete the database row associated with this model instance.
	 *
	 * @return bool|int Response of wpdb::query.
	 */
	public function delete() {
		return $this->orm->delete();
	}

	/**
	 * Get the database ID of this model instance.
	 *
	 * @return int The database ID of the models instance.
	 *
	 * @throws Exception When the ID is a null value.
	 */
	public function id() {
		return $this->orm->id();
	}

	/**
	 * Hydrate this model instance with an associative array of data.
	 * WARNING: The keys in the array MUST match with columns in the
	 * corresponding database table. If any keys are supplied which
	 * do not match up with columns, the database will throw an error.
	 *
	 * @param array $data The data to pass to the ORM.
	 *
	 * @return void
	 */
	public function hydrate( $data ) {
		$this->orm->hydrate( $data )->force_all_dirty();
	}

	/**
	 * Calls static methods directly on the ORM
	 *
	 * @param string $method    The method to call.
	 * @param array  $arguments The arguments to use.
	 *
	 * @return array Result of the static call.
	 */
	public static function __callStatic( $method, $arguments ) {
		if ( ! \function_exists( 'get_called_class' ) ) {
			return [];
		}

		$model = static::factory( static::class );

		return \call_user_func_array( [ $model, $method ], $arguments );
	}
}
