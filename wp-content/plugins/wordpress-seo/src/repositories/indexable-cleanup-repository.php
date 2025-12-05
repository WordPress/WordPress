<?php

namespace Yoast\WP\SEO\Repositories;

use mysqli_result;
use Yoast\WP\Lib\Model;
use Yoast\WP\Lib\ORM;
use Yoast\WP\SEO\Helpers\Author_Archive_Helper;
use Yoast\WP\SEO\Helpers\Post_Type_Helper;
use Yoast\WP\SEO\Helpers\Taxonomy_Helper;

/**
 * Repository containing all cleanup queries.
 */
class Indexable_Cleanup_Repository {

	/**
	 * A helper for taxonomies.
	 *
	 * @var Taxonomy_Helper
	 */
	private $taxonomy;

	/**
	 * A helper for post types.
	 *
	 * @var Post_Type_Helper
	 */
	private $post_type;

	/**
	 * A helper for author archives.
	 *
	 * @var Author_Archive_Helper
	 */
	private $author_archive;

	/**
	 * The constructor.
	 *
	 * @param Taxonomy_Helper       $taxonomy       A helper for taxonomies.
	 * @param Post_Type_Helper      $post_type      A helper for post types.
	 * @param Author_Archive_Helper $author_archive A helper for author archives.
	 */
	public function __construct( Taxonomy_Helper $taxonomy, Post_Type_Helper $post_type, Author_Archive_Helper $author_archive ) {
		$this->taxonomy       = $taxonomy;
		$this->post_type      = $post_type;
		$this->author_archive = $author_archive;
	}

	/**
	 * Starts a query for this repository.
	 *
	 * @return ORM
	 */
	public function query() {
		return Model::of_type( 'Indexable' );
	}

	/**
	 * Deletes rows from the indexable table depending on the object_type and object_sub_type.
	 *
	 * @param string $object_type     The object type to query.
	 * @param string $object_sub_type The object subtype to query.
	 * @param int    $limit           The limit we'll apply to the delete query.
	 *
	 * @return int|bool The number of rows that was deleted or false if the query failed.
	 */
	public function clean_indexables_with_object_type_and_object_sub_type( string $object_type, string $object_sub_type, int $limit ) {
		global $wpdb;

		$indexable_table = Model::get_table_name( 'Indexable' );

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: There is no unescaped user input.
		$sql = $wpdb->prepare( "DELETE FROM $indexable_table WHERE object_type = %s AND object_sub_type = %s ORDER BY id LIMIT %d", $object_type, $object_sub_type, $limit );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: Already prepared.
		return $wpdb->query( $sql );
	}

	/**
	 * Counts amount of indexables by object type and object sub type.
	 *
	 * @param string $object_type     The object type to check.
	 * @param string $object_sub_type The object sub type to check.
	 *
	 * @return float|int
	 */
	public function count_indexables_with_object_type_and_object_sub_type( string $object_type, string $object_sub_type ) {
		return $this
			->query()
			->where( 'object_type', $object_type )
			->where( 'object_sub_type', $object_sub_type )
			->count();
	}

	/**
	 * Deletes rows from the indexable table depending on the post_status.
	 *
	 * @param string $post_status The post status to query.
	 * @param int    $limit       The limit we'll apply to the delete query.
	 *
	 * @return int|bool The number of rows that was deleted or false if the query failed.
	 */
	public function clean_indexables_with_post_status( $post_status, $limit ) {
		global $wpdb;

		$indexable_table = Model::get_table_name( 'Indexable' );

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: There is no unescaped user input.
		$sql = $wpdb->prepare( "DELETE FROM $indexable_table WHERE object_type = 'post' AND post_status = %s ORDER BY id LIMIT %d", $post_status, $limit );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: Already prepared.
		return $wpdb->query( $sql );
	}

	/**
	 * Counts indexables with a certain post status.
	 *
	 * @param string $post_status The post status to count.
	 *
	 * @return float|int
	 */
	public function count_indexables_with_post_status( string $post_status ) {
		return $this
			->query()
			->where( 'object_type', 'post' )
			->where( 'post_status', $post_status )
			->count();
	}

	/**
	 * Cleans up any indexables that belong to post types that are not/no longer publicly viewable.
	 *
	 * @param int $limit The limit we'll apply to the queries.
	 *
	 * @return bool|int The number of deleted rows, false if the query fails.
	 */
	public function clean_indexables_for_non_publicly_viewable_post( $limit ) {
		global $wpdb;
		$indexable_table = Model::get_table_name( 'Indexable' );

		$included_post_types = $this->post_type->get_indexable_post_types();

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: Too hard to fix.
		if ( empty( $included_post_types ) ) {
			$delete_query = $wpdb->prepare(
				"DELETE FROM $indexable_table
				WHERE object_type = 'post'
				AND object_sub_type IS NOT NULL
				LIMIT %d",
				$limit
			);
		}
		else {
			// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber -- Reason: we're passing an array instead.
			$delete_query = $wpdb->prepare(
				"DELETE FROM $indexable_table
				WHERE object_type = 'post'
				AND object_sub_type IS NOT NULL
				AND object_sub_type NOT IN ( " . \implode( ', ', \array_fill( 0, \count( $included_post_types ), '%s' ) ) . ' )
				LIMIT %d',
				\array_merge( $included_post_types, [ $limit ] )
			);
		}
		// phpcs:enable

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: No relevant caches.
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery -- Reason: Most performant way.
		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared -- Reason: Is it prepared already.
		return $wpdb->query( $delete_query );
		// phpcs:enable
	}

	/**
	 * Counts all indexables for non public post types.
	 *
	 * @return float|int
	 */
	public function count_indexables_for_non_publicly_viewable_post() {
		$included_post_types = $this->post_type->get_indexable_post_types();

		if ( empty( $included_post_types ) ) {
			return $this
				->query()
				->where( 'object_type', 'post' )
				->where_not_equal( 'object_sub_type', 'null' )
				->count();
		}
		else {
			return $this
				->query()
				->where( 'object_type', 'post' )
				->where_not_equal( 'object_sub_type', 'null' )
				->where_not_in( 'object_sub_type', $included_post_types )
				->count();
		}
	}

	/**
	 * Cleans up any indexables that belong to taxonomies that are not/no longer publicly viewable.
	 *
	 * @param int $limit The limit we'll apply to the queries.
	 *
	 * @return bool|int The number of deleted rows, false if the query fails.
	 */
	public function clean_indexables_for_non_publicly_viewable_taxonomies( $limit ) {
		global $wpdb;
		$indexable_table = Model::get_table_name( 'Indexable' );

		$included_taxonomies = $this->taxonomy->get_indexable_taxonomies();

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: Too hard to fix.
		if ( empty( $included_taxonomies ) ) {
			$delete_query = $wpdb->prepare(
				"DELETE FROM $indexable_table
				WHERE object_type = 'term'
				AND object_sub_type IS NOT NULL
				LIMIT %d",
				$limit
			);
		}
		else {
			// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber -- Reason: we're passing an array instead.
			$delete_query = $wpdb->prepare(
				"DELETE FROM $indexable_table
				WHERE object_type = 'term'
				AND object_sub_type IS NOT NULL
				AND object_sub_type NOT IN ( " . \implode( ', ', \array_fill( 0, \count( $included_taxonomies ), '%s' ) ) . ' )
				LIMIT %d',
				\array_merge( $included_taxonomies, [ $limit ] )
			);
		}
		// phpcs:enable

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: No relevant caches.
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery -- Reason: Most performant way.
		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared -- Reason: Is it prepared already.
		return $wpdb->query( $delete_query );
		// phpcs:enable
	}

	/**
	 * Cleans up any indexables that belong to post type archive page that are not/no longer publicly viewable.
	 *
	 * @param int $limit The limit we'll apply to the queries.
	 *
	 * @return bool|int The number of deleted rows, false if the query fails.
	 */
	public function clean_indexables_for_non_publicly_viewable_post_type_archive_pages( $limit ) {
		global $wpdb;
		$indexable_table = Model::get_table_name( 'Indexable' );

		$included_post_types = $this->post_type->get_indexable_post_archives();

		$post_archives = [];

		foreach ( $included_post_types as $post_type ) {
			$post_archives[] = $post_type->name;
		}
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: Too hard to fix.
		if ( empty( $post_archives ) ) {
			$delete_query = $wpdb->prepare(
				"DELETE FROM $indexable_table
				WHERE object_type = 'post-type-archive'
				AND object_sub_type IS NOT NULL
				LIMIT %d",
				$limit
			);
		}
		else {
			// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber -- Reason: we're passing an array instead.
			$delete_query = $wpdb->prepare(
				"DELETE FROM $indexable_table
				WHERE object_type = 'post-type-archive'
				AND object_sub_type IS NOT NULL
				AND object_sub_type NOT IN ( " . \implode( ', ', \array_fill( 0, \count( $post_archives ), '%s' ) ) . ' )
				LIMIT %d',
				\array_merge( $post_archives, [ $limit ] )
			);
		}
		// phpcs:enable

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: No relevant caches.
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery -- Reason: Most performant way.
		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared -- Reason: Is it prepared already.
		return $wpdb->query( $delete_query );
		// phpcs:enable
	}

	/**
	 * Counts indexables for non publicly viewable taxonomies.
	 *
	 * @return float|int
	 */
	public function count_indexables_for_non_publicly_viewable_taxonomies() {
		$included_taxonomies = $this->taxonomy->get_indexable_taxonomies();
		if ( empty( $included_taxonomies ) ) {
			return $this
				->query()
				->where( 'object_type', 'term' )
				->where_not_equal( 'object_sub_type', 'null' )
				->count();
		}
		else {
			return $this
				->query()
				->where( 'object_type', 'term' )
				->where_not_equal( 'object_sub_type', 'null' )
				->where_not_in( 'object_sub_type', $included_taxonomies )
				->count();
		}
	}

	/**
	 * Counts indexables for non publicly viewable taxonomies.
	 *
	 * @return float|int
	 */
	public function count_indexables_for_non_publicly_post_type_archive_pages() {
		$included_post_types = $this->post_type->get_indexable_post_archives();

		$post_archives = [];

		foreach ( $included_post_types as $post_type ) {
			$post_archives[] = $post_type->name;
		}
		if ( empty( $post_archives ) ) {
			return $this
				->query()
				->where( 'object_type', 'post-type-archive' )
				->where_not_equal( 'object_sub_type', 'null' )
				->count();
		}

		return $this
			->query()
			->where( 'object_type', 'post-type-archive' )
			->where_not_equal( 'object_sub_type', 'null' )
			->where_not_in( 'object_sub_type', $post_archives )
			->count();
	}

	/**
	 * Cleans up any user indexables when the author archives have been disabled.
	 *
	 * @param int $limit The limit we'll apply to the queries.
	 *
	 * @return bool|int The number of deleted rows, false if the query fails.
	 */
	public function clean_indexables_for_authors_archive_disabled( $limit ) {
		global $wpdb;

		if ( ! $this->author_archive->are_disabled() ) {
			return 0;
		}

		$indexable_table = Model::get_table_name( 'Indexable' );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: Too hard to fix.
		$delete_query = $wpdb->prepare( "DELETE FROM $indexable_table WHERE object_type = 'user' LIMIT %d", $limit );
		// phpcs:enable

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: No relevant caches.
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery -- Reason: Most performant way.
		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared -- Reason: Is it prepared already.
		return $wpdb->query( $delete_query );
		// phpcs:enable
	}

	/**
	 * Counts the amount of author archive indexables if they are not disabled.
	 *
	 * @return float|int
	 */
	public function count_indexables_for_authors_archive_disabled() {
		if ( ! $this->author_archive->are_disabled() ) {
			return 0;
		}

		return $this
			->query()
			->where( 'object_type', 'user' )
			->count();
	}

	/**
	 * Cleans up any indexables that belong to users that have their author archives disabled.
	 *
	 * @param int $limit The limit we'll apply to the queries.
	 *
	 * @return bool|int The number of deleted rows, false if the query fails.
	 */
	public function clean_indexables_for_authors_without_archive( $limit ) {
		global $wpdb;

		$indexable_table           = Model::get_table_name( 'Indexable' );
		$author_archive_post_types = $this->author_archive->get_author_archive_post_types();
		$viewable_post_stati       = \array_filter( \get_post_stati(), 'is_post_status_viewable' );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: Too hard to fix.
		// phpcs:disable WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber -- Reason: we're passing an array instead.
		$delete_query = $wpdb->prepare(
			"DELETE FROM $indexable_table
				WHERE object_type = 'user'
				AND object_id NOT IN (
					SELECT DISTINCT post_author
					FROM $wpdb->posts
					WHERE post_type IN ( " . \implode( ', ', \array_fill( 0, \count( $author_archive_post_types ), '%s' ) ) . ' )
					AND post_status IN ( ' . \implode( ', ', \array_fill( 0, \count( $viewable_post_stati ), '%s' ) ) . ' )
				) LIMIT %d',
			\array_merge( $author_archive_post_types, $viewable_post_stati, [ $limit ] )
		);
		// phpcs:enable

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: No relevant caches.
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery -- Reason: Most performant way.
		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared -- Reason: Is it prepared already.
		return $wpdb->query( $delete_query );
		// phpcs:enable
	}

	/**
	 * Counts total amount of indexables for authors without archives.
	 *
	 * @return bool|int|mysqli_result|resource|null
	 */
	public function count_indexables_for_authors_without_archive() {
		global $wpdb;

		$indexable_table           = Model::get_table_name( 'Indexable' );
		$author_archive_post_types = $this->author_archive->get_author_archive_post_types();
		$viewable_post_stati       = \array_filter( \get_post_stati(), 'is_post_status_viewable' );

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: Too hard to fix.
		// phpcs:disable WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber -- Reason: we're passing an array instead.
		$count_query = $wpdb->prepare(
			"SELECT count(*) FROM $indexable_table
				WHERE object_type = 'user'
				AND object_id NOT IN (
					SELECT DISTINCT post_author
					FROM $wpdb->posts
					WHERE post_type IN ( " . \implode( ', ', \array_fill( 0, \count( $author_archive_post_types ), '%s' ) ) . ' )
					AND post_status IN ( ' . \implode( ', ', \array_fill( 0, \count( $viewable_post_stati ), '%s' ) ) . ' )
				)',
			\array_merge( $author_archive_post_types, $viewable_post_stati )
		);
		// phpcs:enable

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: No relevant caches.
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery -- Reason: Most performant way.
		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared -- Reason: Is it prepared already.
		return $wpdb->get_col( $count_query )[0];
		// phpcs:enable
	}

	/**
	 * Deletes rows from the indexable table where the source is no longer there.
	 *
	 * @param string $source_table      The source table which we need to check the indexables against.
	 * @param string $source_identifier The identifier which the indexables are matched to.
	 * @param string $object_type       The indexable object type.
	 * @param int    $limit             The limit we'll apply to the delete query.
	 *
	 * @return int|bool The number of rows that was deleted or false if the query failed.
	 */
	public function clean_indexables_for_object_type_and_source_table( $source_table, $source_identifier, $object_type, $limit ) {
		global $wpdb;

		$indexable_table = Model::get_table_name( 'Indexable' );
		$source_table    = $wpdb->prefix . $source_table;
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: There is no unescaped user input.
		$query = $wpdb->prepare(
			"
			SELECT indexable_table.object_id
			FROM {$indexable_table} indexable_table
			LEFT JOIN {$source_table} AS source_table
			ON indexable_table.object_id = source_table.{$source_identifier}
			WHERE source_table.{$source_identifier} IS NULL
			AND indexable_table.object_id IS NOT NULL
			AND indexable_table.object_type = '{$object_type}'
			LIMIT %d",
			$limit
		);
		// phpcs:enable

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: Already prepared.
		$orphans = $wpdb->get_col( $query );

		if ( empty( $orphans ) ) {
			return 0;
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: Already prepared.
		return $wpdb->query( "DELETE FROM $indexable_table WHERE object_type = '{$object_type}' AND object_id IN( " . \implode( ',', $orphans ) . ' )' );
	}

	/**
	 * Deletes rows from the indexable table where the source is no longer there.
	 *
	 * @param int $limit The limit we'll apply to the delete query.
	 *
	 * @return int|bool The number of rows that was deleted or false if the query failed.
	 */
	public function clean_indexables_for_orphaned_users( $limit ) {
		global $wpdb;

		$indexable_table = Model::get_table_name( 'Indexable' );
		$source_table    = $wpdb->users;
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: There is no unescaped user input.
		$query = $wpdb->prepare(
			"
			SELECT indexable_table.object_id
			FROM {$indexable_table} indexable_table
			LEFT JOIN {$source_table} AS source_table
			ON indexable_table.object_id = source_table.ID
			WHERE source_table.ID IS NULL
			AND indexable_table.object_id IS NOT NULL
			AND indexable_table.object_type = 'user'
			LIMIT %d",
			$limit
		);
		// phpcs:enable

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: Already prepared.
		$orphans = $wpdb->get_col( $query );

		if ( empty( $orphans ) ) {
			return 0;
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: Already prepared.
		return $wpdb->query( "DELETE FROM $indexable_table WHERE object_type = 'user' AND object_id IN( " . \implode( ',', $orphans ) . ' )' );
	}

	/**
	 * Counts indexables for given source table + source identifier + object type.
	 *
	 * @param string $source_table      The source table.
	 * @param string $source_identifier The source identifier.
	 * @param string $object_type       The object type.
	 *
	 * @return mixed
	 */
	public function count_indexables_for_object_type_and_source_table( string $source_table, string $source_identifier, string $object_type ) {
		global $wpdb;
		$indexable_table = Model::get_table_name( 'Indexable' );
		$source_table    = $wpdb->prefix . $source_table;
		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: Already prepared.
		return $wpdb->get_col(
			"
			SELECT count(*)
			FROM {$indexable_table} indexable_table
			LEFT JOIN {$source_table} AS source_table
			ON indexable_table.object_id = source_table.{$source_identifier}
			WHERE source_table.{$source_identifier} IS NULL
			AND indexable_table.object_id IS NOT NULL
			AND indexable_table.object_type = '{$object_type}'"
		)[0];
		// phpcs:enable
	}

	/**
	 * Counts indexables for orphaned users.
	 *
	 * @return mixed
	 */
	public function count_indexables_for_orphaned_users() {
		global $wpdb;
		$indexable_table = Model::get_table_name( 'Indexable' );
		$source_table    = $wpdb->users;
		//phpcs:disable WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: Already prepared.
		return $wpdb->get_col(
			"
			SELECT count(*)
			FROM {$indexable_table} indexable_table
			LEFT JOIN {$source_table} AS source_table
			ON indexable_table.object_id = source_table.ID
			WHERE source_table.ID IS NULL
			AND indexable_table.object_id IS NOT NULL
			AND indexable_table.object_type = 'user'"
		)[0];
		// phpcs:enable
	}

	/**
	 * Cleans orphaned rows from a yoast table.
	 *
	 * @param string $table  The table to clean up.
	 * @param string $column The table column the cleanup will rely on.
	 * @param int    $limit  The limit we'll apply to the queries.
	 *
	 * @return int|bool The number of deleted rows, false if the query fails.
	 */
	public function cleanup_orphaned_from_table( $table, $column, $limit ) {
		global $wpdb;

		$table           = Model::get_table_name( $table );
		$indexable_table = Model::get_table_name( 'Indexable' );

		// Warning: If this query is changed, make sure to update the query in cleanup_orphaned_from_table in Premium as well.
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: There is no unescaped user input.
		$query = $wpdb->prepare(
			"
			SELECT table_to_clean.{$column}
			FROM {$table} table_to_clean
			LEFT JOIN {$indexable_table} AS indexable_table
			ON table_to_clean.{$column} = indexable_table.id
			WHERE indexable_table.id IS NULL
			AND table_to_clean.{$column} IS NOT NULL
			LIMIT %d",
			$limit
		);
		// phpcs:enable

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: Already prepared.
		$orphans = $wpdb->get_col( $query );

		if ( empty( $orphans ) ) {
			return 0;
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: Already prepared.
		return $wpdb->query( "DELETE FROM $table WHERE {$column} IN( " . \implode( ',', $orphans ) . ' )' );
	}

	/**
	 * Counts orphaned rows from a yoast table.
	 *
	 * @param string $table  The table to clean up.
	 * @param string $column The table column the cleanup will rely on.
	 *
	 * @return int|bool The number of deleted rows, false if the query fails.
	 */
	public function count_orphaned_from_table( string $table, string $column ) {
		global $wpdb;

		$table           = Model::get_table_name( $table );
		$indexable_table = Model::get_table_name( 'Indexable' );

		// Warning: If this query is changed, make sure to update the query in cleanup_orphaned_from_table in Premium as well.
		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: Already prepared.
		return $wpdb->get_col(
			"
			SELECT count(*)
			FROM {$table} table_to_clean
			LEFT JOIN {$indexable_table} AS indexable_table
			ON table_to_clean.{$column} = indexable_table.id
			WHERE indexable_table.id IS NULL
			AND table_to_clean.{$column} IS NOT NULL"
		)[0];
		// phpcs:enable
	}

	/**
	 * Updates the author_id of indexables which author_id is not in the wp_users table with the id of the reassingned
	 * user.
	 *
	 * @param int $limit The limit we'll apply to the queries.
	 *
	 * @return int|bool The number of updated rows, false if query to get data fails.
	 */
	public function update_indexables_author_to_reassigned( $limit ) {
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: Already prepared.
		$reassigned_authors_objs = $this->get_reassigned_authors( $limit );

		if ( $reassigned_authors_objs === false ) {
			return false;
		}

		return $this->update_indexable_authors( $reassigned_authors_objs, $limit );
	}

	/**
	 * Fetches pairs of old_id -> new_id indexed by old_id.
	 * By using the old_id (i.e. the id of the user that has been deleted) as key of the associative array, we can
	 * easily compose an array of unique pairs of old_id -> new_id.
	 *
	 * @param int $limit The limit we'll apply to the queries.
	 *
	 * @return int|bool The associative array with shape [ old_id => [ old_id, new_author ] ] or false if query to get
	 *                  data fails.
	 */
	private function get_reassigned_authors( $limit ) {
		global $wpdb;

		$indexable_table = Model::get_table_name( 'Indexable' );
		$posts_table     = $wpdb->posts;

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: There is no unescaped user input.
		$query = $wpdb->prepare(
			"
			SELECT {$indexable_table}.author_id, {$posts_table}.post_author
			FROM {$indexable_table} JOIN {$posts_table} on {$indexable_table}.object_id = {$posts_table}.id
			WHERE object_type='post'
			AND {$indexable_table}.author_id <> {$posts_table}.post_author
			GROUP BY {$indexable_table}.author_id, {$posts_table}.post_author
			ORDER BY {$indexable_table}.author_id
			LIMIT %d",
			$limit
		);
		// phpcs:enable

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: Already prepared.
		return $wpdb->get_results( $query, \OBJECT_K );
	}

	/**
	 * Updates the indexable's author_id referring to a deleted author with the id of the reassigned user.
	 *
	 * @param array $reassigned_authors_objs The array of objects with shape [ old_id => [ old_id, new_id ] ].
	 * @param int   $limit                   The limit we'll apply to the queries.
	 *
	 * @return int|bool The associative array with shape [ old_id => [ old_id, new_author ] ] or false if query to get
	 *                  data fails.
	 */
	private function update_indexable_authors( $reassigned_authors_objs, $limit ) {
		global $wpdb;

		$indexable_table = Model::get_table_name( 'Indexable' );

		// This is a workaround for the fact that the array_column function does not work on objects in PHP 5.6.
		$reassigned_authors_array = \array_map(
			static function ( $obj ) {
				return (array) $obj;
			},
			$reassigned_authors_objs
		);

		$reassigned_authors = \array_combine( \array_column( $reassigned_authors_array, 'author_id' ), \array_column( $reassigned_authors_array, 'post_author' ) );

		foreach ( $reassigned_authors as $old_author_id => $new_author_id ) {
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: There is no unescaped user input.
			$query = $wpdb->prepare(
				"
				UPDATE {$indexable_table}
				SET {$indexable_table}.author_id = {$new_author_id}
				WHERE {$indexable_table}.author_id = {$old_author_id}
				AND object_type='post'
				LIMIT %d",
				$limit
			);
			// phpcs:enable

			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: Already prepared.
			$wpdb->query( $query );
		}

		return \count( $reassigned_authors );
	}
}
