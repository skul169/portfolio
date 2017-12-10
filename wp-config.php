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
define('DB_NAME', 'portfolio');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         'ooq1sQ8({$&1*:-Jw73Q/L|#:*b,cZ{~xm?N?Y?w?hrDp:P(Z9Atj]{O*D2P;@Gq');
define('SECURE_AUTH_KEY',  'V]yV.0MMyjgded`?vVqzZSZLF:2,ki|4?.~o}X/Cy,1O+U`V9|x`o!XIyTfl&r`Z');
define('LOGGED_IN_KEY',    'vo]~qlurp<ExOctF$SGC:->F{o=9|ePDsM6wpCJw!!41<UVaC(cArv..xRPelC=;');
define('NONCE_KEY',        '9a&x6cE2j`!f1LC4adtQW|m7PUkGJ-X82$ zCgL]u-g7aC{PI7lBMs$Y ])wb`k9');
define('AUTH_SALT',        'VkBte~(l:x7N[a$!z{`]EaBRG:)4O9sd4YyAYZ]>_Y_~l*&B ac`8B[8HJG.s#T-');
define('SECURE_AUTH_SALT', '%Y b9E0WF)q_[/Q;RD= /S(p|Zz2wGnRFWi*=.0}U+[QF3:ch^@[SHhj(`!c^,JS');
define('LOGGED_IN_SALT',   'xRjkt112Wqp3+1af.E&!WmFc^(<bm)c=/CLim!/{M9y*B<h*p)DJRR2nE5n$hB=]');
define('NONCE_SALT',       'kg<)+Kak4T+tK-S({h$GT)3UAp@BNESTA<elzY7t/fzu=`@Op,_CWhUAIg^}lHiJ');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'truongnv_';

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
