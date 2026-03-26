
function paginar(paginaActual,numPaginas,ruta,cadenaGet ='',destino =''){
    let salida = ''

    salida += `
        <nav aria-label="...">
            <ul class="pagination justify-content-center">`
                if (paginaActual <= 0){
                    salida += ` 
                    <li class="page-item disabled">
                        <span class="page-link"><<</span>
                    </li>`
                } else {
                    salida += ` 
                    <li class="page-item">
                        <a class="page-link" href="${ruta}/${paginaActual-1}?${cadenaGet}"><<</a>
                    </li>`
                }
                
                countLeft = 0
                countRight = 0
                for(i=0; i < numPaginas;i++){
                    if(Math.abs(paginaActual - i) > 2 && i > 0 && i < numPaginas-1){    // ponemos puntos si la distancia a la pagina activa es mayor de 2 y no es la primera ni la ultima
                        if(countLeft == 0 && paginaActual - i > 0){       // por la izquierda de pagina activa y no se han puesto puntitos
                            salida += ` 
                                <li class="page-item">
                                    <span class="page-link">...</span>
                                </li>
                                `
                            countLeft ++
                        } 
                        if (countRight == 0 && paginaActual - i < 0) {
                            salida += ` 
                                <li class="page-item">
                                    <span class="page-link">...</span>
                                </li>
                                `
                            countRight ++
                        }
                        
                    } else {
                        if(i == paginaActual){
                            salida += ` 
                            <li class="page-item active">
                                <span class="page-link">${i+1}</span>
                            </li>
                            `
                        } else {
                            salida += ` 
                            <li class="page-item">
                                <a class="page-link" href="${ruta}/${i}?${cadenaGet}">${i+1}</a>
                            </li>
                            `
                        }
                    }
                }


                if (paginaActual+1 >= numPaginas){
                    salida += ` 
                    <li class="page-item disabled">
                        <span class="page-link">>></span>
                    </li>`
                } else {
                    salida += ` 
                    <li class="page-item">
                        <a class="page-link" href="${ruta}/${paginaActual+1}?${cadenaGet}">>></a>
                    </li>
                    `
                }
            salida += `
            </ul>
        </nav>
    `
    if (destino == ''){
        document.write(salida)
    }
    else {
        document.getElementById(destino).innerHTML = salida
    }
}
