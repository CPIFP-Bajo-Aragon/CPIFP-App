<?php require_once RUTA_APP . '/vistas/inc/header.php' ?>

<div class="container-fluid px-4 py-4">

    <div class="row mb-3 align-items-center">
        <div class="col">
            <span class="nombre_modulo_seguimiento">
                <i class="fas fa-building me-2"></i>Empresas colaboradoras
            </span>
        </div>
        <div class="col-auto">
            <a class="btn btn-custom" href="<?php echo RUTA_URL ?>/empresas/nueva">
                <i class="fas fa-plus me-1"></i>Nueva empresa
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead style="background:#0583c3; color:#fff;">
                    <tr>
                        <th>Empresa</th>
                        <th>Contacto</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($datos['empresas'])): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No hay empresas registradas.</td></tr>
                <?php else: ?>
                    <?php foreach($datos['empresas'] as $emp): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($emp->empresa) ?></strong></td>
                        <td><?php echo htmlspecialchars($emp->contacto ?? '-') ?></td>
                        <td><?php echo htmlspecialchars($emp->email ?? '-') ?></td>
                        <td><?php echo htmlspecialchars($emp->telefono ?? '-') ?></td>
                        <td>
                            <?php if($emp->activa): ?>
                                <span class="badge bg-success">Activa</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inactiva</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo RUTA_URL ?>/empresas/editar/<?php echo $emp->id_empresa ?>"
                               class="btn btn-sm btn-outline-secondary" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-info"
                                    onclick="regenerarToken(<?php echo $emp->id_empresa ?>)" title="Regenerar token de acceso">
                                <i class="fas fa-key"></i>
                            </button>
                            <?php if($datos['usuarioSesion']->id_rol >= 300): ?>
                            <button class="btn btn-sm btn-outline-danger"
                                    onclick="eliminarEmpresa(<?php echo $emp->id_empresa ?>)" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function eliminarEmpresa(id){
    if(!confirm('¿Eliminar esta empresa?')) return;
    fetch('<?php echo RUTA_URL ?>/empresas/eliminar', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id_empresa='+id
    }).then(r=>r.json()).then(ok=>{ if(ok) location.reload(); else alert('Error'); });
}

function regenerarToken(id){
    if(!confirm('¿Regenerar el token de acceso? El enlace anterior dejará de funcionar.')) return;
    fetch('<?php echo RUTA_URL ?>/empresas/regenerar_token', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id_empresa='+id
    }).then(r=>r.json()).then(data=>{
        if(data && data.token) alert('Nuevo token generado. Comparte el nuevo enlace de encuesta con la empresa.');
        else alert('Error al regenerar el token');
        location.reload();
    });
}
</script>

<?php require_once RUTA_APP . '/vistas/inc/footer.php' ?>
