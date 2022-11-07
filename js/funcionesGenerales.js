
                function confirmDelete() {
                    return confirm('Esta seguro que desea eliminar los datos')
                }

                //cerrar alertas
                $(".alert").delay(20000).slideToggle(100, function() {
                    $(this).alert('close');
                });
                $('.close').click(function() {
                    $('.alert').hide();
                  })
               //envioMail
               
                function envioMail(){
                    var motivo= document.getElementById("selectMotivo");
                    var mensaje= document.getElementById("inputMensaje");
                    var avisoRojo =document.getElementById("avisoMensaje");
                
                    if (mensaje.value.length == 0 ){
                    avisoRojo.innerHTML="No se permiten descripciones vacías.";
                    }else{
                    avisoRojo.style.color="#0F9FA8";
                    avisoRojo.innerHTML="Enviando..";
                    location.href ="https://intranet.unraf.edu.ar/PasantiasyBecasFuturxsProfesionalesTest/superAdmin/email/" + motivo.options[motivo.selectedIndex].text + "/" + mensaje.value;
                    }
                    
                }
                
                
                