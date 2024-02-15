function realizarSolicitudAjax(url, metodo, datos, exitoCallback, errorCallback) {
    var xhr = new XMLHttpRequest();
    
    let data = objectToQueryString(datos);
    xhr.open(metodo, url + '&' + data, true);
    //xhr.setRequestHeader('Content-Type', 'application/json');

    
    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
           
            if (exitoCallback) {
                exitoCallback(JSON.parse(xhr.responseText));
            }
        } else {
            
            if (errorCallback) {
                errorCallback(xhr.statusText);
            }
        }
    };

   
    xhr.onerror = function () {
        if (errorCallback) {
            errorCallback('Error de red');
        }
    };

    xhr.send();
}



window.onload = function() {

    var metodo = 'GET'; 


    const el = document.querySelector("#prisma_share_container");
    //console.log("Filters:",el.dataset.filters);
    
    var datos_js = JSON.parse(atob( el.dataset.filtersBase64 ));
    var remoteURL = el.dataset.remote_url;
    var showsearch = el.dataset.showsearch;

    delete datos_js['action'];

    cargaContenidoInicial(remoteURL+'?action=getDataSharedSearchPrisma');
    
    function cargaContenidoInicial(urlAction) {
        realizarSolicitudAjax(urlAction, metodo, datos_js,
            function (respuesta) {
                document.getElementById('kiwopPrisma_paginator').innerHTML = respuesta.paginator;
                document.getElementById('kiwopPrisma_recurses').innerHTML = respuesta.html;
    
                regenerarEventosPaginacion();
               
            },
            function (error) {
                console.log('Error:', error);
                document.getElementById('kiwopPrisma_recurses').innerHTML = error;
            }
        );        
    }

    

    function cambiaPagina(pagina,loader)
    {        
        datos_js.paged = pagina;
        //console.log(datos_js);

        //let data = objectToQueryString(datos_js);

        realizarSolicitudAjax(remoteURL+'?action=getDataSharedSearchPrisma', metodo, datos_js,
            function (respuesta) {
                loader.classList.add("hidden");

                document.getElementById('kiwopPrisma_paginator').innerHTML = respuesta.paginator;
                document.getElementById('kiwopPrisma_recurses').innerHTML = respuesta.html;
    
                regenerarEventosPaginacion();
                            
                
            },
            function (error) {
                loader.classList.add("hidden");
                //console.log('Error:', error);
                document.getElementById('kiwopPrisma_recurses').innerHTML = error;
            }
        );

    }
    

    // Función que se ejecutará con throttling al teclear
    function handleThrottledKeyPress(event) {
        
        let searchValue = event.target.value;

        if (event.key === 'Enter' && searchValue.length > 0) {

            // Aquí puedes realizar las acciones que desees al pulsar una tecla
            let url_get = remoteURL + '?action=getRecursesForRemoteSites'+ '&prisma-custom-search=' + encodeURIComponent(searchValue);
            
            let loader = document.getElementById('loader_text');
            if (loader) {	
                loader.classList.remove("hidden");
            } 

            realizarSolicitudAjax(url_get, 'GET', null,
            function (respuesta) {
                    loader.classList.add("hidden");
    
                    document.getElementById('kiwopPrisma_paginator').innerHTML = respuesta.paginator;
                    document.getElementById('kiwopPrisma_recurses').innerHTML = respuesta.html;
    
                    regenerarEventosPaginacion();
    
                    if (showsearch==='true') {
                        setTimeout(() => {
                            const searchBox = document.getElementById('prisma-custom-search-ajax-box');
                            searchBox.classList.remove("hidden");
                        }, 500);
                    }        
                    
                },
                function (error) {
                    loader.classList.add("hidden");
                    //console.log('Error:', error);
                    document.getElementById('kiwopPrisma_recurses').innerHTML = error;
                }
            );
        } else if (event.key === 'Enter') {
            cargaContenidoInicial(remoteURL+'?action=getDataSharedSearchPrisma');
        }
    }
    
    function regenerarEventosPaginacion()
    {
        if (showsearch==='true') {
            const searchBox = document.getElementById('prisma-custom-search-ajax-box');
            if (searchBox != null) {             
                searchBox.classList.remove("hidden");
            }
        }   
    
        // Obtener el elemento de entrada de texto
        const textInput = document.getElementById('prisma-custom-search-ajax');
    
        // Añadir el evento con throttling
        textInput.addEventListener('keydown', handleThrottledKeyPress); // 500 milisegundos de throttling
        

        document.getElementById('kiwop_prisma_pagina_actual').addEventListener('change', function() {
            let loader = document.getElementById('loader_right');
            if (loader === undefined || loader === null) { 
                loader = document.getElementById('loader_left');
            }
            loader.classList.remove("hidden");
            cambiaPagina(this.value,loader);
        });
        
        if (document.getElementById('kiwopPrisma_next')) {
            let next = document.getElementById('kiwopPrisma_next');
            next.addEventListener('click', function() {
                let page =parseInt(document.getElementById('kiwop_prisma_pagina_actual').value);                         
                let loader = document.getElementById('loader_right');
                loader.classList.remove("hidden");
                cambiaPagina(page+1,loader);
            });  
        }
        
        if (document.getElementById('kiwopPrisma_prev')) {
            let prev = document.getElementById('kiwopPrisma_prev');
            prev.addEventListener('click', function() {
                let page = parseInt(document.getElementById('kiwop_prisma_pagina_actual').value);
                let loader = document.getElementById('loader_left');
                loader.classList.remove("hidden");
                cambiaPagina(page-1,loader);
            });
        }
    }


};



function objectToQueryString(obj) {
    var queryString = '';

    if (typeof obj === 'object' && !Array.isArray(obj)) {

        for (var key in obj) {
            if (obj.hasOwnProperty(key)) {
                if (queryString.length > 0) {
                    queryString += '&';
                }
                queryString += encodeURIComponent(key) + '=' + encodeURIComponent(obj[key]);
            }
        }
        return queryString;
    }

    return obj;

}