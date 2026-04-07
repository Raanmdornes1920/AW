// 1. Validar tarjeta simulada ( 2.5)
function validarTarjeta(event) {
    const tarjeta = document.querySelector('input[placeholder="1234 5678 9101 1121"]').value;
    const regex = /^\d{16}$/;
    if (!regex.test(tarjeta)) {
        alert("El número de tarjeta debe tener exactamente 16 dígitos.");
        event.preventDefault();
        return false;
    }
    return true;
}

// 2. Confirmación de cancelación ( 2.4)
function confirmarCancelacion() {
    return confirm("¿Estás seguro de que deseas cancelar el pedido? Se vaciará el carrito.");
}

// 3. Recalcular subtotal en el carrito sin recargar 
function actualizarSubtotal(idProducto, precio) {
    const cantidad = document.getElementById(`cant_${idProducto}`).value;
    const subtotalElemento = document.getElementById(`sub_${idProducto}`);
    const nuevoSubtotal = (cantidad * precio).toFixed(2);
    subtotalElemento.innerText = nuevoSubtotal + " €";
}