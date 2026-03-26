/**
 * encuestas.js  –  JS propio del módulo de encuestas
 */

// ── Contador de respuestas respondidas en el formulario público ──
document.addEventListener('DOMContentLoaded', function(){

    const tarjetas = document.querySelectorAll('.pregunta-card');
    if(!tarjetas.length) return;

    // Inicializar contador
    actualizarContador();

    document.querySelectorAll('.btn-check').forEach(function(radio){
        radio.addEventListener('change', function(){
            const id = this.name.match(/\[(\d+)\]/)?.[1];
            if(id){
                const card = document.getElementById('preg_' + id);
                if(card) card.classList.add('respondida');
            }
            actualizarContador();
        });
    });

    function actualizarContador(){
        const total      = tarjetas.length;
        const respondidas = document.querySelectorAll('.pregunta-card.respondida').length;
        const contador    = document.getElementById('contador-respondidas');
        if(contador){
            contador.textContent = respondidas + ' / ' + total;
            contador.style.color = (respondidas === total) ? '#27ae60' : '#e67e22';
        }
    }

    // Validación del formulario antes de enviar
    const form = document.getElementById('form-encuesta');
    if(form){
        form.addEventListener('submit', function(e){
            const sin_responder = [];
            tarjetas.forEach(function(card){
                if(!card.classList.contains('respondida')){
                    sin_responder.push(card);
                    card.style.border = '2px solid #e74c3c';
                    card.scrollIntoView({behavior:'smooth', block:'center'});
                }
            });
            if(sin_responder.length){
                e.preventDefault();
                alert('Por favor, responde a todas las preguntas antes de enviar.');
                sin_responder[0].scrollIntoView({behavior:'smooth', block:'center'});
            }
        });
    }
});
