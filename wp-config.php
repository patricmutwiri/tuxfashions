<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
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

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'tuxfashions');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'pat1');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'uX+&=X2Y8GUUkcmY+*?1%-HeqMzw0AU0h+:H(!O.m<</SC94;QVPICouTPT+&D2G');
define('SECURE_AUTH_KEY',  '^)nl|o1FCb>!!gTs<R!^LUyDs^-PMm/7^V$.mK!+iL-*c*SZ6~9f3Rl?sUXSN$Bj');
define('LOGGED_IN_KEY',    'WXVg1A!RieF0 3eq$l%3n<5*H`2M#;g]2{qPnuY@X}n;N+8vAi]rAK|ab6?3bzZP');
define('NONCE_KEY',        'A#=ui+vZ]+jDHzdu]r)B[Yz[2<Q.$4P9qf$bc Gzcod_9 ^ 6[#C?0dt*}xeM6nP');
define('AUTH_SALT',        'P+y+n[N G|Koq@-8sVq:Nf$Ppri]0NN]r<%6hi+|2dc-.}DxgT-nK0PKPOz)&wmx');
define('SECURE_AUTH_SALT', 'EsN&pOOp:s(0~nV|:ybHcq;o !:@pi* hk;kuT.[6GT(47l%kYI3g7:.#D=r}},D');
define('LOGGED_IN_SALT',   'CCuP,^zX73VPs.b^es9t:5?u5G=%jJ b>>qDK{CH.myM5ShpFzY@ju!?JiFVlaur');
define('NONCE_SALT',       'KWirJ{u7R yTC_hscSJ?(Y8+|zp>h_|2-cKHRN*[Y#Y_dX{*9M3#-api_uE^c=x4');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'tuxWd_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');