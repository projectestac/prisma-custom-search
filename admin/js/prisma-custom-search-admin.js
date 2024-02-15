(function( $ ) {
    'use strict';
    

    $(document).on("click",".ktab", function(event){      
        let tabId = $(this).attr('data-tab'); 
        
        // Ocultar todas las pestañas y mostrar la pestaña seleccionada
        var tabs = document.querySelectorAll('.tab-content');
        tabs.forEach(function(tab) {
            tab.classList.remove('active-content');
        });

        //alert("kaka");

        document.getElementById(tabId).classList.add('active-content');

        // Cambiar la clase activa en las pestañas
        var tabButtons = document.querySelectorAll('.ktab');
        tabButtons.forEach(function(button) {
            button.classList.remove('active-tab');
        });

        $(this).addClass('active-tab');
        
    });
    
    $(document).on("click",".subtab", function(event){      
        let tabId = $(this).attr('data-tab'); 
        
        // Ocultar todas las pestañas y mostrar la pestaña seleccionada
        var tabs = document.querySelectorAll('.subtab-content');
        tabs.forEach(function(tab) {
            tab.classList.remove('active-content');
        });
        
        //alert("kaka");
        
        document.getElementById(tabId).classList.add('active-content');
        
        // Cambiar la clase activa en las pestañas
        var tabButtons = document.querySelectorAll('.subtab');
        tabButtons.forEach(function(button) {
            button.classList.remove('active-subtab');
        });
        
        $(this).addClass('active-subtab');
        
    });


    $(document).on("click",".seleccionaTodo", function(event){      
        event.preventDefault();
        let target_class = $(this).attr('data-destino');
        var checkboxes = document.querySelectorAll('.' + target_class);
    
        // Iterar sobre los checkboxes y establecer su propiedad "checked" a true
        checkboxes.forEach(function(checkbox) {
          checkbox.checked = true;
        });
    });

    $(document).on("click",".deSeleccionaTodo", function(event){      
        event.preventDefault();
        let target_class = $(this).attr('data-destino');
        var checkboxes = document.querySelectorAll('.' + target_class);
    
        // Iterar sobre los checkboxes y establecer su propiedad "checked" a true
        checkboxes.forEach(function(checkbox) {
          checkbox.checked = false;
        });
    });

    function ajaxMessage(container,msg) {
        
        Array.isArray(msg) ? msg = msg.join('<br />') : msg = msg;

        $('#'+container).html(msg);
        /*
        setTimeout(function(){ 
            $('#'+container).html('');
        }, 5000);
        */

    }



    $(document).on("click","#saveSearchConf", function(event){  

        event.preventDefault();

        let img = ' <img class="loader" src="'+prisma_custom_search.loader+'" />';
        let popUpResponse = '#popUpModalInfoBox';
        let button = $(this);
        let dataPost = $('#selectedFieldsForm').serialize();        

        let data = {
            action: 'saveDataSearchFormConfig',
            data:dataPost,
            _ajax_nonce: prisma_custom_search.saveDataSearchFormConfig
        };

        button.find('.loader').remove();
        
        button.append(img);        

        
        $('#popUpModalInfoBoxLabel').html('Actualització de grup camps per post type');
        
        $.ajax({
            url: prisma_custom_search.ajax_url,
            data: data,
            type: 'POST',
            dataType : 'json',
            success: function(response) {
                button.find('.loader').remove();
                $(popUpResponse + ' .modal-body').html(response.message);
                $(popUpResponse).modal('show');               
            },
            error: function(errorThrown){
                button.find('.loader').remove();
                $(popUpResponse + ' .modal-body').html(errorThrown.statusText);
                $(popUpResponse).modal('show');
            }
        });

    });
    

    $(document).on("click","#saveGroupsPerPostType", function(event){  

        event.preventDefault();

        let img = ' <img class="loader" src="'+prisma_custom_search.loader+'" />';
        let popUpResponse = '#popUpModalInfoBox';
        let button = $(this);
        let dataPost = $('#groupFieldByPostType').serialize();        

        let data = {
            action: 'saveGroupFieldsByType',
            data:dataPost,
            _ajax_nonce: prisma_custom_search.saveGroupFieldsByType
        };

        button.find('.loader').remove();
        
        button.append(img);        

        
        $('#popUpModalInfoBoxLabel').html('Actualització de grup camps per post type');
        
        $.ajax({
            url: prisma_custom_search.ajax_url,
            data: data,
            type: 'POST',
            dataType : 'json',
            success: function(response) {
                button.find('.loader').remove();
                $(popUpResponse + ' .modal-body').html(response.message);
                $(popUpResponse).modal('show');               
            },
            error: function(errorThrown){
                button.find('.loader').remove();
                $(popUpResponse + ' .modal-body').html(errorThrown.statusText);
                $(popUpResponse).modal('show');
            }
        });

    });
    


    $(document).on("click","#saveCSS", function(event){  

        event.preventDefault();

        let img = ' <img class="loader" src="'+prisma_custom_search.loader+'" />';
        let popUpResponse = '#popUpModalInfoBox';
        let button = $(this);
        let cssFront = $('#cssFront').val();        

        let data = {
            action: 'saveCssFront',
            cssFront:cssFront,
            _ajax_nonce: prisma_custom_search.saveCssFront
        };

        button.find('.loader').remove();
        
        button.append(img);        

        
        $('#popUpModalInfoBoxLabel').html('Actualització de CSS');
        
        $.ajax({
            url: prisma_custom_search.ajax_url,
            data: data,
            type: 'POST',
            dataType : 'json',
            success: function(response) {
                button.find('.loader').remove();
                $(popUpResponse + ' .modal-body').html(response.message);
                $(popUpResponse).modal('show');               
            },
            error: function(errorThrown){
                button.find('.loader').remove();
                $(popUpResponse + ' .modal-body').html(errorThrown.statusText);
                $(popUpResponse).modal('show');
            }
        });

    });
    

    $(document).on("click","#restaurarCSSOriginal", function(event){  

        event.preventDefault();

        let img = ' <img class="loader" src="'+prisma_custom_search.loader+'" />';
        let popUpResponse = '#popUpModalInfoBox';
        let button = $(this);
        
        let data = {
            action: 'restaurarCSSOriginal',
            _ajax_nonce: prisma_custom_search.restaurarCSSOriginal
        };
        
        button.find('.loader').remove();
        
        button.append(img);        
        
        
        $('#popUpModalInfoBoxLabel').html('Restauració de CSS');
        
        $.ajax({
            url: prisma_custom_search.ajax_url,
            data: data,
            type: 'POST',
            dataType : 'json',
            success: function(response) {
                button.find('.loader').remove();
                $('#cssFront').val( response.css );
                $(popUpResponse + ' .modal-body').html(response.message);
                $(popUpResponse).modal('show');               
            },
            error: function(errorThrown){
                button.find('.loader').remove();
                $(popUpResponse + ' .modal-body').html(errorThrown.statusText);
                $(popUpResponse).modal('show');
            }
        });

    });
    



})( jQuery );
