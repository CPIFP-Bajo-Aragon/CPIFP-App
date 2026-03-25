

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_seguimiento.php'?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">





 
<div class="card m-5 mb-5 shadow-lg" style="width:85%">



    <!-- datos del modulo -->
    <div class="card-header" style="background-color:#0583c3; color:white;">
        <div><h6><?php echo $datos['lectivo'][0]->lectivo?></h6></div>
        <div><h6>Horas totales del modulo: <?php echo $datos['datos_modulo'][0]->horas_totales?> horas</h6></div>
        <div class="mb-2">
            <?php foreach($datos['datos_modulo'] as $dat) { ?>
                <h6>Profesores: <?php echo $dat->nombre_completo?></h6>
            <?php } ?>
        </div>
        <div><h6>Horas semanales: <?php echo $datos['datos_modulo'][0]->horas_semanales?> horas</h6></div>
    </div>



    <div class="card-body">
            
            <!-- referente a las horas de clase a la semana -->
            <div class="section mb-4">
                <h5 class="section-title" style="color:orangered;">Horas de clase al dia</h5>  
                <form method="post" class="card-body" action="<?php echo RUTA_URL?>/PDatos/horario_semana/<?php echo $datos['datos_modulo'][0]->id_modulo?>"> 
                    <div class="row m-2">
                        <?php foreach($datos['dias_semana'] as $dias_sem):?>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-2">
                                <div class="input-group-md" style="width:100%;">
                                    <label class="input-group-text" for="dias_semana"><?php echo $dias_sem->dia_semana ?></label>
                                    <input type="hidden" name="id_horario[]" value="<?php echo $dias_sem->id_dia_semana ?>">
                                    <input type="number" class="form-control" 
                                        value="<?php foreach($datos['horario_modulo'] as $hor) {
                                            if($hor->id_dia_semana == $dias_sem->id_dia_semana) {
                                                echo $hor->horas_dia;
                                            }
                                        } ?>"  
                                        name="horas[]" min="0" max="10" placeholder="Horas">
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-2">
                            <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Guardar">
                        </div> 
                    </div>
                </form>
            </div>



            <!-- asignacion de temas y horas -->
            <div class="section mb-4">
            <h5 class="section-title" style="color:orangered;">Asignacion de temas y horas </h5>
            <div class="row mt-3">
                <?php if($datos['suma_total'][0]->suma_temas!=$datos['datos_modulo'][0]->horas_totales){?>
                    <p style="color:#0583c3">Las horas totales del modulo son <b><?php echo $datos['datos_modulo'][0]->horas_totales?> horas.</b> 
                    <br>Crea temas nuevos desde el boton de la tabla<i data-bs-toggle="modal" data-bs-target="#tema_nuevo" class="fas fa-plus-circle ms-2" style="font-size: 1.3em; vertical-align: middle"></i><b> (maximo 15 temas)</b> 
                    y distribuye estas horas entre los temas para que te coincidan con las pendientes de asignar. Los temas Examanes y Dual se crearan automaticamente.
                    <br>HORAS PENDIENTES ASIGNAR: <b><?php echo ($datos['datos_modulo'][0]->horas_totales) - ($datos['suma_total'][0]->suma_temas)?></b> horas.</p>
                <?php }else{ ?>
                    <p style="color:green">Las horas asignadas con las del curriculo coinciden</p>
                <?php } ?>
            </div>


            <table class="table table-bordered m-4 tabla-formato">
                <thead>
                    <tr>
                        <th>Temas
                            <?php if($datos['total_temas'][0]->total < 15): ?>
                                <i data-bs-toggle="modal" data-bs-target="#tema_nuevo" class="fas fa-plus-circle ms-2" 
                                style="color: white; font-size: 1.3em; vertical-align: middle; cursor: pointer;"></i>
                            <?php endif; ?>
                        </th>
                        <th class="text-center">Horas</th>
                        <th class="text-center">Titulo</th>
                        <th style="text-align: center;">Opciones<i class="fas fa-cogs ms-2" style="font-size: 1.3em; vertical-align: middle"></i></th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach($datos['temas'] as $temas):?>
                <tr>

                            <td class="text-center">
                                <?php if($temas->descripcion=='Examenes' || $temas->descripcion=='Dual' ) {
                                    echo '---';
                                } else {
                                    echo $temas->tema;
                                }
                                ?>
                            </td>
                            <td class="text-center"><?php echo $temas->total_horas?> hrs.</td>
                            <td><?php echo $temas->descripcion?></td>


                            <td style="text-align:center">
                                <a data-bs-toggle="modal" data-bs-target="#editar_<?php echo $temas->id_tema?>">
                                    <img class="icono" src="<?php echo RUTA_Icon?>editar.png" alt="Editar">
                                </a>
                                <div class="modal fade" id="editar_<?php echo $temas->id_tema?>">
                                <div class="modal-dialog modal-dialog-centered modal-md">
                                <div class="modal-content rounded-3 shadow-lg">
                                        <div class="modal-header">
                                            <p class="modal-title ms-3">Edición tema</p> 
                                            <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                        <div class="row ms-1 me-1">
                                        <form action="<?php echo RUTA_URL?>/PDatos/editar_tema/<?php echo $temas->id_tema?>" method="post">
                                                <!-- titulo-->
                                                <div class="row mt-4">
                                                    <div class="input-group">
                                                        <label for="descripcion" class="input-group-text">Titulo<sup>*</sup></label>
                                                        <input type="text" class="form-control" id="descripcion" name="descripcion" value="<?php echo $temas->descripcion?>" required 
                                                            <?php if($temas->descripcion=='Examenes' || $temas->descripcion=='Dual'){
                                                                echo 'readonly';
                                                            }?>
                                                        >
                                                    </div>
                                                </div>
                                                <!-- horas -->
                                                <div class="row mt-4">
                                                    <?php if($temas->descripcion!='Examenes' && $temas->descripcion!='Dual'){ ?>
                                                    <div class="col-5">
                                                        <div class="input-group">
                                                            <label for="numero_tema" class="input-group-text">Nº Tema<sup>*</sup></label>
                                                            <input type="number" class="form-control" min="1" max="15" id="numero_tema" name="numero_tema" value="<?php echo $temas->tema?>" required>
                                                        </div>
                                                    </div>
                                                    <?php }else{ ?>
                                                            <input type="hidden" name="numero_tema" value="<?php echo $temas->tema ?>">
                                                <?php } ?>
                                                    <div class="col-5">
                                                        <div class="input-group">
                                                            <label for="total_horas" class="input-group-text">Horas<sup>*</sup></label>
                                                            <input type="number" class="form-control" id="total_horas" min="0" name="total_horas" value="<?php echo $temas->total_horas?>" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer mt-4">
                                                    <input type="hidden" name="id_modulo" id="id_modulo" value="<?php echo $temas->id_modulo?>">
                                                    <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Confirmar">
                                                </div>
                                        </form>
                                        </div>
                                        </div>
                                </div>
                                </div>
                                </div>


                                <?php if($temas->descripcion!='Examenes' && $temas->descripcion!='Dual'){ ?>
                                <a data-bs-toggle="modal" data-bs-target="#borrar_<?php echo $temas->id_tema?>">
                                    <img class="icono" src="<?php echo RUTA_Icon?>papelera.png" alt="Borrar">
                                </a>
                                <div class="modal fade" id="borrar_<?php echo $temas->id_tema?>">
                                <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-3 shadow-lg">
                                    <!-- modal header -->
                                    <div class="modal-header">
                                        <p class="modal-title ms-3">Borrado de tema</p> 
                                        <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
                                    </div>
                                    <!-- modal body -->
                                    <div class="modal-body mt-3">
                                        <p>Vas a borrar el <b>Tema <?php echo $temas->tema?> ( <?php echo $temas->descripcion?> ) </b>. Si confirmas, borrarás todas las 
                                        horas del diario que tenias asociadas a este tema. ¿Estás seguro?</p>
                                    </div>
                                    <!-- boton -->
                                    <div class="modal-footer">
                                        <form action="<?php echo RUTA_URL?>/PDatos/borrar_tema/<?php echo $temas->id_tema?>" method="post">
                                            <input type="hidden" name="id_modulo" id="id_modulo" value="<?php echo $temas->id_modulo?>">
                                            <input type="submit" class="btn" name="borrar" id="boton-modal" value="Borrar">
                                        </form>
                                    </div>
                                </div>
                                </div>
                                </div>
                                <?php } ?>

                            </td>

                </tr>
                <?php endforeach;?>
                </tbody>

            </table>




<!-- MODAL NUEVO TEMA -->

<div class="modal fade" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true" id="tema_nuevo">
<div class="modal-dialog modal-dialog-centered modal-md">
<div class="modal-content rounded-3 shadow-lg">
          
    <div class="modal-header">
        <p class="modal-title ms-3">Nuevo tema</p> 
        <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
    </div>

    <form method="post" action="<?php echo RUTA_URL?>/PDatos/nuevo_tema/<?php echo $datos['datos_modulo'][0]->id_modulo?>"">
        <div class="modal-body">  
            <div class="row mt-4">
                <div class="input-group">
                    <label for="descripcion" class="input-group-text">Titulo<sup>*</sup></label>
                    <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                </div>
            </div>
            <div class="row mt-4 mb-4">
                <div class="col-5">
                    <div class="input-group">
                        <label for="numero_tema" class="input-group-text">Nº Tema<sup>*</sup></label>
                        <input type="number" class="form-control" min="1" max="15" id="numero_tema" name="numero_tema" required>
                    </div>
                </div>
                <div class="col-5">
                    <div class="input-group">
                        <label for="total_horas" class="input-group-text">Horas<sup>*</sup></label>
                        <input type="number" class="form-control" min="0" id="total_horas" name="total_horas" required>
                    </div>
                </div>
            </div>
        </div>  
        <div class="modal-footer">
            <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Confirmar">  
        </div>  
    </form>
   
</div>
</div>
</div>





<script>
document.addEventListener("DOMContentLoaded", function() {

    const prohibidas = ["actividades","otros","faltas","examenes","dual"];

    function quitarAcentos(str) {
        return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    }

    function validarFormulario(form) {
        if(form.dataset.validado) return; // ya agregado, no duplicar
        const input = form.querySelector("#descripcion");

        form.addEventListener("submit", function(e) {
            let desc = input.value.trim().toLowerCase();
            desc = quitarAcentos(desc);
            if (prohibidas.includes(desc)) {
                e.preventDefault();
                alert("Esta palabra está reservada por el sistema. Usa otra descripción ( palabras reservadas: Dual, Examenes, Faltas, Otros y Actividades )");
                input.focus();
            }
        });

        form.dataset.validado = "1"; // marcamos que ya se agregó
    }

    // Modal de nuevo tema
    const modalNuevo = document.getElementById("tema_nuevo");
    if(modalNuevo) {
        modalNuevo.addEventListener('shown.bs.modal', function () {
            const form = modalNuevo.querySelector("form");
            validarFormulario(form);
        });
    }

    // Modales de edición
    const modalesEdicion = document.querySelectorAll("div[id^='editar_']");
    modalesEdicion.forEach(modal => {
        modal.addEventListener('shown.bs.modal', function () {
            const form = modal.querySelector("form");
            validarFormulario(form);
        });
    });

});
</script>



<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>





<script>


// ****************************** VALIDACION HORAS SEMANALES ***********************************************

    document.querySelector('form[action*="horario_semana"]').addEventListener('submit', function(e) {
        const horasInputs = document.querySelectorAll('input[name="horas[]"]');
        let totalHoras = 0;

        horasInputs.forEach(input => {
            const valor = parseInt(input.value);
            if (!isNaN(valor)) {
                totalHoras += valor;
            }
        });

        const horasSemanales = <?php echo (int)$datos['datos_modulo'][0]->horas_semanales; ?>;

        if (totalHoras !== horasSemanales) {
            alert("La suma de las horas asignadas por día (" + totalHoras + " hrs.) no coincide con las horas semanales del módulo (" + horasSemanales + " hrs.).");
            e.preventDefault();
        }
    });



// ****************************** VALIDACION HORAS MODULO ***********************************************


window.addEventListener('load', function () {
    const sumaHorasTemas = <?php echo (int)$datos['suma_total'][0]->suma_temas; ?>;
    const horasTotalesModulo = <?php echo (int)$datos['datos_modulo'][0]->horas_totales; ?>;

    if (sumaHorasTemas !== horasTotalesModulo) {
        const diferencia = horasTotalesModulo - sumaHorasTemas;
        let mensajeExtra = "";

        if (diferencia > 0) {
            mensajeExtra = `FALTAN por asignar ${diferencia} hora(s).`;
        } else {
            mensajeExtra = `SOBRAN ${Math.abs(diferencia)} hora(s) asignadas.`;
        }

        alert(`⚠️ La suma de horas asignadas a los temas (${sumaHorasTemas} hrs) no coincide con las horas totales del módulo (${horasTotalesModulo} hrs).\n\n${mensajeExtra}`);
    }
});


</script>


