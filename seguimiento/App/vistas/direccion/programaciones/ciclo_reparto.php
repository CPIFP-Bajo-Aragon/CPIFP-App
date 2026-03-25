

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_mi_departamento.php'?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">




<table class="table table-bordered m-5 tabla">

    <thead>
        <tr>
            <th colspan="6" >
                <?php if(!empty($datos['curso_ciclo'])): 
                    echo $datos['curso_ciclo'][0]->ciclo.' ('.$datos['curso_ciclo'][0]->curso.')';
                endif; ?>
                <a class="nav-link ms-2" style="font-size: 1.3em; display: inline-flex; align-items: center;" href="<?php echo RUTA_URL?>/jefeDep">
                    <i class="fas fa-arrow-circle-left" style="vertical-align: middle"></i>
                </a>
            </th>
        </tr>
        <tr>
            <th>Modulo <i class="fas fa-book-open"></i></th>
            <th colspan="2" style="text-align:center">Reparto horas <i class="fas fa-clock"></i></th>
            <th colspan="2" style="text-align:center">Programaciones <i class="fas fa-book"></i></th>
        </tr>
        <tr>
            <th></th>
            <th style="text-align:center">Profesores <i class="fas fa-users"></i></th>
            <th style="text-align:center">Horas</th>
            <th style="text-align:center">Descargar <i class="fas fa-users"></i></th>
            <th style="text-align:center">Nueva <i class="fas fa-users"></i></th>
        </tr>
    </thead>



    <tbody>
    <?php if(!empty($datos['curso_ciclo'])):
    foreach($datos['curso_ciclo'] as $ciclo):?>

        <tr>

            <!-- nombre modulo -->
            <td><?php echo $ciclo->modulo.' ('.$ciclo->nombre_corto.')'?></td>


            <td style="text-align:center" data-bs-toggle="modal" data-bs-target="#anadir_<?php echo $ciclo->id_modulo?>" style="cursor:pointer">
                <?php 
                $foundProf = false; 
                foreach ($datos['profes'] as $prof) {
                    foreach ($datos['prof_mod'] as $mod) {
                        if ($prof->id_profesor == $mod->id_profesor && $mod->id_modulo == $ciclo->id_modulo) {
                            echo $prof->nombre_completo.'<br>';
                            $foundProf = true;
                        }
                    }
                }
               if (!$foundProf) { ?>
                    <button class="btn" style="border: none; background: transparent; padding: 0;">
                        <i class="fas fa-chalkboard-teacher" style="color: #0583c3;"></i>
                    </button>
                <?php } ?>
            </td>

            <td style="text-align:center" data-bs-toggle="modal" data-bs-target="#anadir_<?php echo $ciclo->id_modulo?>" style="cursor:pointer">
                <?php 
                $foundProf = false; 
                foreach ($datos['profes'] as $prof) {
                    foreach ($datos['prof_mod'] as $mod) {
                        if ($prof->id_profesor == $mod->id_profesor && $mod->id_modulo == $ciclo->id_modulo) {
                            echo $mod->horas_profesor . ' hrs'.'<br>';
                            $foundProf = true;
                        }
                    }
                }
               if (!$foundProf) { ?>
                    <button class="btn" style="border: none; background: transparent; padding: 0;">
                        <i class="fas fa-chalkboard-teacher" style="color: #0583c3;"></i>
                    </button>
                <?php } ?>
            </td>


            <td style="text-align:center ; color: #0583c3;">
                <?php foreach($datos['programaciones'] as $programaciones):
                if($programaciones->id_modulo == $ciclo->id_modulo):?>
                    <form method="post" action="<?php echo RUTA_URL?>/jefeDep/descargar_programacion/<?php echo $ciclo->id_modulo?>">
                        <input type="hidden" name="ruta_archivo" id="ruta" value="<?php echo $programaciones->ruta?>">
                        <button type="submit" class="nav-link evaluacion-link" style="padding: 5px 15px; color:#0583c3; margin-right: 10px;">
                        <i class="fa fa-download"></i> Descargar 
                        </button>
                    </form>
               <?php endif; endforeach ?>
            </td>


            <td style="text-align:center ; color: #0583c3;">
                <?php foreach($datos['programaciones'] as $programaciones):
                if($programaciones->id_modulo == $ciclo->id_modulo):?>
                        <span><?php echo $programaciones->nueva==1 ?  '  Si' : '  No' ?></span>
               <?php endif; endforeach ?>
            </td>


            <!-- MODAL REPARTO HORAS-->
            <div class="modal fade" id="anadir_<?php echo $ciclo->id_modulo?>">
            <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h2 class="modal-title"><?php echo $ciclo->modulo.' ('.$ciclo->curso.')'?></h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body --> 
                <div class="modal-body">
                <form method="post" action="<?php echo RUTA_URL?>/jefeDep/reparto/<?php echo $ciclo->id_modulo.'-'.$datos['curso_ciclo'][0]->id_departamento?>" class="card-body" id="formRepartoHoras">
                                        
                        <div class="row mb-5 ps-4">                     
                            <div class="col-4">
                                <div class="input-group">
                                    <label for="hrs_totales" class="input-group-text">Horas totales</label>
                                    <input type="text" style="background-color:#F2F2F2;" class="form-control" id="hrs_totales" name="hrs_totales" value="<?php echo $ciclo->horas_totales;?> hrs." readonly>
                                </div>
                            </div>  
                            <div class="col-4">
                                <div class="input-group">
                                    <label for="hrs_semanales" class="input-group-text">Horas semanales</label>
                                    <input type="text" style="background-color:#F2F2F2;" class="form-control" id="hrs_semanales" name="hrs_semanales" value="<?php echo $ciclo->horas_semanales;?> hrs." readonly>
                                </div>
                            </div> 
                        </div>

                        <?php foreach ($datos['profes'] as $profes): ?>
                        <div class="row mb-3 ps-4">
                            <div class="col-8">
                                <div class="input-group">
                                    <input type="number" id="horas" name="horas[]" class="form-control" 
                                        value="<?php foreach ($datos['prof_mod'] as $prof_mod) {
                                                if (($prof_mod->id_profesor == $profes->id_profesor) && ($prof_mod->id_modulo == $ciclo->id_modulo)) {
                                                        echo $prof_mod->horas_profesor;
                                                    }
                                                } ?>" 
                                        min="1" max="<?php echo $ciclo->horas_semanales;?>" placeholder="Horas"> 
                                    <label for="horas" class="input-group-text" style="width:400px"><?php echo $profes->nombre_completo; ?></label>
                                    <input type="hidden" name="profes[]" value="<?php echo $profes->id_profesor ?>">
                                </div>  
                            </div>
                        </div>
                        <?php endforeach ?> 

                        <input type="hidden" name="ciclo" value="<?php echo $ciclo->id_ciclo?>">
                        <input type="hidden" name="id_curso" value="<?php echo $ciclo->id_curso?>">

                        <div class="modal-footer mt-2">
                            <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Confirmar">
                        </div>

                </form>
                </div>

            </div>
            </div>
            </div>

        </tr>

    <?php endforeach; endif; ?>
    </tbody>

</table>





<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>   

