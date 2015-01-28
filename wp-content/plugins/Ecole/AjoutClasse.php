<?php
/*
  Plugin Name: Ajout Classe
 */

new AjoutClasse();

class AjoutClasse {

    public function __construct() {
        add_action('admin_menu', array($this, 'AjoutClasse_Menu'));
    }

    public function AjoutClasse_Menu() {
        add_menu_page('Ajout Classe', 'Ajout Classe', 'manage_options', 'AjoutClasse', array($this, 'AjoutClasse_Page'));
    }

    private function AjoutClasse_Ajout($nom) {
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
        <p style="color:green">Ajout de la Classe Terminé</p>
        <?php
    }

    private function AjoutClasse_Archivage($nom) {
        $current_id = get_current_user_id ();
        $cat_classes = category_exists('Classes');
        $cat_classe = category_exists($nom, $cat_classes);
        $catarr_classe = array('cat_ID' => $cat_classe, 'category_parent' => $cat_classes);
        wp_update_category($catarr_classe);
        wp_update_na
        $users_id = $GLOBALS['wpdb']->get_col("SELECT user_id FROM ".$GLOBALS['wpdb']->usermeta." WHERE meta_key='wp_capabilities' AND meta_value LIKE '%\"$nom\"%'");
        foreach ($users_id as $id) {
            wp_delete_user($id, $current_id);
        }
        remove_role($nom);
        $group_id = $GLOBALS['wpdb']->get_var("SELECT ID FROM " . $GLOBALS['wpdb']->groups_rs . " WHERE group_meta_id LIKE 'wp_role_$nom'");
        $GLOBALS['wpdb']->delete($GLOBALS['wpdb']->user2role2object_rs, array('group_id' => $group_id), array('%d'));
    }

    public function AjoutClasse_Page() {
        ?>
        <h1>Ajouter une Classe</h1>
        <form action="#" method="POST">
            <?php
            if (!isset($_POST['classe']) || $_POST['classe'] == '') {
                unset($_POST['classe']);
                ?>
                <p style="color:red">Pas de nom de classe Renseigner</p>
                <?php
            } else if (isset($_POST['classe']) && $_POST['classe'] != '' && category_exists($_POST['classe'], category_exists('Classes')) == 0) {
                ?>
                <p style="color:green">Ajout de la Classe en cours</p>
                <?php
                $this->AjoutClasse_Archivage($_POST['classe']);
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
        <?php
    }

}
