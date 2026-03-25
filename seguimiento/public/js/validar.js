
var errorCorreo = "";


function correo(n) {
    var cad = "";
    let re = new RegExp('[\\w]*[@]{1}[\\w]*[.]{1}[\\w]*');
    var correo = document.getElementById(n).value;

    if (re.test(correo)) {
        cad = "";
        document.getElementById("errorMail").innerHTML = cad;
        errorCorreo = "";
        comprobarCorreo = true;
        return true;
    } else {
        cad = "Correo con formato incorrecto";
        document.getElementById("errorMail").innerHTML = cad;
        errorCorreo = " ¡Correo con formato incorrecto! ";
        comprobarCorreo = false;
        return false;
    }

}

function Solo_Texto(e) {
    var code;
    if (!e) var e = window.event;
    if (e.keyCode) code = e.keyCode;
    else if (e.which) code = e.which;
    var character = String.fromCharCode(code);
    var AllowRegex = /^[\ba-zA-Z\s-]$/;
    if (AllowRegex.test(character)) return true;
    return false;
}



