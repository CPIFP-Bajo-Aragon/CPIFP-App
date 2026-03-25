



function filtrarTablaCausas(accion) {
    // Obtener todas las filas de la tabla
    const filas = document.querySelectorAll('table tbody tr');

    // Recorrer las filas de la tabla
    filas.forEach(fila => {
        if (accion == 4) {
            // Si la opción seleccionada es "TODAS", mostrar todas las filas
            fila.style.display = '';
        } else if (fila.classList.contains('accion_' + accion)) {
            // Si la fila coincide con la acción seleccionada, mostrarla
            fila.style.display = '';
        } else {
            // Si no coincide con la acción seleccionada, ocultarla
            fila.style.display = 'none';
        }
    });

    // Cambiar el título del <th> dinámicamente según el filtro seleccionado
    const thTitulo = document.getElementById('tabla-titulo');
    if (accion == 4) {
        thTitulo.textContent = 'Todas las Causas y Soluciones';
    } else if (accion == 1) {
        thTitulo.textContent = 'Causas';
    } else if (accion == 2) {
        thTitulo.textContent = 'Soluciones Trimestrales';
    } else if (accion == 3) {
        thTitulo.textContent = 'Soluciones Finales';
    }

    // Eliminar las clases previas de todos los botones
    const buttons = document.querySelectorAll('.btn-custom');
    buttons.forEach(button => {
        button.classList.remove('active');
    });

    // Asignar la clase 'active' al botón seleccionado
    const activeButton = document.getElementById(
        accion == 4 ? 'todas' :
        accion == 1 ? 'causas' :
        accion == 2 ? 'trimes' :
        'final'
    );

    activeButton.classList.add('active');
}



/************************* PROGRAMACIONES ****************************** */


function filtrar_programaciones(accion) {
    // Obtener todas las filas de la tabla
    const filas = document.querySelectorAll('table tbody tr');

    // Recorrer las filas de la tabla
    filas.forEach(fila => {
        const esNueva = fila.getAttribute('data-nueva') == '1';
        const sinProgramacion = fila.getAttribute('data-sin-programacion') == '1';

        // Si la acción es 0 (todas), mostrar todas las filas
        if (accion == 0) {
            fila.style.display = '';
        } 
        // Si la acción es 1 (solo nuevas), mostrar solo las filas con "nueva" = true
        else if (accion == 1 && esNueva) {
            fila.style.display = '';
        } 
        // Si la acción es 2 (sin programación), mostrar solo las filas con "sin_programacion" = true
        else if (accion == 2 && sinProgramacion) {
            fila.style.display = '';
        } 
        // Si no coincide con la acción seleccionada, ocultarla
        else {
            fila.style.display = 'none';
        }
    });

    // Eliminar las clases previas de todos los botones
    const buttons = document.querySelectorAll('.btn-custom');
    buttons.forEach(button => {
        button.classList.remove('active');
    });

    // Asignar la clase 'active' al botón seleccionado
    if (accion === 0) {
        document.getElementById('todas').classList.add('active');
    } else if (accion === 1) {
        document.getElementById('cambiadas').classList.add('active');
    } else if (accion === 2) {
        document.getElementById('sin_programacion').classList.add('active');
    }
}



