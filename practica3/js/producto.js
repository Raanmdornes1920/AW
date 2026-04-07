function recalcular() {
    const baseInput = document.getElementById('p_base');
    const ivaSelect = document.getElementById('p_iva');
    const finalDisplay = document.getElementById('p_final');

    if (baseInput && ivaSelect && finalDisplay) {
        let base = parseFloat(baseInput.value) || 0;
        let iva = parseInt(ivaSelect.value) || 0;
        let total = base + (base * (iva / 100));
        finalDisplay.innerText = total.toFixed(2) + " €";
    }
}

let indexActual = 0;

function cambiarImagen(n) {
    const imagenes = document.querySelectorAll('.img-carrusel');
    if (imagenes.length <= 1) return;

    imagenes[indexActual].classList.remove('active');
    
    indexActual += n;
    if (indexActual >= imagenes.length) indexActual = 0;
    if (indexActual < 0) indexActual = imagenes.length - 1;
    
    imagenes[indexActual].classList.add('active');
}

function modificarCantidad(n) {
    const input = document.getElementById('cantidad');
    if (input) {
        let valor = parseInt(input.value) + n;
        if (valor >= 1) {
            input.value = valor;
        }
    }
}

window.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('p_base')) {
        recalcular();
    }
});