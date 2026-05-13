/**
 * ofertas.js - Gestión dinámica del formulario de ofertas
 * Maneja el selector de productos, cálculo de descuentos en tiempo real
 */

// Variable global para almacenar los productos disponibles
var productosDisponibles = [];

/**
 * Inicialización: carga productos disponibles y, si estamos editando, carga los productos actuales
 */
document.addEventListener('DOMContentLoaded', function() {
    // Cargar productos disponibles del hidden input
    var jsonInput = document.getElementById('productosDisponiblesJSON');
    if (jsonInput && jsonInput.value) {
        try {
            productosDisponibles = JSON.parse(jsonInput.value);
        } catch (e) {
            console.error('Error parseando productos:', e);
        }
    }

    // Si estamos editando, cargar los productos actuales de la oferta
    var actualesInput = document.getElementById('productosActualesJSON');
    if (actualesInput && actualesInput.value) {
        try {
            var productosActuales = JSON.parse(actualesInput.value);
            productosActuales.forEach(function(p) {
                agregarProductoOferta(p.id_producto, p.cantidad);
            });
        } catch (e) {
            console.error('Error parseando productos actuales:', e);
        }
    }

    // Recalcular por si hay datos precargados
    recalcularPrecioPack();
    recalcularDescuentoOferta();
});

/**
 * Agrega una fila de producto al formulario de oferta
 * @param {number|null} idProductoPreseleccionado - ID del producto a preseleccionar (para edición)
 * @param {number|null} cantidadPreseleccionada - Cantidad a preseleccionar (para edición)
 */
function agregarProductoOferta(idProductoPreseleccionado, cantidadPreseleccionada) {
    var contenedor = document.getElementById('contenedor-productos-oferta');
    if (!contenedor) return;

    var idPre = idProductoPreseleccionado || '';
    var cantPre = cantidadPreseleccionada || 1;

    var fila = document.createElement('div');
    fila.className = 'fila-producto-oferta row g-2 align-items-end mb-2';

    // Crear select de productos
    var selectHtml = '<div class="col-12 col-md-7"><select name="prod_ids[]" onchange="recalcularPrecioPack()" class="form-select" required>';
    selectHtml += '<option value="">-- Selecciona producto --</option>';
    productosDisponibles.forEach(function(p) {
        var selected = (p.id == idPre) ? 'selected' : '';
        selectHtml += '<option value="' + p.id + '" data-precio="' + p.precio + '" ' + selected + '>' 
            + p.nombre + ' (' + p.precio.toFixed(2) + ' €)</option>';
    });
    selectHtml += '</select></div>';

    fila.innerHTML = selectHtml +
        '<div class="col-6 col-md-3"><input type="number" name="prod_cants[]" value="' + cantPre + '" min="1" ' +
        'class="form-control" ' +
        'oninput="recalcularPrecioPack()" placeholder="Cant." required></div>' +
        '<div class="col-6 col-md-2"><button type="button" onclick="eliminarFilaProducto(this)" ' +
        'class="btn btn-outline-danger w-100" title="Quitar producto">Quitar</button></div>';

    contenedor.appendChild(fila);
    recalcularPrecioPack();
}

/**
 * Elimina una fila de producto del formulario
 */
function eliminarFilaProducto(boton) {
    var fila = boton.parentElement;
    fila.remove();
    recalcularPrecioPack();
}

/**
 * Recalcula el precio total del pack sumando (precio_con_iva * cantidad) de cada producto seleccionado
 */
function recalcularPrecioPack() {
    var filas = document.querySelectorAll('.fila-producto-oferta');
    var total = 0;

    filas.forEach(function(fila) {
        var select = fila.querySelector('select[name="prod_ids[]"]');
        var inputCant = fila.querySelector('input[name="prod_cants[]"]');
        
        if (select && inputCant) {
            var opcionSeleccionada = select.options[select.selectedIndex];
            var precio = parseFloat(opcionSeleccionada.getAttribute('data-precio')) || 0;
            var cantidad = parseInt(inputCant.value) || 0;
            total += precio * cantidad;
        }
    });

    var spanPrecio = document.getElementById('precio_pack_sin_descuento');
    if (spanPrecio) {
        spanPrecio.textContent = total.toFixed(2) + ' €';
    }

    // Recalcular descuento con el nuevo precio del pack
    recalcularDescuentoOferta();
}

/**
 * Recalcula el porcentaje de descuento basándose en el precio final deseado por el gerente
 */
function recalcularDescuentoOferta() {
    var spanPrecio = document.getElementById('precio_pack_sin_descuento');
    var inputPrecioFinal = document.getElementById('precio_final_deseado');
    var spanPorcentaje = document.getElementById('porcentaje_descuento_calculado');
    var hiddenDescuento = document.getElementById('descuento_porcentaje_hidden');
    var spanAhorro = document.getElementById('ahorro_cliente');

    if (!spanPrecio || !inputPrecioFinal || !spanPorcentaje || !hiddenDescuento) return;

    var precioPackStr = spanPrecio.textContent.replace(' €', '').replace(',', '.');
    var precioPack = parseFloat(precioPackStr) || 0;
    var precioFinal = parseFloat(inputPrecioFinal.value) || 0;

    if (precioPack > 0 && precioFinal > 0 && precioFinal < precioPack) {
        var porcentaje = ((precioPack - precioFinal) / precioPack) * 100;
        var ahorro = precioPack - precioFinal;
        
        spanPorcentaje.textContent = porcentaje.toFixed(2) + '%';
        hiddenDescuento.value = porcentaje.toFixed(2);
        
        if (spanAhorro) {
            spanAhorro.textContent = ahorro.toFixed(2) + ' €';
        }

        // Color visual según el descuento
        if (porcentaje > 50) {
            spanPorcentaje.style.color = '#e74c3c'; // Rojo = descuento muy alto
        } else if (porcentaje > 25) {
            spanPorcentaje.style.color = '#f39c12'; // Naranja = descuento medio
        } else {
            spanPorcentaje.style.color = '#27ae60'; // Verde = descuento razonable
        }
    } else if (precioFinal >= precioPack && precioPack > 0) {
        spanPorcentaje.textContent = '0.00% (precio final >= precio pack)';
        spanPorcentaje.style.color = '#e74c3c';
        hiddenDescuento.value = '0';
        if (spanAhorro) spanAhorro.textContent = '0.00 €';
    } else {
        spanPorcentaje.textContent = '0.00%';
        spanPorcentaje.style.color = '#333';
        hiddenDescuento.value = '0';
        if (spanAhorro) spanAhorro.textContent = '0.00 €';
    }
}
