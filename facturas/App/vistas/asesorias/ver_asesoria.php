<?php require_once RUTA_APP.'/vistas/inc/header.php'?>

<?php 
    $estadoFormulario = "";
    if($datos['asesoria']->id_estado == 3){         // Si la asesoria esta cerrada, desactivo el formulario
        $estadoFormulario = "disabled";         
    }
                                                    // Si tiene el rol 100 y no ha creado la asesoria
    if (!tienePrivilegios($datos['usuarioSesion']->id_rol,[200,300]) && $datos['asesoria']->acciones[0]->id_profesor != $datos['usuarioSesion']->id_profesor){
        $estadoFormulario = 'disabled';
    }
    
?>

<div class="container">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo RUTA_URL ?>">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">ver Asesoría</li>
        </ol>
    </nav>

    <div class="row d-flex justify-content-center text-center mx-0 mt-2">
        <div class="col-12">
            <h1>Asesoría: <?php echo $datos["asesoria"]->nombre_as ?></h1>
        </div>
    </div>




    <div class="row">
        <div class="col-md-7">
            <?php if($datos['asesoria']->id_estado != 3): ?>
                <form method="post" action="<?php echo RUTA_URL ?>/asesorias/add_accion">
                    <input type="hidden" name="id_asesoria" value="<?php echo $datos['asesoria']->id_asesoria ?>">
                    <div class="row">
                        <div class="mb-3 col-10">
                            <textarea class="form-control form-control-sm" id="accion" 
                            name="accion" placeholder="Nueva Acción"></textarea>
                        </div>
                        <div class="mb-3 col-2">
                            <button type="submit" class="w-100 btn btn-success btn-lg">Add</button>
                        </div>
                    </div>
                </form>
            <?php endif ?>

            <div class="card">
                <div class="card-body">
                    <form method="post" class="mb-5">
                        <div class="row">
                            <div class="mb-3 col-4" >
                                <label for="nombre_as" class="form-label">Nombre*</label>
                                <input <?php echo $estadoFormulario ?> type="text" class="form-control form-control-sm" id="nombre_as" name="nombre_as" value="<?php echo $datos["asesoria"]->nombre_as?>" required>
                            </div>

                            <div class="mb-3 col-4">
                                <label for="dni_as" class="form-label">DNI</label>
                                <input <?php echo $estadoFormulario ?> type="text" class="form-control form-control-sm" id="dni_as" name="dni_as" value="<?php echo $datos["asesoria"]->dni_as?>">
                            </div>

                            <div class="mb-3 col-4">
                                <label for="titulo_as" class="form-label">Título</label>
                                <input <?php echo $estadoFormulario ?> type="text" class="form-control form-control-sm" id="titulo_as" name="titulo_as" value="<?php echo $datos["asesoria"]->titulo_as?>">
                            </div>

                            <div class="mb-3 col-4">
                                <label for="telefono_as" class="form-label">Teléfono</label>
                                <input <?php echo $estadoFormulario ?> type="text" class="form-control form-control-sm" id="telefono_as" name="telefono_as" value="<?php echo $datos["asesoria"]->telefono_as?>">
                            </div>

                            <div class="mb-3 col-4">
                                <label for="email_as" class="form-label">Email*</label>
                                <input <?php echo $estadoFormulario ?> type="email" class="form-control form-control-sm" id="email_as" name="email_as" value="<?php echo $datos["asesoria"]->email_as?>" required>
                            </div>

                            <div class="mb-3 col-4">
                                <label for="domicilio_as" class="form-label">Domicilio</label>
                                <input <?php echo $estadoFormulario ?> type="text" class="form-control form-control-sm" id="domicilio_as" name="domicilio_as" value="<?php echo $datos["asesoria"]->domicilio_as?>">
                            </div>

                            <div class="mb-3 col-12">
                                <label for="descripcion_as" class="form-label">Descripción</label>
                                <textarea class="form-control form-control-sm" id="descripcion_as" name="descripcion_as" <?php echo $estadoFormulario ?>><?php echo $datos["asesoria"]->descripcion_as ?></textarea>
                            </div>

                            <div class="col-9">
                                <button type="submit" class="w-100 btn btn-success btn-lg" 
                                    <?php echo $estadoFormulario ?> >Modificar</button>
                            </div>

                            <div class="col-3">
                                <a class="w-100 btn btn-danger btn-lg" href="<?php echo RUTA_URL ?>/">Atrás</a>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
            


            <?php if($datos['asesoria']->id_estado != 3 && tienePrivilegios($datos['usuarioSesion']->id_rol,[200,300])): ?>
                <button type="button" 
                    onclick="valida_cerrar(<?php echo $datos['asesoria']->id_asesoria ?>)" 
                    data-bs-toggle="modal" data-bs-target="#modalCerrarAccion" 
                    class="w-100 btn btn-warning btn-lg">
                    Cerrar Asesoría
                </button>
            <?php endif ?>
            <?php if($datos['asesoria']->id_estado == 3 && tienePrivilegios($datos['usuarioSesion']->id_rol,[300])): ?>
                <form method="post" action="<?php echo RUTA_URL ?>/asesorias/abrir_asesoria">
                    <input type="hidden" name="id_asesoria" value="<?php echo $datos['asesoria']->id_asesoria ?>">
                    <button type="submit" class="w-100 btn btn-warning btn-lg">
                        Abrir Asesoría
                    </button>
                </form>
            <?php endif ?>
                  


        </div>
        <div class="col-md-5">
            <?php foreach($datos["asesoria"]->acciones as $accion): ?>
                <div class="card" id="accion_<?php echo $accion->id_reg_acciones ?>">
                    <div class="card-body">
                        <?php if($accion->automatica): ?>
                            <span class="card-title"><?php echo $accion->accion ?>: </span>
                            <span class="card-subtitle"><?php echo $accion->nombre_completo ?></span>
                            
                        <?php else: ?>
                            <div class="row">
                                <div class="col-9">
                                    <h5 class="card-subtitle"><?php echo $accion->nombre_completo ?>: </h5>
                                </div>
                                <div class="col-3 text-end">
                                    <?php if($datos['asesoria']->id_estado != 3): ?>
                                        <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol,[300]) || $accion->id_profesor == $datos['usuarioSesion']->id_profesor):?>
                                            <a 
                                                class="btn btn-outline-warning btn-sm" 
                                                onclick="rellenarModal(<?php echo $accion->id_reg_acciones ?>)" 
                                                data-bs-toggle="modal" data-bs-target="#modalEditarAccion"
                                            >
                                                <i class="bi-pencil-square"></i>
                                            </a>
                                            <a 
                                                onclick="del_asesoria_modal(<?php echo $accion->id_reg_acciones ?>)"
                                                data-bs-toggle="modal" data-bs-target="#modalDelAccion" 
                                                class="btn btn-outline-danger btn-sm"
                                            >
                                                <i class="bi-trash"></i>
                                            </a>
                                        <?php endif ?>
                                    <?php endif ?>
                                </div>
                            </div>
                            <p class="card-text ps-2" id="textoAccion_<?php echo $accion->id_reg_acciones ?>">
                                <?php echo $accion->accion ?>
                            </p>
                        <?php endif ?>
                    </div>
                    <div class="card-footer text-end">
                        <span class="card-text"><?php echo formatoFecha($accion->fecha_reg) ?></span>
                    </div>
                </div>
            <?php endforeach ?>
            
        </div>


    </div>
</div>

<!-- ++++++++++++++++++++++++++++++++++++++++ Modal Cerrar Asesoria ++++++++++++++++++ -->

<div class="modal fade" id="modalCerrarAccion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCerrarAccionLabel">
                    ¿Estás seguro que quieres cerrar la Asesoría?
                </h5>
            </div>
            <div class="modal-footer">
                <form method="post" id="formCerrarAccion" 
                    action="<?php echo RUTA_URL ?>/asesorias/cerrar_asesoria"
                >
                    <button type="button" class="btn btn-secondary" 
                        data-bs-dismiss="modal">Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning">
                        Cerrar Asesoría
                    </button>
                    <input type="hidden" id="id_asesoria" name="id_asesoria">
                </form>
            </div>
        </div>
    </div>
</div>


<!-- ++++++++++++++++++++++++++++ Modal Editar Accion +++++++++++++++++++++++++++++++++ -->

<div class="modal fade" id="modalEditarAccion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarAccionLabel">
                    Modificar Accion
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

<!-- ++++++++++++++++++++++++++++++++++++++++ Modal Eliminar Accion ++++++++++++++++++ -->

<div class="modal fade" id="modalDelAccion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDelAccionLabel">
                    ¿Estás seguro que quieres eliminar la Acción?
                </h5>
            </div>
            <div class="modal-footer">
                <form method="post" id="formDelAccion" action="javascript:delAccion(document.getElementById('formDelAccion'))">
                    <button type="button" class="btn btn-secondary" 
                        data-bs-dismiss="modal">Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning" data-bs-dismiss="modal">
                        Borrar Acción
                    </button>
                    <input type="hidden" id="del_id_reg_acciones" name="id_reg_acciones">
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
            <strong class="me-auto">Guardado correctamente</strong>
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
            <strong class="me-auto">Error al Guardar !!!</strong>
        </div>
    </div>
</div>



<script>

    function valida_cerrar(id_asesoria) {
        document.getElementById("id_asesoria").value = id_asesoria
    }

    function del_asesoria_modal(id_reg_accion) {
        document.getElementById("del_id_reg_acciones").value = id_reg_accion
    }

    async function rellenarModal(id_reg_acciones){
        // Marcamos el boton como cargando ... y lo deshabilitamos
        let buttonEditar = document.getElementById("buttonEditar")
        buttonEditar.innerHTML='<span class="spinner-border spinner-border-sm"></span> Loading...'
        buttonEditar.disabled = true

        await fetch(`<?php echo RUTA_URL?>/asesorias/get_accion/${id_reg_acciones}`, {
            headers: {
                "Content-Type": "application/json"
            },
            credentials: 'include'
        })
            .then((resp) => resp.json())
            .then(function(data) {
                let accion = data

                // Relleno los datos del formulario
                document.getElementById("id_reg_acciones").value = id_reg_acciones
                document.getElementById("accion_edit").value = accion.accion

                // Activamos de nuevo el boton, despues de un delay
                setTimeout(() => {
                    buttonEditar.innerHTML='Guardar'
                    buttonEditar.disabled = false
                }, 1000);
                
            })
    }


    async function guardarEditAccion(){
        const datosForm = new FormData(document.getElementById("formEditarAccion"))
        await fetch(`<?php echo RUTA_URL?>/asesorias/set_accion`, {
            method: "POST",
            body: datosForm,
        })
            .then((resp) => resp.json())
            .then(function(data) {

                if(data){
                    document.getElementById('textoAccion_'+datosForm.get('id_reg_acciones')).innerHTML = datosForm.get('accion')
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


    async function delAccion(formulario){
        const datosForm = new FormData(formulario)

        await fetch(`<?php echo RUTA_URL?>/asesorias/del_accion`, {
            method: "POST",
            body: datosForm,
        })
            .then((resp) => resp.json())
            .then(function(data) {

                if(data){
                    document.getElementById('accion_'+datosForm.get('id_reg_acciones')).remove()
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