

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_mi_departamento.php'?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo RUTA_URL ?>/public/css/jefeDep/estilos_jefeDep.css">





<div  class="card m-5 mb-5 shadow-lg">


    <div class="card-header fondo_principal_texto_blanco">
        <div class="cabecera_btn_volver">
            <h5 class="margen_derecho_20px"><?php echo $datos['info_modulo'][0]->ciclo.' - '.$datos['info_modulo'][0]->modulo.' ('.$datos['info_modulo'][0]->curso.')'?></b></h5>
            <a class="nav-link" href="<?php echo RUTA_URL?>/JDReparto/fol_leo_modulos">
                <i class="fas fa-arrow-circle-left"></i> Volver
            </a>
        </div>
    </div>



    <div class="card-body">
        <!-- horas totales y horas semanales -->
        <div class="row mt-3 ms-2">
            <div class="col-md-3">
                <div class="input-group mb-3">
                    <span class="input-group-text">Horas Totales</span>
                    <input type="text" class="form-control" value="<?php echo $datos['info_modulo'][0]->horas_totales;?>" readonly>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group mb-3">
                    <span class="input-group-text">Horas Semanales</span>
                    <input type="text" class="form-control" value="<?php echo $datos['info_modulo'][0]->horas_semanales; ?>" readonly>
                </div>
            </div>
        </div>
        <!-- formulario reparto profesores -->
        <form method="post" action="<?php echo RUTA_URL?>/JDReparto/reparto/<?php echo $datos['info_modulo'][0]->id_modulo?>" class="card-body" id="formRepartoHoras">                
            <?php foreach ($datos['profesores_departamento'] as $profes): ?>
                <div class="row mt-3 mb-3">
                    <div class="col-12 col-md-6">
                        <div class="input-group">
                            <input type="number" id="horas" name="horas[]" class="form-control" 
                                value="<?php foreach ($datos['profesores_modulo'] as $prof_mod) {
                                        if (($prof_mod->id_profesor == $profes->id_profesor) && ($prof_mod->id_modulo == $datos['info_modulo'][0]->id_modulo)) {
                                                echo $prof_mod->horas_profesor;
                                            }
                                        } ?>" 
                                min="1" placeholder="Horas"> 
                            <label for="horas" class="input-group-text ancho_400px"><?php echo $profes->nombre_completo; ?></label>
                            <input type="hidden" name="profes[]" value="<?php echo $profes->id_profesor ?>">
                        </div>  
                    </div>
                </div>
            <?php endforeach ?> 
            <div class="mt-3">
                <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Confirmar">
            </div>  
        </form>
    </div>

</div>



<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>



