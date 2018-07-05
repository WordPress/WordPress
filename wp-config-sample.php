<?php
/**
 * 워드프레스 기본 설정
 *
 * wp-config.php 작성 스크립트는 설치 중에이 파일을 사용합니다. 
 * 웹 사이트를 사용할 필요가 없으므로 이 파일을 "wp-config.php"에 복사하고 값을 채울 수 있습니다.
 * 이 파일은 다음과 같은 설정을 포함합니다. 
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - 웹 호스트로 부터 이 정보를 얻을 수 있다. ** //
/** 워드프레스 데이터베이스 이름 */
define( 'DB_NAME', 'database_name_here' );

/** MySQL database 유저 이름 */
define( 'DB_USER', 'username_here' );

/** MySQL database 패스워드 */
define( 'DB_PASSWORD', 'password_here' );

/** MySQL 호스트이름 */
define( 'DB_HOST', 'localhost' );

/** 데이터베이스 테이블 만드는데 사용되는 DB Character set */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. 애매하면 이것을 바꾸지 마세요. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * 이러한 항목들을 유일한 값으로 바꾸세요!
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}을 이용해서 이러한 값들을 생성할 수 있습니다.
 * 기존의 모든 쿠키를 무효화하려면 언제든지 이를 변경할 수 있습니다. 이렇게 하면 모든 사용자가 다시 로그인해야합니다.
 *
 * @since 2.6.0
 */

define( 'AUTH_KEY',         'put your unique phrase here' );
define( 'SECURE_AUTH_KEY',  'put your unique phrase here' );
define( 'LOGGED_IN_KEY',    'put your unique phrase here' );
define( 'NONCE_KEY',        'put your unique phrase here' );
define( 'AUTH_SALT',        'put your unique phrase here' );
define( 'SECURE_AUTH_SALT', 'put your unique phrase here' );
define( 'LOGGED_IN_SALT',   'put your unique phrase here' );
define( 'NONCE_SALT',       'put your unique phrase here' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * 각각 다른 유일한 prefix를 설정한다면 하나의 데이터베이스에 여러 번 설치할 수 있습니다.
 * 숫자와 문자, _만 입력하세요!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 * 개발 중 통지를 표시하려면 이 값을 true로 변경하십시오.
 * 플러그인 및 테마 개발자는 개발 환경에서 WP_DEBUG를 사용하는 것이 좋습니다.
 *
 * 디버깅에 사용할 수있는 다른 상수에 대한 정보는 Codex를 방문하십시오.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/** 워드프레스 디렉토리 절대 경로 */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** 포함되는 파일들과 환경변수를 설정한다. */
require_once( ABSPATH . 'wp-settings.php' );
