<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'movingloadsusa');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         '][3<Z_-?195iia>8F(M_|s<BBd$PEX8rj+`DvPOZZi*Dt0ODF!..0JX,xOI6!l@[');
define('SECURE_AUTH_KEY',  '+BdQ~V!9Q0j^.)#g_RB/GOS)-iiCA65iL4:@vQZlA(,CB~ttKMiapa=Vv,A+gb-}');
define('LOGGED_IN_KEY',    'jd[nj1^bGB%g4B1<;i5Id`Itz|1G.*1]f-M+ee94;f8UC[j@8dJ0`^GKcl% ]{]+');
define('NONCE_KEY',        'h%b!NT4o~>+Jxh MnNGH6VEX}>?GN3}D+n8!PktZv-(`z2N-Q,6M5}5cV,jTG65]');
define('AUTH_SALT',        'xcEjkf!SzR[xyNrp-!_#%#p*~UI+?aSvQ>v=VT%|#-uP!6;%#t.JLrJpxHIJ|0n0');
define('SECURE_AUTH_SALT', '~_f~a VjiME+C99=Te44[~:fw)LiUH*bcTN.&R%cJ=G}M+k-l-x^/Q|SHc]j8u~Q');
define('LOGGED_IN_SALT',   'yAt-I{IlOzkE]:,t4#]5?bb@:7>9A-+EZ`}g_ijnWX%At(Tq~ONG dV)u6/q,aJ|');
define('NONCE_SALT',       'aWbtukX_FsC9;SP06Q}-=lmopccd{%@bD?N>~qa5P*iW7|z1^k/9J<x&Kn1r^QDt');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);
define('DISPLAY_FORMAT_DATETIME_SHORT', 'j-M-y g:i A');
/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
