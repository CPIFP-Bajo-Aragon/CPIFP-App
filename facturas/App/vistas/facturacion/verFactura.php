
<?php require_once RUTA_APP.'/vistas/inc/header.php'; ?>


<div class="container">

  


    <div class="row d-flex justify-content-center text-center mx-0 mt-3">
        <div class="col-3">   
        <?php include RUTA_APP.'/vistas/inc/subMenuFacturas.php'; ?>
        
        </div>
        <div class="col-9">
                <?php $errores=0;?>

                <?php
                
                echo "<br>";
                $formato="text-success";
                
                
                ?>
                <div class="row">
                </div>
                <div class="col-12">
                    <h4 class="<?php echo $formato;?>">PROVEEDOR: <?php echo $datos['factura']->Nombre;?></h4> 
                </div>    
                
               
                <div class="row">
                    <div class="col-6">
                        <h5 class="<?php echo $formato;?>">DESTINO: <?php echo $datos['factura']->Depart_Servicio;?></h5>
                    </div>
                    <div class="col-6">
                        
                    <h5 class="<?php echo $formato;?>">RESPONSABLE: <?php echo $datos['factura']->Responsable;?></h5>
                    </div>
                    
                </div>

               
                
                <div class="row">
                    <div class="col-6">
                        <h5 class="<?php echo $formato;?>">Num Factura: <?php echo $datos['factura']->NFactura;?></h5>
                    </div>
                    
                </div>

                
                <div class="row">
                    <div class="col-6">
                     

                    <h5 class="<?php echo $formato;?>">Fecha de Factura: <?php echo $datos['factura']->Ffactura;?> </h5>
                    </div>
                    <div class="col-6">
                        <h5 class="<?php echo $formato;?>">Fecha conformidad: <?php echo $datos['factura']->Faprobacion;?></h5>
                    </div>
                    
                </div>

                

                <div class="row">
                <div class="col-6">
                        <h5 class="<?php echo $formato;?>">Importe: <?php echo $datos['factura']->Importe;?></h5>
                </div>
                    <div class="col-6">
                    <h5 class="<?php echo $formato;?>">Inventariable: <?php echo $datos['factura']->Inventariable;?></h5>
                    </div>
                    
                </div>
                

                <div class="row">
                    <div class="mb-5 col-12">
                    Observaciones: <?php echo $datos['factura']->observaciones;?>
                    </div>
                </div>

                <div class="row bg-light ">
              
                    <div class="col-12">
                    <h5>Item1 -> <span class="<?php echo $formato;?>"><?php echo $datos['factura']->Item1;?></span> (Información facilitada, trato recibido y servicio post_venta)</h5>
                    <h5>Item2 -> <span class="<?php echo $formato;?>"><?php echo $datos['factura']->Item2;?></span> (Cumplimiento con los plazos de entrega de productos o ejecución de servicios)</h5>
                    <h5>Item3 -> <span class="<?php echo $formato;?>"><?php echo $datos['factura']->Item3;?></span> (Sistema de transporte, embalaje y/ entrega del producto o servicio)</h5>
                    <h5>Item4 -> <span class="<?php echo $formato;?>"><?php echo $datos['factura']->Item4;?></span> (Relacion Precio - Calidad)</h5>
                    <?php $media=($datos['factura']->Item1+$datos['factura']->Item2+$datos['factura']->Item3+$datos['factura']->Item4)/4;?>
                    <h5>Media del proveedor: <?php echo $media;?> 
                    </div>
                </div>
                 
                
            <div class="row">

                           
                    <div class="col-12">
                          
                            <form action="<?php echo RUTA_URL."/GestionFacturas/imprimirFactura";?>" method="POST" target="_blank">
                                <input type="hidden" name="N_Asiento" value="<?php echo $datos['factura']->N_Asiento;?>">
                                <input type="hidden" name="NomProveedor" value="<?php echo $datos['factura']->Nombre;?>">
                                <input type="hidden" name="NomDestnino" value="<?php echo $datos['factura']->Depart_Servicio;?>">
                                <input type="hidden" name="responsable" value="<?php echo $datos['factura']->Responsable;?>">
                                <input type="hidden" name="CIF" value="<?php echo $datos['factura']->CIF;?>">
                                <input type="hidden" name="NFactura" value="<?php echo $datos['factura']->NFactura;?>">
                                <input type="hidden" name="inventariable" value="<?php echo $datos['factura']->Inventariable;?>">
                                <input type="hidden" name="Fconformidad" value="<?php echo $datos['factura']->Faprobacion;?>">
                                <input type="hidden" name="Ffactura" value="<?php echo $datos['factura']->Ffactura;?>">
                                <input type="hidden" name="descripcion_as" value="<?php echo $datos['factura']->observaciones;?>">
                                <input type="hidden" name="importe" value="<?php echo $datos['factura']->Importe;?>">
                                <input type="hidden" name="Item1" value="<?php echo $datos['factura']->Item1;?>">
                                <input type="hidden" name="Item2" value="<?php echo $datos['factura']->Item2;?>">
                                <input type="hidden" name="Item3" value="<?php echo $datos['factura']->Item3;?>">
                                <input type="hidden" name="Item4" value="<?php echo $datos['factura']->Item4;?>">
                                <input type="hidden" name="cancelar" value="cancelar">
                                <button type="submit" name ="imprimirFactura"   class="w-100 btn btn-success btn-lg" >IMPRIMIR FACTURA</button>
                            </form>
                    </div>

           
            </div>
    
    </div>
            

        </div>
    </div>

</div>



<script>
    
        /// FUNCIÓN EN JAVASCTRIPT QUE INDICA QUE SE HACE AL CANCELAR. 
        function cancelarDatos()
        {
            //CAMBIAMOS EL ACTION DEL FORMULAIRIO
            document.getElementById('guardarFactura').setAttribute('action', '')

            //hacemos el submit
            document.getElementById('guardarFactura').submit()
            
        }


</script>

<!-- ++++++++++++++++++++++++++++++++++++++++ Modal Cerar Accion ++++++++++++++++++ -->

<div class="modal fade" id="modalDelAsesoria" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDelAsesoriaLabel">
                    ¿Estás seguro que quieres eliminar la Asesoría?
                </h5>
            </div>
            <div class="modal-body">
                <p>Se borraran todas las acciones realizadas en la Asesoría.</p>
            </div>
            <div class="modal-footer">
                <form method="post" id="formDelAsesoria" action="javascript:del_asesoria()">
                    <button type="button" class="btn btn-secondary" 
                        data-bs-dismiss="modal">Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning" data-bs-dismiss="modal">
                        Borrar Asesoría
                    </button>
                    <input type="hidden" id="id_asesoria" name="id_asesoria">
                </form>
            </div>
        </div>
    </div>
</div>


<!-- ++++++++++++++++++++++++++++++++++++++++ Toast de Validacion Asincrona ++++++++++++++++++ -->

<div class="toast-container position-fixed bottom-0 end-0 p-3 m-4" style="z-index: 11">
    <div id="toastOK" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <svg class="bd-placeholder-img rounded me-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" preserveAspectRatio="xMidYMid slice" focusable="false">
                <rect width="100%" height="100%" fill="green">
                </rect>
            </svg>
            <strong class="me-auto">Acción OK</strong>
        </div>
    </div>
</div>

<div class="position-fixed bottom-0 end-0 p-3 m-4" style="z-index: 11">
    <div id="toastKO" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <svg class="bd-placeholder-img rounded me-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" preserveAspectRatio="xMidYMid slice" focusable="false">
                <rect width="100%" height="100%" fill="red">
                </rect>
            </svg>
            <strong class="me-auto">Error !!!</strong>
        </div>
    </div>
</div>


<script>
    function del_asesoria_modal(id_asesoria) {
        document.getElementById("id_asesoria").value = id_asesoria
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

