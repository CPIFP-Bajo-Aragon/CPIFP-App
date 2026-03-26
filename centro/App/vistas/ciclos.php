

<?php require_once RUTA_APP . '/vistas/inc/header_general.php'?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">



<div class="p-5 shadow border mt-5 tarjeta">
<div class="container">
<div class="row">
    

        <!-- encabezado tarjeta -->
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <strong id="ciclo_encabezado">Ciclos del centro</strong>
                </div>
            </div>
            <div class="row mt-3 mb-3">
                <div class="input-group col-12 col-md-5 ">
                    <label for="filtroCiclos" class="input-group-text">Filtro ciclos</label>
                    <input type="text" id="filtroCiclos" class="form-control" placeholder="Escribe el nombre del ciclo" onkeyup="filtrarTablaCiclos()">
                </div>
            </div>
        </div>



        <!-- tabla ciclos -->
        <div class="table-responsive">
        <table class="table table-bordered tabla-formato">

            <thead>
                <tr>
                    <!-- nuevo ciclo -->
                    <th><span>Nuevo ciclo</span><i data-bs-toggle="modal" data-bs-target="#nuevo_ciclo" class="fas fa-plus-circle ms-2 circulo_mas"></i></th>              
                    <!-- grado -->
                    <th class="text-center">Grado<i class="fas fa-graduation-cap ms-2"></i></th>
                    <!-- turno -->
                    <th class="text-center">Turno<i class="fas fa-sun ms-2"></i></th>
                    <!-- departamento -->
                    <th class="text-center">Departamento<i class="fas fa-building ms-2"></i></th>
                    <!-- opciones -->
                    <th class="text-center">Opciones<i class="fas fa-cogs ms-2 circulo_mas"></i></th>
                </tr>
            </thead>

            <tbody>
                <?php foreach($datos['ciclos'] as $ciclo):?> 
                <tr>

                    <!-- nombre ciclo -->
                    <td><?php echo $ciclo->ciclo?></td>
                    <!-- grado -->
                    <td class="text-center"><?php echo $ciclo->nombre?></td>
                    <!-- turno -->
                    <td class="text-center"><?php echo $ciclo->turno?></td>
                    <!-- departamento -->
                    <td class="text-center"><?php echo $ciclo->departamento?></td>
                    <!-- opciones -->
                    <?php if (tienePrivilegios($datos['usuarioSesion']->id_rol,[50])):?>            
                    <td class="text-center">


                        <!-- VER CICLO -->
                        <a href="<?php echo RUTA_URL;?>/ciclo/ciclo_gestion/<?php echo $ciclo->id_ciclo?>" class="text-decoration-none">
                            <img class="icono" id="icono_ver" src="<?php echo RUTA_Icon?>ver.png" alt="Ver">
                        </a>

                        
                        <!-- BORRAR CICLO -->
                        <a data-bs-toggle="modal" data-bs-target="#borrar_<?php echo $ciclo->id_ciclo?>">
                            <img class="icono" src="<?php echo RUTA_Icon?>papelera.png" alt="Borrar"></img>
                        </a>
                        <div class="modal fade" id="borrar_<?php echo $ciclo->id_ciclo?>">
                        <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-3 shadow-lg">
                            <!-- modal header -->
                            <div class="modal-header">
                                <p class="modal-title ms-3">Borrado de ciclos</p> 
                                <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
                            </div>
                            <!-- modal body -->
                            <div class="modal-body mt-3"> 
                                <p>Vas a borrar el ciclo <b> "<?php echo $ciclo->ciclo?> " </b>, estas seguro ? </p>
                            </div>
                            <!-- boton envio -->
                            <div class="modal-footer">
                                <form action="<?php echo RUTA_URL?>/ciclo/borrar_ciclo/<?php echo $ciclo->id_ciclo?>" method="post">
                                    <input type="submit" class="btn" name="borrar" id="boton-modal" value="Borrar">
                                </form>
                            </div>
                        </div>
                        </div>
                        </div> 

                    </td>
                    <?php endif ?>

                </tr>
                <?php endforeach ?>
            </tbody>

        </table>
        </div>

</div>
</div>
</div>


<!--------------------------- NUEVO CICLO ------------------------>


<div class="modal fade" id="nuevo_ciclo">
<div class="modal-dialog modal-dialog-centered modal-lg"> 
<div class="modal-content rounded-3 shadow-lg">

        <!-- modal header -->
        <div class="modal-header">
            <p class="modal-title ms-3">Nuevo ciclo</p>
            <button type="button" class="btn-close me-4" data-bs-dismiss="modal"></button>
        </div>

            <!-- modal body -->
            <div class="modal-body info">
            <div class="row ms-1 me-1">
            <form action="<?php echo RUTA_URL?>/ciclo/nuevo_ciclo" method="post">

                <div class="row">
                    <!-- nombre -->
                    <div class="mb-4 col-12 col-sm-6 col-md-8">
                        <div class="input-group">
                            <label for="ciclo" class="input-group-text">Nombre<sup>*</sup></label>
                            <input type="text" class="form-control" id="ciclo" name="ciclo" required>
                        </div>
                    </div>
                    <!-- codigo -->
                    <div class="mb-4 col-12 col-sm-6 col-md-4">
                        <div class="input-group">
                            <label for="ciclo_corto" class="input-group-text">Codigo<sup>*</sup></label>
                            <input type="text" class="form-control" id="ciclo_corto" name="ciclo_corto" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- departamentos -->
                    <div class="mb-4 col-12 col-sm-6 col-md-5">
                        <div class="input-group">
                            <label for="id_departamento" class="input-group-text">Departamento<sup>*</sup></label>
                            <select name="id_departamento" id="id_departamento" class="form-control" required>
                                <?php foreach($datos['departamentos'] as $depart): ?>
                                    <option value="<?php echo $depart->id_departamento?>"><?php echo $depart->departamento?></option>
                                <?php endforeach ?>   
                            </select>
                        </div>
                    </div>
                    <!-- tipo grado -->
                    <div class="mb-4 col-12 col-sm-6 col-md-4">
                        <div class="input-group">
                            <label for="id_grado" class="input-group-text">Tipo grado<sup>*</sup></label>
                            <select name="id_grado" id="id_grado" class="form-control" required>
                                <?php foreach($datos['grados'] as $grad): ?>
                                    <option value="<?php echo $grad->id_grado?>"><?php echo $grad->nombre?></option>
                                <?php endforeach ?>   
                            </select>
                        </div>
                    </div>
                    <!-- turno -->
                    <div class="mb-4 col-12 col-sm-6 col-md-3">
                        <div class="input-group">
                            <label for="id_turno" class="input-group-text">Turno<sup>*</sup></label>
                            <select name="id_turno" id="id_turno" class="form-control" required>
                                <?php foreach($datos['turnos'] as $turno): ?>
                                    <option value="<?php echo $turno->id_turno?>"><?php echo $turno->turno?></option>
                                <?php endforeach ?>   
                            </select>
                        </div>
                    </div>
                </div>

                <!-- numero cursos -->
                <div class="row">
                    <div class="mb-4 col-12 col-sm-6 col-md-4">
                        <div class="input-group">
                            <label for="num_cursos" class="input-group-text">Nº de Cursos<sup>*</sup></label>
                            <input type="number" class="form-control" id="num_cursos" name="num_cursos" min="1" required>
                        </div>
                    </div>
                </div>

                <!-- codigos cursos -->
                <div id="codigos_cursos"></div>

                <!-- boton cursos -->
                <div class="modal-footer mt-4">
                    <input type="submit" class="btn" name="aceptar" id="boton-modal" value="Confirmar">
                </div>

            </form>
            </div>
            </div>

</div>
</div>
</div>




<script src="<?php echo RUTA_URL;?>/public/js/centro.js"></script>
<?php require_once RUTA_APP . '/vistas/inc/footer.php'?>







<script>

    // Función que genera los campos de código de cursos dinámicamente
    document.getElementById('num_cursos').addEventListener('input', function() {
        var numCursos = parseInt(this.value);
        var container = document.getElementById('codigos_cursos');
        container.innerHTML = ''; // Limpiar los campos existentes


        if (!isNaN(numCursos) && numCursos > 0) {
            for (var i = 1; i <= numCursos; i++) {
                var div = document.createElement('div');
                div.classList.add('row', 'mb-4');
                
                var col = document.createElement('div');
                col.classList.add('col-12', 'col-sm-6', 'col-md-4'); 

                var inputGroup = document.createElement('div');
                inputGroup.classList.add('input-group');

                var label = document.createElement('label');
                label.classList.add('input-group-text');
                label.setAttribute('for', 'codigos_cursos_' + i);
                label.textContent = 'Código ' + i + 'º Curso';

                var input = document.createElement('input');
                input.type = 'text';
                input.classList.add('form-control');
                input.id = 'codigos_cursos_' + i;
                input.name = 'codigos_cursos_' + i;
                input.required = true;

                inputGroup.appendChild(label);
                inputGroup.appendChild(input);

                col.appendChild(inputGroup);
                div.appendChild(col);
                container.appendChild(div);
            }
        }
    });

 
</script>




