<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://kiwop.com
 * @since      1.0.0
 *
 * @package    Prisma_Custom_Search
 * @subpackage Prisma_Custom_Search/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Prisma_Custom_Search
 * @subpackage Prisma_Custom_Search/public
 * @author     Antonio Sánchez (kiwop) <antonio@kiwop.com>
 */
class Prisma_Custom_Search_Public {

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

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */



    private $wpdb;
    private $excluded_post_types;

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
            'astra-advanced-hook',
            'wpcf7_contact_form',
        );

        global $wpdb;

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->wpdb = $wpdb;
        
        add_shortcode('showPrismaSearchForm', array($this, 'showPrismaSearchForm'));

        add_action('init', array($this,'permitir_cors'));       
        add_action('rest_api_init', array($this,'my_custom_rest_cors'), 15 );
        
        add_action('wp_ajax_getRecurses', array($this, 'getRecurses') );          
        add_action('wp_ajax_nopriv_getRecurses', array($this, 'getRecurses') );          

        add_action('wp_ajax_getDataSharedSearchPrisma', array($this, 'getDataSharedSearchPrisma') );  
        add_action('wp_ajax_nopriv_getDataSharedSearchPrisma', array($this, 'getDataSharedSearchPrisma') );          

        add_action('wp_ajax_getRecursesForRemoteSites', array($this, 'getRecursesForRemoteSites') );          
        add_action('wp_ajax_nopriv_getRecursesForRemoteSites', array($this, 'getRecursesForRemoteSites') );          
        
        add_filter('the_post', array($this, 'custom_display_fields_based_on_post_type'));
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Prisma_Custom_Search_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Prisma_Custom_Search_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/prisma-custom-search-public.css', array(), $this->version, 'all' );
        $script_path = plugin_dir_path(__FILE__) . 'css/prisma-custom-search-public.css';
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/prisma-custom-search-public.css', array(), filemtime($script_path), 'all' );


        $dir = plugin_dir_url( __FILE__ );
        $dir = trim($dir, 'public/');
        wp_enqueue_style('bootstrap', $dir . '/vendor/twbs/bootstrap/dist/css/bootstrap.min.css', array(), false, 'all');



	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Prisma_Custom_Search_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Prisma_Custom_Search_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
        $file =  'js/prisma-custom-search-public.js';
        $script_path = plugin_dir_path(__FILE__) . $file;
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . $file, array( 'jquery' ), filemtime($script_path), false );

        $dir = plugin_dir_url( __FILE__ );
        $dir = trim($dir, 'public/');

        wp_enqueue_script('bootstrap',  $dir . '/vendor/twbs/bootstrap/dist/js/bootstrap.min.js', null, null, true);



        // link ajax calls with functions and vars por JS
        wp_localize_script(
            $this->plugin_name, 
            'prisma_custom_search_globals', 
            array(
                'ajax_url'  => admin_url('admin-ajax.php'),
                'admin_url' => admin_url( 'admin.php' ),
                
                // aqui todos los nonce del backend                
                'getRecurses' => wp_create_nonce('getRecurses_nonce' ),       
                'loader' => plugin_dir_url("") . '/prisma-custom-search/public/img/ajax-loader-mini.gif',
                'loader_xl' => plugin_dir_url("") . '/prisma-custom-search/public/img/ajax-loader.gif',

            )
        );
	}



    
    public function custom_display_fields_based_on_post_type() {

        ob_start();

        if (is_single()) {
               
            echo get_the_content();
            
            $post_type = get_post_type();
            $post_id = get_the_ID();
            
            /*
            if (in_array($post_type, $this->excluded_post_types)) {
                $contenido = ob_get_contents();
                ob_end_clean();
                return $contenido;                
            }
            */
            echo "<br /><hr />
                <table class='kiwopPrismaTableACF'>
                <tbody>
            ";
            // Obtén todos los grupos de campos
            $field_groups = acf_get_field_groups();

            if ($field_groups) {
                foreach ($field_groups as $field_group) {
                    // Obtén los campos asociados al grupo de campos y tipo de post específicos
                    $group_fields = acf_get_fields($field_group['ID']);
                    
                    if ($group_fields) {

                        foreach ($group_fields as $field) {
                            //$field_name = $field['name'];
                            $fo = get_field_object($field['key'],$post_id);

                            $value = $fo['value'];
                            $name = $fo['name'];
                            
                            if (strpos($name,'app_download_') !== false && !empty($value) && $post_type == 'app' ) {
                                $button = "<a href='".$value."'> Descarrega </a>";
                                $value = $button;
                            }

                            if ($post_type == 'app' && $name == 'cost' && (int)$value == 0 ) {                                        
                                $value = "Gratuïta";
                            }

                            if ($field['type']=='url' && !empty($value) ) {
                                $button = "<a style='text-decoration:none' href='".$value."'> Enllaç </a>";
                                $value = $button;
                            }
                            
                            if ( !empty($value) && $value!='No aplica' && $value!='No aplicable'  ) {
                                if (is_array($value)) {
                                    $value = "· " . implode('<br />· ',$value);
                                }
                                echo "<tr>";
                                echo "  <th>".$field['label']."</th>";
                                echo "  <td>". $value ."</td>";
                                echo "</tr>";
                                
                            }
                        }                       
                    }
                }
            } else {
                echo "No se encontraron grupos de campos ACF.";
            }  
            echo "</tbody></table>";                       
        }
        
        $contenido = ob_get_contents();
        ob_end_clean();
        return $contenido;

        //echo "MIERDA";
    }
    
    


    public function showPrismaSearchForm() 
    {        

        $postTypes = $this->getPostTypes();

        $searchFormFields = $this->searchFormFields($postTypes);
        
        
        $postTypesHTML = $this->getPostTypesHTMLCache();
        
        if (strlen($postTypesHTML) == 0) {
            $postTypesHTML = $this->getPostTypesHTML($postTypes);
            $this->savePostTypesHTMLCache($postTypesHTML);
        }

        $postTypesHTML = $this->getPostTypesHTML($postTypes);

        $results = $this->getRecurses(false);

        
        ob_start();

        include_once(plugin_dir_path(__FILE__) . 'partials/prisma-custom-search-public-display.php');
        
        return ob_get_clean();
       
    }


    
    private function savePostTypesHTMLCache($postTypesHTML)
    {
        $file = plugin_dir_path(__FILE__);       
        $file = trim($file, 'public/');
        $file .= DIRECTORY_SEPARATOR .'cache' .DIRECTORY_SEPARATOR. 'postTypesHTMLCache.html';
        
        if (substr($file, 0, 2) != 'C:' && substr($file, 0, 1) != '/') {
            $file = "/" . $file;
        }

        if (!file_put_contents($file, $postTypesHTML)) {
            $error  = "Error plugin prisma-custom-search, file_put_contents no puede escribir en:<br /> " . $file;
            $error .= "\nAconsejamos revise los permisos de escritura de la carpeta:<br /> ";
            error_log($error);
        }
    }


    private function getPostTypesHTMLCache()
    {
        $file = plugin_dir_path(__FILE__);       
        $file = trim($file, 'public/');
        $file .= DIRECTORY_SEPARATOR . 'cache' .DIRECTORY_SEPARATOR. 'postTypesHTMLCache.html';
        
        if (substr($file, 0, 2) != 'C:' && substr($file, 0, 1) != '/') {
            $file = "/" . $file;
        }

        if (!file_exists($file)) {
            return false;
        }

        // Obtener la fecha de modificación del archivo
        $fecha_modificacion = filemtime($file);
        
        // Obtener la fecha actual
        $fecha_actual = time();
        
        // Calcular la diferencia en segundos
        $diferencia_en_segundos = $fecha_actual - $fecha_modificacion;
        
        // Definir el límite en media hora
        $limite_una_hora = 1800;
        
        // Verificar si la diferencia es mayor a una hora
        if ($diferencia_en_segundos > $limite_una_hora) {
            return false;
        } else {
            return file_get_contents($file);
        }
    }

    private function getPostTypes()
    {
        $arr_group_fields_by_type = json_decode(get_option('prisma_custom_search_group_fields_by_type'),true);
        
        $post_types = get_post_types();

        // Excluye los tipos de contenido internos de WordPress
        foreach ($post_types as $key => $value) {
            if (in_array($value, $this->excluded_post_types)) {
                unset($post_types[$key]);
            }
        }
        //var_dump($post_types);die();
        foreach ($post_types as $key => $value) {
            if (is_array($arr_group_fields_by_type) && isset($arr_group_fields_by_type[$key]) ) {
                $post_types[$key] = $arr_group_fields_by_type[$key];
            } else {
                $post_types[$key] = '';
            }
        }     
        
        
        return $post_types;
    }

    private function getPostTypesHTML($data)
    {
        $ptypes = [];
        foreach($data as $post_type => $ACFGroupFieldsID ) {
            $ptypes[] = "'".$post_type."'";
        }
        $str_ptypes = implode(",",$ptypes);


        $ordered_data = [];
        $arr_ACFGroupFieldsIDs = [];
        foreach($data as $post_type => $ACFGroupFieldsID ) {
            $sql = "SELECT COUNT(*) as num_post_pertype FROM ".$this->wpdb->prefix."posts WHERE post_type = '$post_type' AND post_status = 'publish'";
            $num_post_pertype = $this->wpdb->get_var($sql);
            if ($num_post_pertype > 0) {
                $ordered_data[$post_type] = $num_post_pertype;
                $arr_ACFGroupFieldsIDs[$post_type] = $ACFGroupFieldsID;
            }
        }
        
        arsort($ordered_data);
       // var_dump($ordered_data,$arr_ACFGroupFieldsIDs);die();

        $sql = "
            SELECT COUNT(*) as num_post_pertype 
            FROM ".$this->wpdb->prefix."posts 
            WHERE post_type in (".$str_ptypes.") 
            AND post_status = 'publish'
        ";
        $num_post_pertype = $this->wpdb->get_var($sql);

        $html = '<select class="fieldSearch" name="post_types" id="select_post_type" >';
        $html .= '<option value="">Tots ('.$num_post_pertype.')</option>';

        foreach($ordered_data as $post_type => $qty ) {
            if (isset($data[$post_type])) {
                $ACFGroupFieldsID = $arr_ACFGroupFieldsIDs[$post_type];
                $html .= '<option data-value="'.$ACFGroupFieldsID.'" value="'.$post_type.'">'.$post_type.' ('.$qty.')</option>';
            }
        }
        $html .= '</select>';

        return $html ;
    }

    private function searchFormFields($postTypes)
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

        $typeGroups = json_decode(get_option('prisma_custom_search_group_fields_by_type'));
        
        

        $html = '';
        
        
        foreach ($typeGroups as $postType => $ACFGroupFieldsID) {
            if (strpos($ACFGroupFieldsID,',') !== false) {
                $arr_ACFGroupFieldsID = explode(',', $ACFGroupFieldsID);
            } else {
                $arr_ACFGroupFieldsID = [$ACFGroupFieldsID];
            }

            foreach($arr_ACFGroupFieldsID as $ACFGroupFieldsID) {
                $ACFGroupFieldsID = trim($ACFGroupFieldsID);
                $fields = acf_get_fields($ACFGroupFieldsID);

                
                if (strlen($ACFGroupFieldsID) && count($fields)) {
                    $html .= '<div data-post_type="'.$postType.'" class="hidden '.$postType.' prismaGroupfield '.$ACFGroupFieldsID.'" id="div_'.$ACFGroupFieldsID.'" >';
                    $i = 0;
                    foreach ($fields as $field) {  
                        if (in_array($field['type'],$forbidden_fields_search)) {
                            continue;
                        }                              
                        if (!in_array($field['name'],$selectedFields)) {
                            continue;
                        }                              
                        $html .= $this->getFieldForm($field, $i,$ACFGroupFieldsID,$postType);
                        $i++;
                    }
                    $html .= '</div>';
                }
            }
        }        



        return $html;
    }

    private function getFieldForm($field_object, $index, $acfGrupo,$postType)
    {
      
        $html  = '<label for="PrismaSearcForm_'.$index.'" >'.$field_object['label'].'</label><br />';
        if ($field_object['type']=='select') {
            $html .= '<select class="fieldSearch" name="'.$postType.'_'.$acfGrupo.'_acf_'.$field_object['name'].'"  >';
            $html .= '  <option value="">Tots</option>';
            foreach($field_object['choices'] as $choice) {
                $html .= '<option value="'.$choice.'">'.$choice.'</option>';
            }
            $html .= '</select><br />';
        } else {
            $html .= '<input class="fieldSearch" type="text" name="'.$postType.'_'.$acfGrupo.'_acf_'.$field_object['name'].'" id="PrismaSearcForm_'.$index.'" value="" /><br />';
        }
        
        return $html;
    }


    
    public function getResultsHTML($dataRecurses = null)
    {
        if (empty($data)) {
            $data = $this->getData();
            $dataRecurses = $data['data'];
        }
        
        //var_dump($sql,$results);die();
        
        ob_start();
        
        include_once(plugin_dir_path(__FILE__) . 'partials/listadoBusqueda.php');
        
        return ob_get_clean();
        
        return $html;       
    }
    
    
    public function getRecurses($return_json = true) { 
        
        if ($return_json) {
            check_ajax_referer( 'getRecurses_nonce' );
        }
        
        try {
            $dataRecurses = $this->getData();
        } catch ( Exception $e) {
            $dataRecurses = $e->getMessage();
        }
        
        //var_dump($dataRecurses); die();
        
        if (isset($dataRecurses['data'])) {
            
            $listado_html = $this->getResultsHTML($dataRecurses['data']);
            
            $dataRecurses['error'] = false;
            $dataRecurses['html'] = $listado_html;
            $dataRecurses['paginator'] = self::getPagination($dataRecurses);
            unset($dataRecurses['data']);
            
            if ($return_json===false) {
                return $dataRecurses;
            } else {
                status_header(200);
                echo wp_send_json($dataRecurses);
            }
            
        } else {
            status_header(200);
            echo wp_send_json([
                "error" => true,
                "message" => $dataRecurses,
            ]);
        }
    }
    
    private function getData()
    {
        $postTypes = $this->getPostTypes();
        $ptypes = [];
        foreach($postTypes as $post_type => $ACFGroupFieldsID ) {
            $ptypes[] = "'".esc_sql($post_type)."'";
        }
        $str_ptypes = implode(",",$ptypes);

        
        ////////////////////////////////////////////////////////////////////////////////////////
        $get_params = @$_POST['data'] ? $_POST['data'] : (@$_GET['data'] ? $_GET['data'] : '');
        parse_str($get_params, $params); 

        $page = @$_POST['paged'] ? $_POST['paged'] : (@$_GET['paged'] ? $_GET['paged'] : 1);
        $order_by = @$params['prismaOrderBy'] ? $params['prismaOrderBy'] : 'p.post_date';
        $search = @$params['prisma-custom-search'] ? $params['prisma-custom-search'] : (isset($_GET['prisma-custom-search']) ? $_GET['prisma-custom-search'] : '');
        
        $post_type = isset($_POST['post_type']) ? $_POST['post_type'] : (isset($_GET['post_type']) ? $_GET['post_type'] : '');
        $acfGroups = isset($_POST['acfGroups']) ? $_POST['acfGroups'] : (isset($_GET['post_type']) ? $_GET['post_type'] : '');
       
        $sentido = 'DESC';
        if ($order_by == 'p.post_title') {
            $sentido = 'ASC';
        }
        
        if (!is_numeric($page)) $page = 1;
        
        $per_page = 12;
        
        $offset = $per_page * ($page-1);
        
        ////////////////////////////////////////////////////////////////////////////////////////
        
        
        
        
        ##################  condiciones filtros ############################

        $where  = ' 1=1 ';
        $where .= " AND p.post_status = 'publish' ";
        if (strlen($post_type)) {
            $where .= " AND p.post_type = '".esc_sql($post_type)."' ";
        } else {
            $where .= " AND p.post_type in (".$str_ptypes.") ";
        }

        if ($search != '') {
            $where .= " AND (p.post_title LIKE '%$search%' OR p.post_content LIKE '%$search%') ";
        }
        
        //var_dump($params); die();

        $arr_acfgrupos = explode(',',$acfGroups);

        $post_meta_joins = [];
        $i = 0;
        
        
        foreach ($arr_acfgrupos as $acfGroup) {
            $acfGroup = trim($acfGroup);
            
            // app_group_656dae56539ab_acf_ambit_competencial

            foreach($params as $field => $value) {
                
                //var_dump($params,$acfGroup); die();

                if ( strpos($field,$acfGroup) === false || strpos($field,$post_type) === false || empty($value) ) {
                    # es un campo de otro grupo distinto o de un tipo distinto al que se esta haciendo la busqueda
                    # esto ocurre porque hay campos de busqueda que se repiten en distintos grupos
                    continue; 
                } else {
                    $field = str_replace($acfGroup.'_','',$field);
                    $field = substr($field,strlen($post_type)+1);
                }
                
                //var_dump($field,$value); die();
    
                
                if (substr($field,0,4) == 'acf_'  ) {
                    
                    $campo_busqueda = substr($field,4);
                    $where .= " AND pm$i.meta_key = '$campo_busqueda' AND pm$i.meta_value LIKE '%".esc_sql($value)."%' ";
                    $post_meta_joins[] =  " LEFT JOIN ".$this->wpdb->prefix."postmeta pm$i ON pm$i.post_id = p.ID and pm$i.meta_key = '".$campo_busqueda."' ";
                    $i++;
                }
            }
        }
        
        
        #####################################################################
        $sql = " SELECT p.ID,p.post_title,p.post_type,p.post_modified FROM ".$this->wpdb->prefix."posts p ";
        
        if (count($post_meta_joins)) {
            $sql .= implode("\n",$post_meta_joins);
        }
        
        $sql .= " WHERE $where ";
        $sql .= " ORDER BY ".$order_by."  " . $sentido;                
        $sql .= " LIMIT $offset, $per_page ";
        
        //var_dump($sql); die();
        
        $data = $this->wpdb->get_results($sql);

        #####################################################################
        
        $sqlTotal  = " SELECT count(*) as total FROM ".$this->wpdb->prefix."posts p  ";
        if (count($post_meta_joins)) {
            $sql .= implode("\n",$post_meta_joins);
        }
        $sqlTotal .= " WHERE " . $where;
        
        
        $total_rows = $this->wpdb->get_var($sqlTotal);
        #####################################################################
        
        $result = [
            "data" => $data,
            "page" => $page,
            "per_page" => $per_page,
            "total" => $total_rows,
        ];         
        
        return $result;        

    }


    private static function getPagination($result) { 
        $page = $result['page'];
        $per_page = $result['per_page'];
        $total = $result['total'];        

        $total_pages = ceil($total / $per_page);

        //if (!$total) return 'R is empty.';

        $prior_class = 'a_navigator';
        $next_class = 'a_navigator';

        if ($page == 1) {
            $prior_class = ' a_navigator_disabled ';
        }

        if ($page+1 > ($total_pages) ) {
            $next_class = ' a_navigator_disabled ';
        } 
        $limite = false;
        if ($page >= $total_pages ) {
            $page = $total_pages;
            $limite = true;
        }

        $inicio = true;
        if ($page > 1 ) {
            $inicio = false;
        }

        if ($total>1) {
            $label = " Registres ";
        } else {
            $label = " Registre ";
        }

        if ($total_pages>0) {
            $html = '<select id="kiwop_prisma_pagina_actual" >';
            for($i=1; $i <= $total_pages; $i++) { 
                if ($i==$page) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                $html .= "<option $selected value='$i'>$i</option>";
            }
            $html .= '</select><span id="loader_select_page"></span>';
        } else {
            $html = '1';
            $total_pages = 1;
        }

        $left_arrow = plugin_dir_url("") . '/prisma-custom-search/public/img/left.png';
        $right_arrow = plugin_dir_url("") . '/prisma-custom-search/public/img/right.png';
        $loader = plugin_dir_url("") . '/prisma-custom-search/public/img/ajax-loader-mini.gif';
        $searchedValue = isset($_GET['prisma-custom-search']) ? $_GET['prisma-custom-search'] : '';

        ob_start();

        ?>      
            <div class="row"> 
                <div id="prisma-custom-search-ajax-box" class="col-xs-12 col-md-6 hidden"> 
                    <input class="fieldSearch" type="text" id="prisma-custom-search-ajax" value="<?php echo $searchedValue; ?>" placeholder="Cerca per text i prem Enter" />    
                    <img id="loader_text" src="<?php echo $loader ?>" alt="loader" class="hidden" />
                </div>                
                <div class="col-xs-12"> 
                    Total: <strong><?php echo $total ?></strong> <?php echo $label ?>  &nbsp;&nbsp;&nbsp;&nbsp;
                    
                    <?php if(!$inicio) { ?>
                    <img id="loader_left" src="<?php echo $loader ?>" alt="loader" class="hidden" />
                    <span id="kiwopPrisma_prev" class="prior_row"  data-page="<?php echo $page ?>" 
                            data-per_page="<?php echo $per_page ?>" 
                            data-total="<?php echo $total ?>" > 
                        <img src="<?php echo $left_arrow ?>" class=" <?php echo $prior_class  ?> "/>
                    </span> 
                    <?php } ?>
                        
                    &nbsp;
                    <span class="pages_label">Pàg.
                        <?php echo $html . " de <strong>" . ($total_pages) ?></strong> &nbsp;
                    </span>                
                    
                    <?php if(!$limite) { ?>
                        <span id="kiwopPrisma_next" class="next_row" data-page="<?php echo ($page) ?>" 
                        data-per_page="<?php echo $per_page ?>" 
                        data-total="<?php echo $total ?>" >
                        <img src="<?php echo $right_arrow ?>" class="<?php echo $next_class  ?> " />                       
                    </span>
                    <img id="loader_right" src="<?php echo $loader ?>" alt="loader" class="hidden" />
                    <?php } ?>
                </div> 

            </div>
        <?php 

        $contenido = ob_get_contents();
        ob_end_clean();
        return $contenido;
    }


    public function getDataSharedSearchPrisma()
    {        
        $result = $this->getRecurses(false);
              
        $response = json_encode( $result );
        echo $response;
        die();  
    
    }

    public function getRecursesForRemoteSites()
    {        
        

        $result = $this->getRecurses(false);
              
        $response = json_encode( $result );
        echo $response;
        die();  
    
    }

    
    function permitir_cors() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Max-Age: 86400'); // Tiempo de caché para prevenir verificaciones previas frecuentes
    
        // Manejar solicitudes OPTIONS de manera especial (para verificaciones previas CORS)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }

    function my_custom_rest_cors() {
        remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
        add_filter( 'rest_pre_serve_request', function( $value ) {
          header( 'Access-Control-Allow-Origin: *' );
          header( 'Access-Control-Allow-Methods: GET, POST, OPTIONS' );
          header( 'Access-Control-Allow-Headers: Content-Type');
          header( 'Access-Control-Allow-Credentials: true' );
          header( 'Access-Control-Expose-Headers: Link', false );
      
          return $value;
        } );
    }
    


}
