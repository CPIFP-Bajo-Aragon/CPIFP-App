<?php require_once RUTA_APP.'/vistas/inc/header.php' ?>

<?php 
    // print_r($datos['filtro']);
    // if (!isset($datos['filtro']['estado'])){
    //     $datos['filtro']['estado'] = array_column($datos['estados'], 'id_estado');
    //     print_r($datos['filtro']);
    //     // exit();
    // }
?>

<div class="container">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo RUTA_URL ?>">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Asesorias</li>
        </ol>
    </nav>

    <div class="float-end">
        <a class="btn btn-primary me-md-4" href="<?php echo RUTA_URL ?>/asesorias/add_asesoria"><h3>+</h3></a>
    </div>

    <form method="get" action="<?php echo RUTA_URL ?>/asesorias/filtro">
        <div class="row">

            <div class="col-2 offset-1">
                <label for="fecha_ini" class="form-label">De </label>
                <input type="date"  name="fecha_ini" id="fecha_ini" onchange="submit()" value="<?php echo $this->datos["filtro"]['fecha_ini'] ?>" class="form-control form-control-sm">
            </div>
            <div class="col-2">
                <label for="fecha_fin" class="form-label"> A </label>
                <input type="date"  name="fecha_fin" id="fecha_fin" onchange="submit()" value="<?php echo $this->datos["filtro"]['fecha_fin'] ?>" class="form-control form-control-sm">
            </div>

            <div class="col-2">
                <label for="buscar" class="form-label">Buscar</label>
                <input type="text"  name="buscar" id="buscar" onchange="submit()" autocomplete="off" class="form-control form-control-sm">
            </div>


            <div class="col-2 offset-1">
                <?php foreach ($datos["estados"] as $estado): ?>
                    <?php
                        $checked = '';
                        if (in_array($estado->id_estado, $datos["filtro"]['estado'])){
                            $checked = 'checked';
                        }
                    ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="estado[]" value="<?php echo $estado->id_estado ?>" id="estado_<?php echo $estado->id_estado ?>" onchange="submit()" <?php echo $checked ?>>
                        <label class="form-check-label" for="estado_<?php echo $estado->id_estado ?>">
                            <?php if($estado->id_estado == 1): ?>
                                <strong class="text-success"><?php echo $estado->estado ?></strong>
                            <?php elseif($estado->id_estado == 2): ?>
                                <strong class="text-warning"><?php echo $estado->estado ?></strong>
                            <?php elseif($estado->id_estado == 3): ?>
                                <strong class="text-danger"><?php echo $estado->estado ?></strong>
                            <?php endif ?>
                        </label>
                    </div>
                <?php endforeach ?>
            </div>

        </div>

    </form>

    <strong class="text-success">Nº de Registros: <?php echo count($datos["asesorias"]) ?></strong>
    <script>
        // ponemos el foco en el input buscar al final de texto
        window.addEventListener("load", function(event) {
            document.getElementById('buscar').value = "<?php echo $datos["filtro"]['buscar']?>"
            document.getElementById('buscar').focus()
        })
    </script>
    
    <!-- <h1>Asesorias </h1> -->

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Título</th>
                <th scope="col">Datos Personales (Nombre, DNI, teléfono, email)</th>
                <th scope="col">Descripción</th>
                <th scope="col">Domicilio</th>
                <th scope="col">Estado</th>
                <th scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $datos["asesorias"] as $asesoria ): ?>
                <tr id="asesoria_<?php echo $asesoria->id_asesoria ?>">
                    <th scope="row"><?php echo $asesoria->id_asesoria ?></th>
                    <td><?php echo $asesoria->titulo_as ?></td>
                    <td><?php 
                        echo ($asesoria->nombre_as) ? "Nombre: ".$asesoria->nombre_as."<br>" : "";
                        echo ($asesoria->dni_as) ? "DNI: ".$asesoria->dni_as."<br>" : "";
                        echo ($asesoria->telefono_as) ? "teléfono: ".$asesoria->telefono_as."<br>" : "";
                        echo ($asesoria->email_as) ? "email: ".$asesoria->email_as."<br>" : "";
                    ?></td>
                    <td><?php echo $asesoria->descripcion_as ?></td>
                    <td><?php echo $asesoria->domicilio_as ?></td>
                    <td>
                        <?php if($asesoria->id_estado == 1): ?>
                            <strong class="text-success"><?php echo $asesoria->estado ?></strong>
                        <?php elseif($asesoria->id_estado == 2): ?>
                            <strong class="text-warning"><?php echo $asesoria->estado ?></strong>
                        <?php elseif($asesoria->id_estado == 3): ?>
                            <strong class="text-danger"><?php echo $asesoria->estado ?></strong>
                        <?php endif ?>
                    </td>


                    <td class="text-nowrap">
    <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol,[100,200,300]) || $asesoria->acciones[0]->id_profesor == $datos['usuarioSesion']->id_profesor): ?>
                        <a class="btn btn-outline-warning btn-sm" href="<?php echo RUTA_URL ?>/asesorias/ver_asesoria/<?php echo $asesoria->id_asesoria ?>">
                            <i class="bi-pencil-square"></i>
                        </a>
    <?php endif ?>

    <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol,[300])): ?>
                        <a 
                            onclick="del_asesoria_modal(<?php echo $asesoria->id_asesoria ?>)" 
                            data-bs-toggle="modal" data-bs-target="#modalDelAsesoria" 
                            class="btn btn-outline-danger btn-sm"
                        >
                            <i class="bi-trash"></i>
                        </a>
    <?php endif ?>
                    </td>


                </tr>
                <tr id="acciones_<?php echo $asesoria->id_asesoria ?>">
                    <td></td>
                    <td colspan="6">
                        <ul>
                            <?php foreach($asesoria->acciones as $accion): ?>
                                <li>
                                    <strong>Fecha: </strong> <?php echo  formatoFecha($accion->fecha_reg) ?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <strong>Creada por: </strong> <?php echo  $accion->nombre_completo ?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <strong>Acción: </strong> <?php echo  $accion->accion ?>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>

</div>


<!-- ++++++++++++++++++++++++++++++++++++++++ Modal Eliminar Asesoria ++++++++++++++++++ -->

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

