<?php
require_once('../wp-config.php');

/**
 ** maybe_create_table()
 ** Create db table if it doesn't exist.
 ** Returns:  true if already exists or on successful completion
 **           false on error
 */
function maybe_create_table($table_name, $create_ddl) {
    global $wpdb;
    foreach ($wpdb->get_col("SHOW TABLES",0) as $table ) {
        if ($table == $table_name) {
            return true;
        }
    }
    //didn't find it try to create it.
    $q = $wpdb->query($create_ddl);
    // we cannot directly tell that whether this succeeded!
    foreach ($wpdb->get_col("SHOW TABLES",0) as $table ) {
        if ($table == $table_name) {
            return true;
        }
    }
    return false;
}

/**
 ** maybe_add_column()
 ** Add column to db table if it doesn't exist.
 ** Returns:  true if already exists or on successful completion
 **           false on error
 */
function maybe_add_column($table_name, $column_name, $create_ddl) {
    global $wpdb;
    foreach ($wpdb->get_col("DESC $table_name",0) as $column ) {
        if ($column == $column_name) {
            return true;
        }
    }
    //didn't find it try to create it.
    $q = $wpdb->query($create_ddl);
    // we cannot directly tell that whether this succeeded!
    foreach ($wpdb->get_col("DESC $table_name",0) as $column ) {
        if ($column == $column_name) {
            return true;
        }
    }
    return false;
}


/**
 ** maybe_drop_column()
 ** Drop column from db table if it exists.
 ** Returns:  true if it doesn't already exist or on successful drop
 **           false on error
 */
function maybe_drop_column($table_name, $column_name, $drop_ddl) {
    global $wpdb;
    foreach ($wpdb->get_col("DESC $table_name",0) as $column ) {
        if ($column == $column_name) {
            //found it try to drop it.
            $q = $wpdb->query($drop_ddl);
            // we cannot directly tell that whether this succeeded!
            foreach ($wpdb->get_col("DESC $table_name",0) as $column ) {
                if ($column == $column_name) {
                    return false;
                }
            }
        }
    }
    // else didn't find it
    return true;
}
?>