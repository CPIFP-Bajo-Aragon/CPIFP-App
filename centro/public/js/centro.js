


//********************************* filtro tabla departamentos ************************/

function filtrarTablaDep(accion) {
    const filas = document.querySelectorAll('table tbody tr');
    filas.forEach(fila => {
        if (accion == 4) {
            fila.style.display = '';
        } else if (fila.classList.contains('accion_' + accion)) {
            fila.style.display = '';
        } else {
            fila.style.display = 'none';
        }
    });

    const buttons = document.querySelectorAll('.btn-custom');
    buttons.forEach(button => {
        button.classList.remove('active');
    });

    const activeButton = document.getElementById(
        accion == 4 ? 'todos' :
        accion == 0 ? 'estrategicos' :
        'formacion'
    );
    activeButton.classList.add('active');
}





//********************************* filtro tabla personal centro ***********************************/

function filtrarTablaPersonal() {

    let filtro = document.getElementById('filtroProfesor').value.toLowerCase();
    filtro = filtro.normalize("NFD").replace(/[\u0300-\u036f]/g, ""); // Eliminar acentos

    // Obtener la tabla y todas sus filas (excepto el encabezado)
    let tabla = document.querySelector('.tabla-formato');
    let filas = tabla.getElementsByTagName('tr');
    for (let i = 1; i < filas.length; i++) { // Comenzamos desde 1 para evitar el encabezado
        let celdas = filas[i].getElementsByTagName('td');
        let nombreProfesor = celdas[0].textContent.toLowerCase();
        nombreProfesor = nombreProfesor.normalize("NFD").replace(/[\u0300-\u036f]/g, ""); // Eliminar acentos del nombre
        if (nombreProfesor.indexOf(filtro) > -1) {
            filas[i].style.display = ''; // Mostrar la fila si coincide
        } else {
            filas[i].style.display = 'none'; // Ocultar la fila si no coincide
        }
    }
}



//********************************* filtro tabla ciclos *************************************/

function filtrarTablaCiclos() {

    let filtro = document.getElementById('filtroCiclos').value.toLowerCase();
    filtro = filtro.normalize("NFD").replace(/[\u0300-\u036f]/g, ""); // Eliminar acentos

    // Obtener la tabla y todas sus filas (excepto el encabezado)
    let tabla = document.querySelector('.tabla-formato');
    let filas = tabla.getElementsByTagName('tr');
    for (let i = 1; i < filas.length; i++) { // Comenzamos desde 1 para evitar el encabezado
        let celdas = filas[i].getElementsByTagName('td');
        let nombreProfesor = celdas[0].textContent.toLowerCase();
        nombreProfesor = nombreProfesor.normalize("NFD").replace(/[\u0300-\u036f]/g, ""); // Eliminar acentos del nombre
        if (nombreProfesor.indexOf(filtro) > -1) {
            filas[i].style.display = ''; // Mostrar la fila si coincide
        } else {
            filas[i].style.display = 'none'; // Ocultar la fila si no coincide
        }
    }
}





  