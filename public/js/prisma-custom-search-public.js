(function( $ ) {
	'use strict';

    var paramsShared = [];
    var htmlShared = '';

    function clearGroupFields()
    {
        $('.prismaGroupfield').addClass('hidden');
    }

    $(document).on("change","#select_post_type", function(event){    
        
        let value = $(this).val();

        event.preventDefault();
        
        var selectedOption = $("option:selected", this);
        var grupo = selectedOption.attr("data-value");
        $('#cercaRecurses').attr('data-post_type', value);
        $('#cercaRecurses').attr('data-groups', grupo);
       


        clearGroupFields();

        //console.log(value);

        if (grupo.length > 0) {

            //let id_group = '#div_' + value;
            //$(id_group).removeClass('hidden');
            $('#PrismaGroupFields .'+value).removeClass('hidden');
            //console.log("ENTRO!!");
        }
    });



    
    $(document).on("click","#shareSearchPrisma", function(event){    
        
        let popUpResponse = '#popUpModalInfoBox';

        event.preventDefault();      

        paramsShared['action'] = 'getDataSharedSearchPrisma';
        
        let paramsSharedEncrypted = btoa( JSON.stringify(paramsShared) );        

        let css_url = $(this).attr('data-css_url');
        let js_url = $(this).attr('data-js_url');
        let remote_url = $(this).attr('data-remote_url');
       
        let script = "<script src=\""+js_url+"\" ></script>\n";
        let css = "<link rel=\"stylesheet\" href=\""+css_url+"\" />\n";
        let css2 = "<link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css\" rel=\"stylesheet\" integrity=\"sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC\" crossorigin=\"anonymous\">\n";
        let nav = "<div id=\"kiwopPrisma_paginator\" class=\"navigation\"></div>\n";
        let result = "<div id=\"kiwopPrisma_recurses\" class=\"row search-results-grid\"></div>\n";
        let div = "<div data-showsearch='true' data-remote_url='"+remote_url+"' id=\"prisma_share_container\" data-filters-base64='"+paramsSharedEncrypted+"' >"+nav+result+"</div>";

        htmlShared = script + css + css2 + div;
        $('#sharedPrismaSearhCode').val( htmlShared  );

        $(popUpResponse + ' .modal-body').html();
        $(popUpResponse).modal('show');   
        $('#popUpModalInfoBoxLabel').html('Incrusta aquest codi en una altra web per tenir el llistat de resultats al teu lloc web');
        

    });

    $(document).on("click","#prismaCopyToClipboard", function(event){  
        var textarea = document.getElementById("sharedPrismaSearhCode");
    
        // Selecciona el contenido del textarea
        textarea.select();
    
        try {
            // Intenta copiar el texto al portapapeles
            navigator.clipboard.writeText(textarea.value)
                .then(function() {
                    $('#messageCopiedAlert').html('Copiat al portapapeles');
                })
                .catch(function(err) {
                    $('#messageCopiedAlert').html('no s`ha pogut copiar al porta-retalls');
                });
        } catch (err) {            
            try {
                textarea.select();
                document.execCommand("copy");
                $('#messageCopiedAlert').html('contingut copiat!');
                setInterval(function(){
                    $('#messageCopiedAlert').html('');
                }, 3000);
            } catch (err2) {                        
                $('#messageCopiedAlert').html('no s`ha pogut copiar al porta-retalls');
            }
        }
    });
    
    $(document).on("click","#afegixCercador", function(event){
        let is_checked = $(this).is(":checked");

        let str = htmlShared; // global var
              
        let replaced_text_true = str.replace("data-showsearch='false'", "data-showsearch='true'");
        let replaced_text_false = str.replace("data-showsearch='true'", "data-showsearch='false'");
               
        if (is_checked) {
            $('#sharedPrismaSearhCode').val( replaced_text_true );
            htmlShared = replaced_text_true;
        } else {
            $('#sharedPrismaSearhCode').val( replaced_text_false );
            htmlShared = replaced_text_false;
        }
    });

    $(document).on("click",".closeThePopup", function(event){      
        $('#popUpModalInfoBox').modal('hide');
    });
    
    $(document).on("click","#cercaRecurses", function(event){    
        
        event.preventDefault();      
        
        let name_container = '#kiwopPrisma_recurses';
        let container = $(name_container);
        let paginador = $("#kiwopPrisma_paginator");
        let button = $(this);
        button.find('.loader').remove();
        
        let img = ' <img class="loader" src="'+prisma_custom_search_globals.loader+'" />';
        button.append(img);
        
        let dataPost = $('#prismaFiltreRecurses').serialize();
        let post_type = $(this).attr('data-post_type');
        let acfGroups = $(this).attr('data-groups');

        let data = {
            action: 'getRecurses',
            data:dataPost,
            acfGroups:acfGroups,
            post_type:post_type,
            _ajax_nonce: prisma_custom_search_globals.getRecurses
        };

        paramsShared = data;
        
        filtrarDades(container, name_container, paginador, button, data);
        
        
    });
    
    function filtrarDades(container, name_container, paginador, button, data)
    {
        
        $.ajax({
            url: prisma_custom_search_globals.ajax_url,
            data: data,
            type: 'POST',
            dataType : 'json',
            success: function(response) {
                button.find('.loader').remove();
                if (response.error) {
                    $(container).html(response.message);
                } else {
                    $(container).html(response.html);
                    $(paginador).html(response.paginator);                    
                    
                    $('#shareSearchPrisma').removeClass('hidden');
                }                
            },
            error: function(errorThrown){
                button.find('.loader').remove();
                ajaxMessage(name_container,errorThrown.statusText);
            }
        });
    }
    
    function ajaxMessage(container,msg) {
        
        Array.isArray(msg) ? msg = msg.join('<br />') : msg = msg;

        $('#'+container).html(msg);
    }
    
    // --------------------------------------------------------------------------------------------------------------------------
    // pestaña principal, navegador ajax listado ultimos descuentos aplicados
    // --------------------------------------------------------------------------------------------------------------------------

    $(document).on("change","#kiwop_prisma_pagina_actual ", function(event){    
        var button = $('#loader_select_page');
        let value = $(this).val();
        cambiaPaginaRecursos('prior', button, value);
    });

    $(document).on("click","#kiwopPrisma_paginator .prior_row", function(event){    
        var button = $(this);
        cambiaPaginaRecursos('prior', button);
    });

    $(document).on("click","#kiwopPrisma_paginator .next_row", function(event){    
        var button = $(this);
        cambiaPaginaRecursos('next', button);
    });

    function cambiaPaginaRecursos(direction, button, selected_page = null)
    {
        //event.preventDefault();


        let page = parseInt( button.attr('data-page') );
        
        if (selected_page == null) {

            if (direction == 'prior') {
                let priorIsDisabled = button.hasClass('a_navigator_disabled');
                if (priorIsDisabled) {
                    return;
                }  
                page = page - 1;           
            }
    
            if (direction == 'next') {
                let nextIsDisabled = button.hasClass('a_navigator_disabled');
                if (nextIsDisabled) {
                    return;
                }            
                page = page + 1;           
            }
        } else {
            page = selected_page; 
        }

        //event.preventDefault();      
        
        let name_container = '#kiwopPrisma_recurses';
        let container = $(name_container);
        let paginador = $("#kiwopPrisma_paginator");

        button.find('.loader').remove();
        
        let img = ' <img class="loader" src="'+prisma_custom_search_globals.loader+'" />';
        button.append(img);

        let dataPost = $('#prismaFiltreRecurses').serialize();

        //Cookies.set('filtresImportacions', JSON.stringify(dataPostJS), { expires: 365 } );


        let data = {
            paged:page,
            action: 'getRecurses',
            data:dataPost,
            _ajax_nonce: prisma_custom_search_globals.getRecurses
        };

        filtrarDades(container, name_container, paginador, button, data);
        
    }
    

})( jQuery );
