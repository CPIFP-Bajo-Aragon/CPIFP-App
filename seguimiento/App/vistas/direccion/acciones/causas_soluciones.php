

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<?php require_once RUTA_APP . '/vistas/inc/menu_configuracion.php'?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">




<div class="row m-4">
    <div class="col-12 col-md-5">
        <button id="todas" class="btn btn-md btn-custom active" onclick="filtrarTablaCausas(4)">Ver todas</button>
        <button id="causas" class="btn btn-md btn-custom" onclick="filtrarTablaCausas(1)">Causas</button>
        <button id="trimes" class="btn btn-md btn-custom" onclick="filtrarTablaCausas(2)">S.Trimestrales</button>
        <button id="final" class="btn btn-md btn-custom" onclick="filtrarTablaCausas(3)">S.Finales</button>
    </div>
</div>


  



<!-- Tabla responsive -->
<div class="table-responsive">
<table class="table m-5 tabla-formato">

        <!-- CABECERA TABLA -->
        <thead>
            <tr>
                <th id="tabla-titulo-container">
                    <span id="tabla-titulo">Todas las Causas y Soluciones</span>
                    <i data-bs-toggle="modal" data-bs-target="#nueva_accion" class="fas fa-plus-circle ms-2" style="color: white; font-size: 1.3em; vertical-align: middle"></i>
                </th>
                <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol,[50])):?>
                    <th style="text-align:center">Opciones<i class="fas fa-cogs ms-2" style="color: white; font-size: 1.3em; vertical-align: middle"></i></th>
                <?php endif ?> 
            </tr>
        </thead>
        

        <tbody id="tablaCuerpo">
            <?php foreach ($datos['causas_soluciones'] as $caus) : ?>
                <tr class="filtrar accion_<?php echo $caus->id_accion; ?>">

                    <td><?php echo $caus->solucion ?></td>

                    <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol, [50])) : ?>
                    <td>


                            <!-- EDICION DE CAUSA Y SOLUCIONES -->
                            <a data-bs-toggle="modal" data-bs-target="#editar_<?php echo $caus->id_solucion ?>">
                                <img class="icono" src="<?php echo RUTA_Icon ?>editar.png">
                            </a>

                            <div class="modal fade" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true" id="editar_<?php echo $caus->id_solucion ?>">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content rounded-3 shadow-lg">

                                <div class="modal-header">
                                    <p class="modal-title ms-3">Edición de acciones</p>
                                    <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
                                </div>

                                 <!-- Modal Body -->
                                <div class="modal-body">
                                <div class="row ms-1 me-1">
                                <form action="<?php echo RUTA_URL?>/acciones/editar_causa_solucion/<?php echo $caus->id_solucion ?>" method="post">

                                        <div class="row mt-2 mb-4">
                                            <div class="col-12">
                                                <div class="input-group">
                                                    <label for="accion" class="input-group-text" style="color:#0583c3;">Tipo de acción<sup>*</sup></label>
                                                    <select id="accion" name="accion" class="form-select form-control-md" required>
                                                        <option value="" disabled selected>Selecciona una opción</option>
                                                        <?php foreach ($datos['tipos'] as $tipos): ?>
                                                            <option value="<?php echo $tipos->id_accion; ?>" <?php echo ($tipos->id_accion == $caus->id_accion) ? 'selected' : ''; ?>>
                                                                <?php echo $tipos->accion; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <textarea class="form-control" id="descripcion" name="descripcion" required rows="4" placeholder="Escribe una descripcion"><?php echo $caus->solucion;?></textarea>
                                            </div>
                                        </div>

                                        <!-- Modal Footer -->
                                        <div class="modal-footer">
                                            <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Editar">
                                        </div>
                                    
                                </form>
                                </div>
                                </div>

                            </div>
                            </div>
                            </div>



                            <a data-bs-toggle="modal" data-bs-target="#borrar_<?php echo $caus->id_solucion?>">
                                <img class="icono" src="<?php echo RUTA_Icon ?>papelera.png">
                            </a>
                            <div class="modal fade" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true" id="borrar_<?php echo $caus->id_solucion?>">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-3 shadow-lg">
                                        <div class="modal-header">
                                            <p class="modal-title ms-3">Borrado de acciones</p>
                                            <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body mt-3">
                                            <p style="text-align:center">Vas a borrar: <b>"<?php echo $caus->solucion?>"</b> , estas seguro?</p>
                                        </div>    
                                        <div class="modal-footer">                                 
                                            <form action="<?php echo RUTA_URL?>/acciones/borrar_causa_solucion/<?php echo $caus->id_solucion ?>" method="post">
                                                <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Borrar">
                                            </form>   
                                        </div>                                   
                                    </div>
                                </div>
                            </div>


                    </td>
                    <?php endif; ?>


                </tr>
            <?php endforeach; ?>
        </tbody>

</table>
</div>
    




<!-- NUEVA CAUSA -->
<div class="modal fade" id="nueva_accion" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-lg">
<div class="modal-content rounded-3 shadow-lg">

        <!-- Modal Header -->
        <div class="modal-header">
            <p class="modal-title ms-3">Alta de acciones</p>
            <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
            <div class="row ms-1 me-1">

                <form action="<?php echo RUTA_URL?>/acciones/nueva_causa_solucion" method="post">

                    <div class="row mt-2 mb-4">
                        <div class="col-12">
                            <div class="input-group">
                                <label for="accion" class="input-group-text" style="color:#0583c3;">Tipo de accion<sup>*</sup></label>
                                <select id="accion" name="accion" class="form-select form-control-md" required>
                                    <option value="" disabled selected>Selecciona una opción</option>
                                    <?php foreach($datos['tipos'] as $tipos):?>
                                        <option value=<?php echo $tipos->id_accion?>><?php echo $tipos->accion;?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <textarea class="form-control" id="descripcion" name="descripcion" required rows="4" placeholder="Escribe una descripcion"></textarea>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Confirmar">
                    </div>

                </form>
            </div>
        </div>

</div>
</div>
</div>



<script src="<?php echo RUTA_URL;?>/public/js/main.js"></script>
<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>















          




