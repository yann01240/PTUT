<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clefs secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur 
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C'est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d'installation. Vous n'avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define('DB_NAME', 'wordpress');

/** Utilisateur de la base de données MySQL. */
define('DB_USER', 'root');

/** Mot de passe de la base de données MySQL. */
define('DB_PASSWORD', 'root');

/** Adresse de l'hébergement MySQL. */
define('DB_HOST', 'localhost');

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define('DB_CHARSET', 'utf8');

/** Type de collation de la base de données. 
  * N'y touchez que si vous savez ce que vous faites. 
  */
define('DB_COLLATE', '');

/**#@+
 * Clefs uniques d'authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant 
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n'importe quel moment, afin d'invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'qe6iza_<p1R @4zX lV3i[jlkN!O[4vXB31to+ESUD 5.4>q+w.Kn|sO@l,DnE/4');
define('SECURE_AUTH_KEY',  'o-Cqyi^R_KIX6dX[-~y=~FuAP.ctsd(zsau|(D5foSlJHD+^p[Z#b,bu7pL!X*-x');
define('LOGGED_IN_KEY',    ':[*_ZXONs)N{&`&8Chl$!G^+i4G#FF|I@Y|+|W,) M+(4mS4@*w*V>uU3|nSvXSB');
define('NONCE_KEY',        'Jg%7a}aW|=nOc|!B/2nN`n]0m~ ;$1pZPmD#R2G&:# r*@;&T+XX{aM(OcjN`?y:');
define('AUTH_SALT',        '^MkW#)k_|L$n|;C1QsL[qka_a2u[ep&+i5J@i}T(|;pTM;^!$Y1o#+<;m 5)cUM}');
define('SECURE_AUTH_SALT', '|>.H3&TP}1Y58!yA!|4(M<_2=TkI-B4QHU-~!m|[eZi+^n:&410<O8Xws!z`Zg-c');
define('LOGGED_IN_SALT',   '=59z]W@Hw;vw.u~`T<*QrX:mo)*L,;zhk=XTv<F{w>q2aN~#+Y:yLg&xHf>u EJ%');
define('NONCE_SALT',       'zl9(w@$I@r;[^iwEK;?*9gH-xuv8--!->jiHB}AJrbO*msbM~-FNJ8t(SKtCjB==');
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique. 
 * N'utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés!
 */
$table_prefix  = 'wp_';
define('TABLE_PREFIX', $table_prefix);

/** 
 * Pour les développeurs : le mode deboguage de WordPress.
 * 
 * En passant la valeur suivante à "true", vous activez l'affichage des
 * notifications d'erreurs pendant votre essais.
 * Il est fortemment recommandé que les développeurs d'extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de 
 * développement.
 */ 
define('WP_DEBUG', false); 

/* C'est tout, ne touchez pas à ce qui suit ! Bon blogging ! */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');