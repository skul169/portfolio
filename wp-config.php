<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

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
define('DB_NAME', 'porfolio');

/** MySQL database username */
define('DB_USER', 'truongnv');

/** MySQL database password */
define('DB_PASSWORD', 'nguyenvantruong1690');

/** MySQL hostname */
define('DB_HOST', 'portfolio.cb6npoabh5yk.ap-southeast-1.rds.amazonaws.com');

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
define('AUTH_KEY',         '&7>+>IIs6(i?, jHm(Os@ZM!bvyd@?N}xEKWB$W-TxqFO7V@k{&XV9+F7uMWJg*^');
define('SECURE_AUTH_KEY',  'sJ6dU`TbPVq/$8GWhDb+$%<gJAQM=0t[a@A0zlL,V|&?SYuDB|R J<]1nokpA/9o');
define('LOGGED_IN_KEY',    '_#*WB}s?,7OnT[ ]:}q7v{^>W-yh& B3Sec~PRjw;[JZ^g!FQDExO>@Erhe|!*|P');
define('NONCE_KEY',        '*m p.pkY[Q&`FWYY{.>Svd[C{[H`BsEN/(Sh5BQO`aAQ,wcB2|08R~P%2b3.1XfP');
define('AUTH_SALT',        '5Q>!usO@`imIBavTC-& I!b BwtYOops|)j<`}[j$E6,N$_&h R}))*q_khdx:`|');
define('SECURE_AUTH_SALT', 'oaC:R%w,y,4l4^ZbeDiQf?LDkkI-Ul?.S;/=$5}dj|TJrX{vDU4cH$D[rtAKO_7=');
define('LOGGED_IN_SALT',   'KR6wr!bhcLBbJ7F=[<FePx[vf|I& 2W!nfiX?%,m}+)t5SteSgfl5,E>6kU3_]#!');
define('NONCE_SALT',       'n=I:vtzo.VH7(Lli:kU:8pbDTq;K;s*k!vPT#Px X^|H#o?]55*P>Rx)[_*%PR5+');
//define('FTP_USER', 'truongnv'); // Your FTP username
//define('FTP_PASS', 'nguyenvantruong1690'); // Your FTP password
//define('FTP_HOST', '13.250.54.166:21'); // Your FTP URL:Your FTP port
//define( 'FTP_BASE', '/usr/share/nginx/portfolio/' );
//define( 'FTP_CONTENT_DIR', '/usr/share/nginx/portfolio/wp-content/' );
//define( 'FTP_PLUGIN_DIR', '/usr/share/nginx/portfolio/wp-content/plugins/' );

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
