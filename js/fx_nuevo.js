// MENU --------------------------------------------------------------------------------
	$(function() {

		$('.menu').click(function(){

			var titulo = $(this).attr('title')
			var datos = titulo.split('_',2)
			
			var carpeta = datos[0]
			var opcion = datos[1]

			$("#panel_inicio").html('<div class="text-center"><div class="loadingsm"></div></div>');
			$('#panel_inicio').load("abm/"+carpeta+"/"+opcion+".php");

		})
	})

// NUEVOS --------------------------------------------------------------------------------

	function nuevo(formulario){

		var pars = ''
		var campos = Array()
		var campospasan = Array()

		$("#formulario_nuevo").find(':input').each(function(){
              
            $(this).attr('id')
            var dato = $(this).attr('id').split('_',2) 

            if (dato[0] == 'dato') {
               campos.push("dato_"+dato[1])
              campospasan.push("dato_"+dato[1])
            };
              
          });
		
		 for (i = 0; i < campos.length; i++) {
			campo = document.getElementById(campos[i]);

			pars =pars + campospasan[i] + "=" + campo.value + "&";
		 }	
		 alert(pars);
				
				$("#div_mensaje_general").html('<div class="text-center"><div class="loadingsm"></div></div>');
				// $('#boton_guardar').attr('disabled', true);

				$.ajax({
						url : "abm/guardar/"+formulario+".php",
						data : pars,
						dataType : "json",
						type : "get",

						success: function(data){
								
							if (data.success == 'true') {

								$('#div_mensaje_general').html('<div id="mensaje_general" class="alert alert-info alert-dismissible" style="height:47px" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Ingreso exitoso!</div>');				
								setTimeout("$('mensaje_general').alert('close')", 1000);
								setTimeout("$('#panel_inicio').load('abm/nuevo/"+formulario+".php')", 1050);
							} else {
								$('#div_mensaje_general').html('<div id="mensaje_general" class="alert alert-danger alert-dismissible" style="height:47px" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Error reintente!</div>');				
								setTimeout("$('mensaje_general').alert('close')", 2000);
							}
						
						}

				});

	}
 	
// REPORTES --------------------------------------------------------------------------------

	function reporte(formulario){

		var pars = ''
		var campos = Array()
		var campospasan = Array()

		$("#formulario_reporte").find(':input').each(function(){
              
            var dato = $(this).attr('id').split('_',2) 
            
            if (dato[0] == 'dato') {
               campos.push("dato_"+dato[1])
              campospasan.push("dato_"+dato[1])
            };
              
          });
		
		 for (i = 0; i < campos.length; i++) {
			campo = document.getElementById(campos[i]);

			// alert(campo.value)

			pars =pars + campospasan[i] + "=" + campo.value + "&";
		 }	
		 	// alert(pars);
				
				$("#div_reporte").html('<div class="text-center"><div class="loadingsm"></div></div>');
				$("#div_reporte").load("abm/reporte/"+formulario+".php", pars);

	}


	// CONTROLES --------------------------------------------------------------------------------
	
	function calculo_in_fi(){

		var valor_i = $('#valor_inicial').val()
		var valor_f = $('#valor_final').val()
		var calculo = valor_f - valor_i

		if(calculo > 0){
	
			$('#dato_calculo').val(calculo)

		}else{

			$('#dato_calculo').val('')
			// $('#riego_inicial').val('')
			$('#valor_final').val('')

		}

	 }

// MODIFICA --------------------------------------------------------------------------------

	
	jQuery.throughObject = function(obj){
	    for(var attr in obj){
	      if (attr != 'success') {
	        $('#dato_'+ attr).val(obj[attr])
	      };
	      
	      // alert('dato_'+ attr + ' : ' + obj[attr]);
	      if(typeof obj[attr] === 'object'){
	        jQuery.throughObject(obj[attr]);
	      }
	    }
	  }

	function modifica(formulario,id){
 
	    pars ="formulario=" + formulario + "&" + "id=" + id + "&";

	    // alert(pars)

	    $.ajax({
	            url : "abm/modifica/consultas.php",
	            data : pars,
	            dataType : "json",
	            type : "get",

	            success: function(data, textStatus, jqXHR){
	                
	              if (data.success == 'true') {

	                jQuery.throughObject(data);  

	                // // botones
	                $('#boton_limpiar').prop('disabled', true)
	                $('#boton_guardar').text('Modificar')
	                
	              } else {
	                $('#div_mensaje_general').html('<div id="mensaje_general" class="alert alert-danger alert-dismissible" style="height:47px" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Error reintente!</div>');       
	                setTimeout("$('mensaje_general').alert('close')", 2000);
	              }
	            
	            }

	        });

	  }
	

// otros --------------------------------------------------------------------------------

	function cbx(id){

	$('#dato_'+id).val($('#dato_'+id).prop('checked'))

	}