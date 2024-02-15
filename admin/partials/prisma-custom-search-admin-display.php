<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://kiwop.com
 * @since      1.0.0
 *
 * @package    Prisma_Custom_Search
 * @subpackage Prisma_Custom_Search/admin/partials
 */
?>


<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<ul class="tabs" >
    <li class="ktab active-tab" data-tab='tab1'><i class="fa-solid fa-robot"></i> Camps de cerca </li>
    <li class="ktab" data-tab='tab2'><i class="fa-solid fa-screwdriver-wrench"></i> Grups de camps per post_type </li>
    <li class="ktab" data-tab='tab3'><i class="fa-solid fa-file-code"></i> CSS </li>
</ul>


<div id="tab1" class="tab-content active-content" >
    
    <div class="p-4 col-xs-12" >       
        <h4>Configuració del formulari de cerca</h4>
        <p>Selecciona els camps que vols que aparegui al formulari de cerca</p>
        <hr />
        <form id="selectedFieldsForm" >
            <?php echo $searchFormFields ?>
        </form>
        <button type="button" class="btn btn-primary" id="saveSearchConf" >
            Guardar
        </button>
    </div>
</div>    

<div id="tab2" class="tab-content" >

    <div class="p-4 col-xs-12">
        <h4>Assignació de grups de camps per post_type</h4>
        <p>Aquest valor es pot obtenir de la pàgina del connector ACF, no hi ha funcions públiques per extreure aquest valor</p>
        <small class="pink-100" >Això és necessari per al cercador, ja que depenent del tipus de contingut que voleu cercar l'usuari se us mostraran els diferents camps de cerca</small>
        <hr />
        <form id="groupFieldByPostType" >
            <table class="table" >
                <thead>
                    <tr>
                        <th>Post Type</th>
                        <th>ID Grup de camps</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        foreach ($groupFieldsByType as $post_type => $acf_group_id) {
                            echo '<tr>';
                            echo '  <td style="width:200px">'.$post_type.'</td>';
                            echo '  <td><input class="form-control" type="text" name="'.$post_type.'" value="'.$acf_group_id.'" ></td>';
                            echo '</tr>';
                        }
                    ?>
                </tbody>
            </table>
        </form>
        <button type="button" class="btn btn-primary" id="saveGroupsPerPostType" >
            Guardar
        </button>
    </div>
</div>

<div id="tab3" class="tab-content" style="min-height:500px;">
    <div class="p-4 col-xs-12">
        <h4>CSS frontend</h4>
        <button type="button" class="mb-3 btn btn-primary" id="saveCSS" >
            Guardar
        </button>&nbsp;&nbsp;&nbsp;&nbsp;
        <button title="Atenció, perdreu qualsevol canvi fet anteriorment del CSS i restaurareu la versió inicial" 
                type="button" 
                class="mb-3 btn btn-danger" 
                id="restaurarCSSOriginal" >
            Restaurar CSS Original
        </button><br />
        <textarea id="cssFront" rows="100" name="css" class="form-control"><?php echo $css ?></textarea>
    </div>
</div>     



<div class="modal modal-lg fade " id="popUpModalInfoBox" tabindex="-1" role="dialog" aria-labelledby="popUpModalInfoBoxLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="popUpModalInfoBoxLabel"></h5>
                <button type="button" class="closeThePopup" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
            </div>
        </div>
    </div>
</div>
        
   
