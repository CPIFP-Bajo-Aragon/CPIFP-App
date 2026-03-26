
<?php require_once RUTA_APP.'/vistas/inc/header.php'; ?>


<div class="container">

    <div class="row d-flex justify-content-center text-center mx-0 mt-2">
        <div class="col-12">
            <h1>RELACIÓN DE PROVEEDORES</h1>
        </div>
    </div>
    

    <div class="row">
            <div class="col-3">   
                <?php include RUTA_APP.'/vistas/inc/subMenuProveedores.php'; ?>
            </div>


            <div class="col-9"> 
                                <!-- Formulario filtro --> 
                                <div class="row">
                                <FORM action="<?php echo RUTA_URL."/Proveedores/listaProveedores";?>" method="POST">
                                        <div class="col-9">
                                        CIF:<INPUT name ="CIF" type="text">     
                                        Nombre:<INPUT name ="Nombre" type="text"> 
                                        <INPUT name ="Buscar" type="submit" Value="Buscar">                                                    
                                        </div>                                        
                                </FORM>
                                </div>


                         <!-- Dibujo de la paginación --> 
                        <div class="row">
                                <div class="col-12">
                                      <?php 
                                      $totalPaginas=$datos['totalPaginas'];
                                      $paginaActual=$datos['paginaAcual'];
                                      dibujarBotonesPaginacion($totalPaginas,$paginaActual);
                                      ?>
                                </div>
                        </div>
                     
            
                <div class="row">
                        <div class="col-2">
                                CIF
                        </div>
                        <div class="col-4">
                                Proveedor
                        </div>   
                        <div class="col-2">
                                Media Año
                        </div>   
                        <div class="col-2">
                                Total Media
                        </div>     
                        <div class="col-2">
                                   
                        </div>    
                              
                </div>

                <?php 
                $cont=1;
                foreach($datos["proveedores"] as $proveedor): ?>
                    
                    <?php 
                    $media="---";
                    if($proveedor->MItem1!="" && $proveedor->MItem2!="" && $proveedor->MItem3!="" && $proveedor->MItem4!="")
                                            $media=($proveedor->MItem1+$proveedor->MItem2+$proveedor->MItem3+$proveedor->MItem4)/4;
                    
                    $mediaUltimoAnio="---";
                    if($proveedor->MItem1_UltimoAnio!="" && $proveedor->MItem2_UltimoAnio!="" && $proveedor->MItem3_UltimoAnio!="" && $proveedor->MItem4_UltimoAnio!="")
                            $mediaUltimoAnio=($proveedor->MItem1_UltimoAnio+$proveedor->MItem2_UltimoAnio+$proveedor->MItem3_UltimoAnio+$proveedor->MItem4_UltimoAnio)/4;
                    
                    ?>

                <?php 
                if($cont%2==0) echo '<div class="row bg-light text-dark" >';
                else echo '<div class="row bg-secondary text-white" >';
                
                $cont++;
                ?>
                        <div class="col-2 text-truncate">
                                <?php echo $proveedor->CIF;?>
                        </div>
                        <div class="col-4 text-truncate">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalDatosProveedor" onclick="mostrarDatos(2)"><?php echo $proveedor->Nombre;?></a> 
                        </div>   
                        <div class="col-2">
                                <?php echo $mediaUltimoAnio;?>
                        </div>   
                        <div class="col-2">
                                    <?php echo $media;?>
                        </div>             
                        <div class="col-2">
                        
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalEditarProveedor">
                            <svg  class="" width="16" height="16"   >
                                <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z"/>
                            </svg>
                        </a>
                        

                        
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalBorrarProveedor">
                        <svg width="16" height="16" class="bi bi-trash" viewBox="0 0 16 16">
                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                        </svg>
                        </a>

                        </div> 
                </div>
                    
                <?php endforeach ?>

                <div class="row">
                                <div class="col-12">

                                      <?php 
                                      dibujarBotonesPaginacion($totalPaginas,$paginaActual);
                                      ?>
                                </div>
                </div>
            </div>
    </div>     


    
  

</div>

<script>
    function mostrarDatos(id_asesoria) {
        alert("Mostrar Datos");
    }

    async function del_asesoria(){
        
        const datosForm = new FormData(document.getElementById("formDelAsesoria"))
        await fetch(`<?php echo RUTA_URL?>/asesorias/del_asesoria`, {
            method: "POST",
            body: datosForm,
        })
            .then((resp) => resp.json())
            .then(function(data) {

                // console.log(data)

                if(data){
                    document.getElementById('asesoria_'+datosForm.get('id_asesoria')).remove()
                    document.getElementById('acciones_'+datosForm.get('id_asesoria')).remove()
                    // Mostamos mensaje de exito
                    const toast = document.getElementById("toastOK")
                    const bootToast = new bootstrap.Toast(toast)
                    bootToast.show()
                } else {
                    // Mostramos mensaje de error
                    const toast = document.getElementById("toastKO")
                    const bootToast = new bootstrap.Toast(toast)
                    bootToast.show()
                }

            })
            .catch((error) => {
                // console.log(error)
                const toast = document.getElementById("toastKO")
                const bootToast = new bootstrap.Toast(toast)
                bootToast.show()
            })
    }
</script>



<?php require_once RUTA_APP.'/vistas/inc/footer.php' ?>


<!-- ++++++++++++++++++++++++++++ Modal Datos Proveedor +++++++++++++++++++++++++++++++++ -->


<!-- Modal -->
<div class="modal fade" id="modalDatosProveedor" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDatosProveedorLabel">Datos del Proveedor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>
                <?php echo $totalPaginas;?>
                
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>



<!-- ++++++++++++++++++++++++++++ Modal Editar Proveedor +++++++++++++++++++++++++++++++++ -->

<div class="modal fade" id="modalEditarProveedor" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarAccionLabel">
                    Editar Proveedor
                </h5>
            </div>
            <div class="modal-footer">
                <form method="post" id="formEditarAccion" 
                    action="javascript:guardarEditAccion()"
                >
                    <div class="row">
                        <div class="mb-3 col-12">
                            <textarea cols="70" class="form-control form-control-sm" id="accion_edit" 
                            name="accion" placeholder="Editar Acción"></textarea>
                        </div>
                    </div>
                    
                    <input type="hidden" id="id_reg_acciones" name="id_reg_acciones">

                    <button type="button" class="btn btn-secondary" 
                        data-bs-dismiss="modal">Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning" id="buttonEditar" data-bs-dismiss="modal">
                        Guardar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ++++++++++++++++++++++++++++ Modal Borrar Proveedor +++++++++++++++++++++++++++++++++ -->


<!-- Modal -->
<div class="modal fade" id="modalBorrarProveedor" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDatosProveedorLabel">Borrar Proveedor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>
                <?php echo $totalPaginas;?>
                
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>