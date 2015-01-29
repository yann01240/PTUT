<?php
/*
  Plugin Name: Ecole
 */

new EcolePlugin();

function str_getcsv_custom($input, $delimiter = ';', $enclosure = '"') {
    return str_getcsv($input, $delimiter, $enclosure);
}

class EcolePlugin {

    public function __construct() {
        add_action('admin_menu', array($this, 'EcolePlugin_Menu'));
    }

    public function EcolePlugin_Menu() {
        add_menu_page('Ecole', 'Ecole', 'manage_options', 'EcolePlugin', array($this, 'EcolePlugin_Page'));
    }

    private function EcolePlugin_Ajout($nom) {
        $group_id = $GLOBALS['wpdb']->get_row("SHOW TABLE STATUS WHERE Name='" . $GLOBALS['wpdb']->groups_rs . "'")->Auto_increment;
        $cat_nicename = strtolower($nom);
        $cat_desc = '';
        $cat_classes = get_cat_ID('classes');
        $catarr_parent = array('cat_name' => $nom, 'category_description' => $cat_desc, 'category_nicename' => $cat_nicename, 'category_parent' => $cat_classes);
        $cat_classe = wp_insert_category($catarr_parent);
        $catarr_tribune = array('cat_name' => 'Tribune Libre', 'category_description' => $cat_desc, 'category_nicename' => 'tribune_libre_' . $cat_nicename, 'category_parent' => $cat_classe);
        $catarr_vie_de_la_classe = array('cat_name' => 'Vie de la Classe', 'category_description' => $cat_desc, 'category_nicename' => 'vie_de_la_classe_' . $cat_nicename, 'category_parent' => $cat_classe);
        $cat_vie_de_la_classe = wp_insert_category($catarr_vie_de_la_classe);
        $cat_tribune = wp_insert_category($catarr_tribune);
        add_role(strtolower($nom), $nom, get_role("contributor")->capabilities);

        $post_id_classes = $GLOBALS['wpdb']->get_var("SELECT post_id FROM " . $GLOBALS['wpdb']->postmeta . " WHERE meta_key='_menu_item_object_id' and meta_value=$cat_classes");

        $item_classe = array(
            'menu-item-object-id' => $cat_classe,
            'menu-item-object' => 'category',
            'menu-item-parent-id' => $post_id_classes,
            'menu-item-type' => 'taxonomy',
            'menu-item-status' => 'publish',
        );
        $id_menu = wp_get_nav_menu_object('menu_principal')->term_id;
        $post_id_classe = wp_update_nav_menu_item($id_menu, 0, $item_classe);

        $item_vie_de_la_classe = array(
            'menu-item-object-id' => $cat_vie_de_la_classe,
            'menu-item-object' => 'category',
            'menu-item-parent-id' => $post_id_classe,
            'menu-item-type' => 'taxonomy',
            'menu-item-status' => 'publish',
        );

        wp_update_nav_menu_item($id_menu, 0, $item_vie_de_la_classe);

        $item_tribune = array(
            'menu-item-object-id' => $cat_tribune,
            'menu-item-object' => 'category',
            'menu-item-parent-id' => $post_id_classe,
            'menu-item-type' => 'taxonomy',
            'menu-item-status' => 'publish',
        );

        wp_update_nav_menu_item($id_menu, 0, $item_tribune);

        $GLOBALS['wpdb']->insert($GLOBALS['wpdb']->user2role2object_rs, array(
            'group_id' => $group_id,
            'role_name' => 'post_contributor',
            'role_type' => 'rs',
            'scope' => 'term',
            'src_or_tx_name' => 'category',
            'obj_or_term_id' => $cat_tribune
                ), array(
            '%d',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%d'));
        ?>
        <p style="color:green">Ajout de la Classe Terminée</p>
        <?php
    }

    private function EcolePlugin_Suppression($nom) {
        $cat_archive = get_cat_ID('archives');
        $post_id_archive = $GLOBALS['wpdb']->get_var("SELECT post_id FROM " . $GLOBALS['wpdb']->postmeta . " WHERE meta_key='_menu_item_object_id' and meta_value=$cat_archive");
        $posts_id = $GLOBALS['wpdb']->get_col("SELECT ID FROM " . $GLOBALS['wpdb']->posts . " WHERE post_parent=$post_id_archive");


        $cat_classe = category_exists($nom, $cat_archive);
        $cat_tribune = category_exists("Tribune Libre", $cat_classe);
        $cat_vie = category_exists("Vie de la Classe", $cat_classe);

        $posts = get_posts(array('category' => $cat_classe, $cat_tribune, $cat_vie));
        foreach ($posts as $post) {
            wp_delete_post($post->ID, true);
        }
        wp_delete_category($cat_vie);
        wp_delete_category($cat_tribune);
        wp_delete_category($cat_classe);
        ?>
        <p style="color:green">Suppression Terminée</p>
        <?php
    }

    private function EcolePlugin_Archivage($nom) {
        if (category_exists($nom, category_exists('Archives')) != 0) {
            ?>
            <p style="color:red">Une classe portant le même nom avait déjà été archiver donc une suppression a été lancer</p>
            <?php
            $this->EcolePlugin_Suppression($nom);
        }
        $current_id = get_current_user_id();

        $cat_classes = get_cat_ID('classes');

        $cat_classe = category_exists($nom, $cat_classes);

        $cat_archive = get_cat_ID('archives');
        $catarr_classe = array('cat_ID' => $cat_classe, 'category_parent' => $cat_archive);
        wp_update_category($catarr_classe);

        $id_menu = wp_get_nav_menu_object('menu_principal')->term_id;


        $id_classe = wp_get_nav_menu_object(strtolower($nom))->term_id;

        $post_id_classe = $GLOBALS['wpdb']->get_var("SELECT post_id FROM " . $GLOBALS['wpdb']->postmeta . " WHERE meta_key='_menu_item_object_id' and meta_value=$cat_classe");

        $post_id_archive = $GLOBALS['wpdb']->get_var("SELECT post_id FROM " . $GLOBALS['wpdb']->postmeta . " WHERE meta_key='_menu_item_object_id' and meta_value=$cat_archive");

        $item_classe = array(
            'menu-item-object-id' => $cat_classe,
            'menu-item-object' => 'category',
            'menu-item-parent-id' => $post_id_archive,
            'menu-item-type' => 'taxonomy',
            'menu-item-status' => 'publish'
        );

        $post_id_classe = wp_update_nav_menu_item($id_menu, $post_id_classe, $item_classe);

        $users_id = $GLOBALS['wpdb']->get_col("SELECT user_id FROM " . $GLOBALS['wpdb']->usermeta . " WHERE meta_key='wp_capabilities' AND meta_value LIKE '%\"$nom\"%'");
        foreach ($users_id as $id) {
            wp_delete_user($id, $current_id);
        }
        remove_role(strtolower($nom));

        $group_id = $GLOBALS['wpdb']->get_var("SELECT ID FROM " . $GLOBALS['wpdb']->groups_rs . " WHERE group_meta_id LIKE 'wp_role_$nom'");
        $GLOBALS['wpdb']->delete($GLOBALS['wpdb']->user2role2object_rs, array('group_id' => $group_id), array('%d'));
        ?>
        <p style="color:green">Archivage Terminée</p>
        <?php
    }

    private function EcolePlugin_Import($path_fichier) {
        ini_set('auto_detect_line_endings', TRUE); /// (PHP's detection of line endings) write at the top.


        $csvrows = array_map('str_getcsv_custom', file($path_fichier));
        $csvheader = array_shift($csvrows);
        foreach ($csvheader as &$value) {
            $value = strtolower($value);
        }
        $csv = array();
        foreach ($csvrows as $row) {
            $csv[] = array_combine($csvheader, $row);
        }

        $cat_classes = get_cat_ID('classes');
        foreach ($csv as $user) {
            echo $user['prenom'] . "." . $user['nom'] . "<br>";
            if (category_exists($user['classe'], $cat_classes) == 0) {
                ?>
                <p style="color:green"><?php $user['classe'] ?> n'existait pas donc l'ajout a été lancer</p>
                <?php
                $this->EcolePlugin_Ajout($user['classe']);
            }
            $userdata = array(
                'user_pass' => $user['date de naissance'],
                'user_login' => $user['prenom'] . "." . $user['nom'],
                'user_email' => $user['prenom'] . "." . $user['nom'] . '@test.com',
                'first_name' => $user['prenom'],
                'last_name' => $user['nom'],
                'role' => strtolower($user['classe'])
            );
            wp_insert_user($userdata);
        }
        ?>
        <p style="color:green">Import Terminé</p>
        <?php
    }

    public function EcolePlugin_Page() {
        ?>
        <h1>Ajouter une Classe</h1>
        <form action="#" method="POST">
            <?php
            if (!isset($_POST['classe']) || $_POST['classe'] == '') {
                unset($_POST['classe']);
                ?>
                <p style="color:red">Pas de nom de classe renseignée</p>
                <?php
            } else if (isset($_POST['classe']) && $_POST['classe'] != '' && category_exists($_POST['classe'], category_exists('Classes')) == 0) {
                ?>
                <p style="color:green">Ajout de la Classe en cours</p>
                <?php
                $this->EcolePlugin_Ajout($_POST['classe']);
            } else if (category_exists($_POST['classe'], category_exists('Classes')) != 0) {
                ?>
                <p style="color:green">Cette Classe existe déja</p>
                <?php
            }
            ?>
            <label for="inputClasse">Classe</label>
            <input type="text" id="inputClasse" placeholder="Classe" name="classe" required>
            <button type="submit">Executer</button>
        </form>
        <br>
        <h1>Archiver une classe</h1>
        <form action="#" method="POST">
            <?php
            if (!isset($_POST['archivage_classe']) || $_POST['archivage_classe'] == '') {
                unset($_POST['archivage_classe']);
                ?>
                <p style="color:red">Pas de nom de classe renseignée</p>
                <?php
            } else if (isset($_POST['archivage_classe']) && $_POST['archivage_classe'] != '' && category_exists($_POST['archivage_classe'], category_exists('Classes')) != 0) {
                ?>
                <p style="color:green">Archivage de la classe en cours</p>
                <?php
                $this->EcolePlugin_Archivage($_POST['archivage_classe']);
            } else if (category_exists($_POST['archivage_classe'], category_exists('Classes')) == 0) {
                ?>
                <p style="color:green">Cette Classe n'existe pas</p>
                <?php
            }
            ?>
            <label for="inputClasse">Classe</label>
            <select> 
                <option value="">Selectionner</option> 
                <?php
                $cat_classes = get_cat_ID('classes');
                $categories = wp_get_nav_menus(array('parent'=>'94'));
                foreach ($categories as $category) {
                    $option = '<option value="' . $category->cat_name . '">';
                    $option .= $category->cat_name;
                    $option .= '</option>';
                    echo $option;
                }
                ?>
            </select>
            <?php wp_list_categories(); ?> 
            <input type="text" id="inputClasse" placeholder="Classe" name="archivage_classe" required>
            <button type="submit">Executer</button>
        </form>
        <h1>Supprimer une classe Archivé</h1>
        <form action="#" method="POST">
            <?php
            if (!isset($_POST['suppr_classe']) || $_POST['suppr_classe'] == '') {
                unset($_POST['suppr_classe']);
                ?>
                <p style="color:red">Pas de nom de classe renseignée</p>
                <?php
            } else if (isset($_POST['suppr_classe']) && $_POST['suppr_classe'] != '' && category_exists($_POST['suppr_classe'], category_exists('Archives')) != 0) {
                ?>
                <p style="color:green">Suppression de la classe en cours</p>
                <?php
                $this->EcolePlugin_Suppression($_POST['suppr_classe']);
            } else if (category_exists($_POST['suppr_classe'], category_exists('Classes')) == 0) {
                ?>
                <p style="color:green">Cette Classe n'existe pas</p>
                <?php
            }
            ?>
            <label for="inputClasse">Classe</label>
            <input type="text" id="inputClasse" placeholder="Classe" name="suppr_classe" required>
            <button type="submit">Executer</button>
        </form>
        <h1>Importer un fichier Eleve</h1>
        <form action="#" method="POST" enctype="multipart/form-data">
            <?php
            if (isset($_FILES['fichier'])) {
                if ($_FILES['fichier']['error'] == UPLOAD_ERR_NO_FILE) {
                    echo "fichier manquant.";
                } else if ($_FILES['fichier']['error'] == UPLOAD_ERR_INI_SIZE) {
                    echo "fichier dépassant la taille maximale autorisée par PHP.";
                } else if ($_FILES['fichier']['error'] == UPLOAD_ERR_FORM_SIZE) {
                    echo "fichier dépassant la taille maximale autorisée par le formulaire.";
                } else if ($_FILES['fichier']['error'] == UPLOAD_ERR_PARTIAL) {
                    echo "fichier transféré partiellement.";
                } else if ($_FILES['fichier']['error'] == UPLOAD_ERR_OK) {
                    $extensions_valides = array('csv');
                    $extension_upload = strtolower(substr(strrchr($_FILES['fichier']['name'], '.'), 1));
                    if (in_array($extension_upload, $extensions_valides)) {
                        $this->EcolePlugin_Import($_FILES['fichier']['tmp_name']);
                    } else {
                        echo "extension non valide.";
                    }
                }
            }
            ?>
            <label for="inputFile">Sélectionner un fichier</label>
            <input type="file" id="inputFile" name="fichier" required>
            <button type="submit">Executer</button>
        </form>
        <?php
    }

}

/*
UPLOAD_ERR_NO_FILE : fichier manquant.
UPLOAD_ERR_INI_SIZE : fichier dépassant la taille maximale autorisée par PHP.
UPLOAD_ERR_FORM_SIZE : fichier dépassant la taille maximale autorisée par le formulaire.
UPLOAD_ERR_PARTIAL : fichier transféré partiellement.
*/
