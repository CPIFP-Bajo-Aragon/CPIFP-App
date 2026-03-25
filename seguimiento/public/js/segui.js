const btnMostrarSeguimiento = document.querySelector( '#btnMostrarSeguimiento' )
const informe       = document.querySelector( '#informe' )
const informeWeb    = document.querySelector( '#informeWeb' )
const btnCrearPDF   = document.querySelector( '#btnCrearPDF' )
const btnCrearXLSX  = document.querySelector( '#btnCrearXLSX' )
const datosProfesor = document.querySelector( '#datosProfesor' )
const tablaEP1      = document.querySelector( '#EP1' )
const tablaEP2      = document.querySelector( '#EP2' )
const tablaAA       = document.querySelector( '#AA' )
const tablaHI       = document.querySelector( '#HI' )
const tablaAP       = document.querySelector( '#AP' )
const tablaAT       = document.querySelector( '#AT' )
//Guardamos las tablas en un vector
const tablas        = [ datosProfesor, tablaEP1, tablaEP2, tablaAA, tablaHI, tablaAP, tablaAT ]

const crearXLSX = () => {

  let datos;

  for (let i = 0; i < tablas.length; i++) {
    //La primera vez crea la variable datos, las siguientes añade tablas a los datos
    ( i == 0 ) ? datos =  XLSX.utils.aoa_to_sheet( [ [ "" ] ] ) : XLSX.utils.sheet_add_aoa( datos, [ [ "" ] ], { origin: -1 } )
    XLSX.utils.sheet_add_dom( datos, tablas[ i ], { origin: -1 } )    
  }
  /* Crear libro y exportar */
  const libro = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet( libro, datos, 'Seguimiento' )
  XLSX.writeFile( libro, 'Seguimiento.xlsx' )
}


btnMostrarSeguimiento.addEventListener( 'click', () => {
    if( informe.hasAttribute( 'hidden' )){
        informe.removeAttribute( 'hidden' )
    } else {
        informe.setAttribute( 'hidden','hidden' )
    }
    
})
//Función que crea html2pdf
btnCrearPDF.addEventListener( 'click', () => {
    const opt = {
        margin: 10,
        filename: 'seguimiento.pdf',
    }
    html2pdf().set( opt ).from( informeWeb ).save()
})

//Función que guarda el XLSX
btnCrearXLSX.addEventListener( 'click', crearXLSX )

