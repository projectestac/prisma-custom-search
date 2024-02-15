<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://kiwop.com
 * @since      1.0.0
 *
 * @package    Prisma_Custom_Search
 * @subpackage Prisma_Custom_Search/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Prisma_Custom_Search
 * @subpackage Prisma_Custom_Search/admin
 * @author     Antonio Sánchez (kiwop) <antonio@kiwop.com>
 */
class Prisma_Custom_Search_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	private $excluded_post_types;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

        $this->excluded_post_types = array(
            'post',
            'page', 
            'attachment', 
            'revision', 
            'nav_menu_item',
            'custom_css',
            'customize_changeset',
            'oembed_cache',
            'user_request',
            'wp_block',
            'wp_template',
            'wp_template_part',
            'wp_global_styles',
            'wp_navigation',
            'acf-taxonomy',
            'acf-post-type',
            'acf-ui-options-page',
            'acf-field-group',
            'acf-field',
            'wpcf7_contact_form',
            'astra-advanced-hook',
        );



		$this->plugin_name = $plugin_name;
		$this->version = $version;

        
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('wp_ajax_saveDataSearchFormConfig', array($this, 'saveDataSearchFormConfig') );            
        add_action('wp_ajax_saveGroupFieldsByType', array($this, 'saveGroupFieldsByType') );            
        add_action('wp_ajax_saveCssFront', array($this, 'saveCssFront') );            
        add_action('wp_ajax_restaurarCSSOriginal', array($this, 'restaurarCSSOriginal') );            

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

        $dir = plugin_dir_url( __FILE__ );
        $dir = trim($dir, 'admin/');
        $script_path = plugin_dir_path(__FILE__) . 'css/prisma-custom-search-admin.css';

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/prisma-custom-search-admin.css', array(), filemtime($script_path), 'all' );

        // Enqueue Font Awesome
        wp_enqueue_style('font-awesome',  $dir . '/vendor/fortawesome/font-awesome/css/all.min.css', array(), false, 'all');

        wp_enqueue_style('bootstrap', $dir . '/vendor/twbs/bootstrap/dist/css/bootstrap.min.css', array(), false, 'all');

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

        $script_path = plugin_dir_path(__FILE__) . 'js/prisma-custom-search-admin.js';
        $dir = plugin_dir_url( __FILE__ );
        $dir = trim($dir, 'admin/');

		wp_enqueue_script( 
            $this->plugin_name, 
            plugin_dir_url( __FILE__ ) . 'js/prisma-custom-search-admin.js', 
            array( 'jquery','jquery-ui-core', 'jquery-ui-dialog' ), 
            filemtime($script_path), 
            false
        );

        wp_enqueue_script('bootstrap',  $dir . '/vendor/twbs/bootstrap/dist/js/bootstrap.min.js', array('jquery'), null, true);

        // link ajax calls with functions and vars por JS
        wp_localize_script(
            $this->plugin_name, 
            'prisma_custom_search', 
            array(
                'ajax_url'  => admin_url('admin-ajax.php'),
                'admin_url' => admin_url( 'admin.php' ),
                'loader' => plugin_dir_url("") . '/prisma-custom-search/admin/img/ajax-loader-mini.gif',
                'loader_xl' => plugin_dir_url("") . '/prisma-custom-search/admin/img/ajax-loader.gif',
                
                // aqui todos los nonce del backend                
                'saveDataSearchFormConfig' => wp_create_nonce('saveDataSearchFormConfig_nonce' ),
                'saveGroupFieldsByType' => wp_create_nonce('saveGroupFieldsByType_nonce' ),
                'saveCssFront' => wp_create_nonce('saveCssFront_nonce' ),
                'restaurarCSSOriginal' => wp_create_nonce('restaurarCSSOriginal_nonce' ),
      
            )
        );

	}


    public function admin_menu() {
        
        // echo "mierda"; die();
         
        add_menu_page(
            'Kiwop',
            'Cercador personalitzat',
            'manage_options',
            'kiwop_prisma_custom_search',
            array($this, 'render_admin_page'),
            'dashicons-search', // Puedes cambiar el icono
            30 // Posición en el menú
        );
         
    }
 
     
    public function render_admin_page() {     

        $css = $this->getCSSContent();
        $searchFormFields = $this->searchFormFields();
        $groupFieldsByType = $this->getGroupFieldsByPostType();
        include_once('partials/prisma-custom-search-admin-display.php');
    }
    
    private function getCSSContent()
    {
        $dir = plugin_dir_path(__FILE__);
        $dir = trim($dir, 'admin/');
    	$file = $dir . '/public/css/prisma-custom-search-public.css';

        if (substr($file, 0, 2) != 'C:' && substr($file, 0, 1) != '/') {
            $file = "/" . $file;
        }

        $css = file_get_contents($file);

        return $css;
    }

    public function getGroupFieldsByPostType()
    {

        $arr_group_fields_by_type = json_decode(get_option('prisma_custom_search_group_fields_by_type'),true);
        
        
        $post_types = get_post_types();

        // Excluye los tipos de contenido internos de WordPress
        foreach ($post_types as $key => $value) {
            if (in_array($value, $this->excluded_post_types)) {
                unset($post_types[$key]);
            }
        }
        foreach ($post_types as $key => $value) {
            if (is_array($arr_group_fields_by_type) && isset($arr_group_fields_by_type[$key]) ) {
                $post_types[$key] = $arr_group_fields_by_type[$key];
            } else {
                $post_types[$key] = '';
            }
        }     
        
        return $post_types;
    }
    
    private function searchFormFields()
    {
        $selectedFields = get_option('prisma_custom_search_selected_fields_for_search_form');

        $forbidden_fields_search = [
            'gallery',
            'textarea',
            'file',
            'repeater',
            'url',
            'wysiwyg',
        ];

        
        $html  = '<table class="table" style="width:auto" >';
        $html .= '<thead>';
        $html .= '  <tr>';
        $html .= '      <th>Selecció</th>';
        $html .= '      <th>Nom camp</th>';
        $html .= '      <th>Camp type</th>';
        $html .= '  </tr>';

        $field_groups = acf_get_field_groups();

        foreach ($field_groups as $field_group) {
            $fields = acf_get_fields($field_group['key']);

            $i = 0;
            foreach ($fields as $field_object) {                                
                if (!in_array($field_object['type'],$forbidden_fields_search)) {
                    if (is_array($selectedFields) && in_array($field_object['name'], $selectedFields)) {
                        $field_object['checked'] = 'checked';
                    } else {
                        $field_object['checked'] = '';
                    }
                    $html .= $this->getCustomFieldHTML($field_object, $i);
                    $i++;
                }
            }
        }        
        
        $html .= '</table>';


        return $html;
    }

    private function getCustomFieldHTML($field_object, $index)
    {


        $html  = '<tr>';
        $html .= '  <td>';
        $html .= '      <input type="checkbox" '. $field_object['checked'].' name="fieldsSelected[]" value="'.$field_object['name'].'" />';
        $html .= '  </td>';
        $html .= '  <td>';
        $html .= '      <label for="PrismaSearcForm_'.$index.'" >'.$field_object['label'].'</label>';
        $html .= '  </td>';
        $html .= '  <td>';
        $html .=       $field_object['type'];
        $html .= '  </td>';
        $html .= '</tr>';
        
        return $html;
    }




    public function saveDataSearchFormConfig() 
    {
        check_ajax_referer( 'saveDataSearchFormConfig_nonce' );

        parse_str(@$_POST['data'], $data);  

        $selectedFields = array();

        foreach ($data['fieldsSelected'] as $index => $fieldName) {
            $selectedFields[] = $fieldName;
        }

                
        $res = update_option('prisma_custom_search_selected_fields_for_search_form', $selectedFields);

        if ($res) {
            $response['error'] = false;
            $response['message'] = 'Dades desades correctament';
        } else {
            $response['error'] = true;
            $response['message'] = 'Error en desar';
        }
       
        $response = json_encode( $response );
        echo $response;
        die();  

    }
    
    public function saveGroupFieldsByType() 
    {
        check_ajax_referer( 'saveGroupFieldsByType_nonce' );

        parse_str(@$_POST['data'], $data);  

        $arr_group_fields_by_type = array();

        foreach ($data as $key => $value) {
            if (!in_array($key, $this->excluded_post_types)) {
                $arr_group_fields_by_type[$key] = $value;
            }
        }
       
        $res = update_option('prisma_custom_search_group_fields_by_type', json_encode($arr_group_fields_by_type));

        if ($res) {
            $response['error'] = false;
            $response['message'] = 'Dades desades correctament';
        } else {
            $response['error'] = true;
            $response['message'] = 'Error en desar';
        }
       
        $response = json_encode( $response );
        echo $response;
        die();  
    }
    
    
    public function saveCssFront() 
    {
        check_ajax_referer( 'saveCssFront_nonce' );

        $cssFront = isset($_POST['cssFront']) ? $_POST['cssFront'] : '';        

        if (!empty($cssFront)) {
            $dir = plugin_dir_path(__FILE__);
            $dir = trim($dir, 'admin/');
            
            $file = $dir . '/public/css/prisma-custom-search-public.css';            
            $backupfile = $dir . '/public/css/prisma-custom-search-public.css.backup.original';
           
            if (!file_exists($backupfile)) {
                copy($file, $backupfile);
            }

            file_put_contents($file, $cssFront);            

            $response['error'] = false;
            $response['message'] = 'Contingut CSS guardat.';
        } else {
            $response['error'] = true;
            $response['message'] = 'El contingut CSS ha arribat buit, no es van salvar dades.';
        }
       
        $response = json_encode( $response );
        echo $response;
        die();  


    }
    
    
    public function restaurarCSSOriginal() 
    {
        check_ajax_referer( 'restaurarCSSOriginal_nonce' );


        $dir = plugin_dir_path(__FILE__);
        $dir = trim($dir, 'admin/');
        $file = $dir . 'public/css/prisma-custom-search-public.css';
        $backupfile = $dir . 'public/css/prisma-custom-search-public.css.backup.original';
        
        $file = str_replace("/",DIRECTORY_SEPARATOR,$file);
        $backupfile = str_replace("/",DIRECTORY_SEPARATOR,$backupfile);

        try {

            //var_dump($backupfile,$file); die();

            $res = copy($backupfile, $file);            

            if (!$res) {
                throw new Exception('Error en restaurar el CSS original, va fallar la funció copy de PHP.');
            }
            
        } catch ( Exception $e ) {
            $response['error'] = true;
            $response['message'] = 'Error en restaurar el CSS original. ' . $e->getMessage() ;
            $response = json_encode( $response );
            echo $response;
            die();  
        }


        $response['css'] = $this->getCSSContent();
        $response['error'] = false;
        $response['message'] = 'Contingut CSS restaurat.';
       
        $response = json_encode( $response );
        echo $response;
        die();  


    }
    
}
