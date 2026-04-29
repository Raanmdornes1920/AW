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
<<<<<<< HEAD
    
    indexActual += n;
    if (indexActual >= imagenes.length) indexActual = 0;
    if (indexActual < 0) indexActual = imagenes.length - 1;
    
=======

    indexActual += n;
    if (indexActual >= imagenes.length) indexActual = 0;
    if (indexActual < 0) indexActual = imagenes.length - 1;

>>>>>>> angela
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

//Para la previsualizacion del carrusel de imagenes en el formulario de actualizar producto
function previsualizarImagenes(input) {
    const contenedor = document.getElementById('carrusel_previsualizacion');
    if (!contenedor) return;

    contenedor.innerHTML = ''; // Limpiar previsualización anterior
    indexActual = 0; // Resetear el índice del carrusel

    if (input.files && input.files.length > 0) {
        // Mostrar botones de navegación si hay más de una foto
        if (input.files.length > 1) {
            contenedor.innerHTML += `
                <button type="button" class="btn-carrusel prev" onclick="cambiarImagen(-1)">&#10094;</button>
                <button type="button" class="btn-carrusel next" onclick="cambiarImagen(1)">&#10095;</button>
            `;
        }

        Array.from(input.files).forEach((file, i) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-carrusel' + (i === 0 ? ' active' : '');
                contenedor.appendChild(img);
            }
            reader.readAsDataURL(file);
        });
<<<<<<< HEAD
        
=======

>>>>>>> angela
        contenedor.style.display = 'block'; // Asegurar que sea visible
    }
}

window.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('p_base')) {
        recalcular();
    }
});