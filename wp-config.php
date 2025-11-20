<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'fr' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'f2P-@_vGb`?IN?tn`mp6J{ )?^uE(07rF9pu}jNzL,1:?);Ec.,wDkwb3xiVkD$<' );
define( 'SECURE_AUTH_KEY',  'rJ[6hy@`(`4a6|x*amtSYH<5e$GunR|?uEqb>z%-V?Mczv 7,$8b}K$Mmfoz2*G*' );
define( 'LOGGED_IN_KEY',    'V]z@BLQ1T=;8}6_905WH 6c#cMq9.I!vTqkCfXjKa>mSfs@lel.- 4({:);T[izb' );
define( 'NONCE_KEY',        '0qk</5tT95z`*e4Wjn3Uy]Xp}9+6~FGt;~A&&ne&3=*>yl8/gJ63USFnCCL;XD7g' );
define( 'AUTH_SALT',        'wY0-D8ssqgCM^q&(XUSPwY#G_FyS0G%T$5~ZP7:kb<ne?w4*W.G?+C,9LVLNf^fo' );
define( 'SECURE_AUTH_SALT', 'it+O*%nMC7S31 ]CFd#X~f;3UDKiMx8nOk:8qA5)+[I<?xzC@$Dn:Z_K/h_AtmHO' );
define( 'LOGGED_IN_SALT',   'Uv+Mc-M1h4|u:k_+H#*/9&_0zYhe )IRZ2Nix|hTv{Iq%=%2To)]-*1hYd.fDAaD' );
define( 'NONCE_SALT',       'duEF%.9u:pG e9a0/kKE2(szpWLCc *ZU5b/F6Se0E5<}]C2!Q}2K&/0{t[<JYKz' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'fr_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
