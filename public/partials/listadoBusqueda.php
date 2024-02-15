<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://kiwop.com
 * @since      1.0.0
 *
 * @package    Prisma_Custom_Search
 * @subpackage Prisma_Custom_Search/public/partials
 */



if (isset($dataRecurses) && !empty($dataRecurses)) { ?>

    <?php foreach($dataRecurses as $post) { ?>                    
        <div class="mt-3 col-xs-6 col-sm-6 col-md-4 col-lg-3 elementPreview">
        <?php

            $link = get_permalink($post->ID);

            $title = strip_tags($post->post_title);
            $title = substr($title,0, 30);
            $title = htmlspecialchars($title);
            $title_hover = '';

            $end = '';
            if (strlen(strip_tags($post->post_title)) > 30 ) {
                $end = '...';
            }
            if  ( $end != '' ) {
                $title .= $end;
                $val = str_replace('\"',"`",strip_tags($post->post_title));
                $title_hover = str_replace("'","`",$val);                    
            }
            // Mostrar la imagen destacada si está presente
            if (has_post_thumbnail($post->ID)) {
                $url = get_the_post_thumbnail_url($post->ID, 'thumbnail');
                $img = "<img style='object-fit:cover; width:100%;max-height:150px;' src='". $url . "' alt='default logo prisma' />";
            } 

            if (empty($url)) {
                $dir  = dirname(__FILE__, 1);
                $rand = sprintf("%02d",rand(1, 13));
                $img_default = 'default_'.$rand.'.jpeg';
                
                $img   = plugins_url('img/'.$img_default, $dir);
                $img = "<img class='' src='". $img . "' alt='default logo prisma' />";
            }

            ?>
            <div>

                <div class="search-result-thumbnail">
                    <?php echo $img ?>
                    <div class="hovertext"  >
                        <!-- Mostrar cada resultado según la estructura de tu tema -->
                        <a  title="<?php echo $title_hover ?>" 
                            href="<?php echo $link ?>" 
                            id="prisma-search-result-title">
                            <?php echo $title ?>
                        </a>
                    </div>
                    <small class="search-result-posttype"><?php echo esc_html($post->post_type); ?></small>
                </div>
            </div>  
        </div>
    <?php 
    }
} else {
    ?>
    <div class="mt-5">
        <h3> NO S'HAN TROBAT RECURSOS AMB ELS FILTRES ACTUALS </h3>    
    </div>

    <?php
}

?>







