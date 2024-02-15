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


$plugin_url = plugins_url('/', __FILE__);
$plugin_url = trim($plugin_url, 'partials/');
$css_url = $plugin_url . '/css/prisma-custom-search-public.css?v=' . date('Ymdhis');;
$js_url = $plugin_url . '/js/share.js?v=' . date('Ymdhis');

$remote_url = get_site_url() . "/wp-admin/admin-ajax.php";


?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->


<div class="row prisma-custom-search-container">
    <div class="col-xs-12 col-md-3 col-lg-3 prisma-background-form-search ">
        <form id="prismaFiltreRecurses" action="" method="post">
            <div class="divField">
                <input class="fieldSearch" type="text" name="prisma-custom-search" id="prisma-custom-search" placeholder="Títol, descripció..." />
                <hr />
            </div>
            <div class="divField">
                <label> Ordenar per </label>
                <select class="fieldSerch" name="prismaOrderBy" style="width:100%" >
                    <option selected value="p.post_date">Data (més recents)</option>
                    <option value="p.post_title">Ordenar per títol</option>
                </select>
            </div>
            <div class="divField">            
                <button id="cercaRecurses" type="button" style="width:100%;margin-top:10px;" >                        
                    Cerca
                </button> 
            </div>
            <div class="divField">
                Altres Filtres <hr />
            </div>
            <div class="divField">
                Seleccioneu tipus de recurs<br />
                <?php echo $postTypesHTML ?>
            </div>
            <div id="PrismaGroupFields" class="divField" >
                <?php echo $searchFormFields ?>
            </div>

            <div class="divField">            
                <hr />
                <button id="shareSearchPrisma" 
                        data-css_url="<?php echo $css_url ?>"    
                        data-js_url="<?php echo $js_url ?>"    
                        data-remote_url="<?php echo $remote_url ?>"    
                        type="button" 
                        class="hidden" 
                        style="width:100%;margin-top:10px;" >                        
                    Incrustar en un altre lloc
                </button>
            </div>              
        </form>
    </div>
    
    <div class="col-xs-12 col-md-9 col-lg-9"  >
        <div id="kiwopPrisma_paginator" class="navigation">
            <?php 
                echo $results['paginator']; 
            ?>
        </div>

        <div id="kiwopPrisma_recurses" class="row search-results-grid">
            <?php echo $results['html']; ?>
        </div>
    </div>
</div>


<div class="modal modal-xl fade " id="popUpModalInfoBox" tabindex="-1" role="dialog" aria-labelledby="popUpModalInfoBoxLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="popUpModalInfoBoxLabel"></h5>
                <button type="button" class="closeThePopup" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" rows="6" id="sharedPrismaSearhCode" ></textarea>
                <hr />
                <button id="prismaCopyToClipboard" >Copiar Codi</button> 
                <label for="afegixCercador" >
                    <input type="checkbox" checked="true" id="afegixCercador" value="1" />
                    Afegir cercador
                </label>
                <span id="messageCopiedAlert"></span>
            </div>
        </div>
    </div>
</div>
        
   