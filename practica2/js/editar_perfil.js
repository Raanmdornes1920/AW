// Obtenemos los elementos
const modal = document.getElementById("modalEditar");
const btnCerrar = document.querySelector(".cerrar-modal");
const spanCampo = document.getElementById("campo-a-editar");
const modalPassword = document.getElementById("modalEditarPassword");
const btnCerrarPassword = document.querySelector(".cerrar-modal-pass");
const modalError = document.getElementById("modalError");
const btnCerrarError = document.querySelector(".cerrar-modal-error");

// Funciones para abrir el modal
function abrirModal(nombreCampo, valorCampo) {
    spanCampo.innerText = nombreCampo; // Cambia el título dinámicamente
    document.getElementById("label-nuevo-valor").innerText = nombreCampo + ": ";
    document.getElementById("campo-editar").value = nombreCampo;
    document.getElementById("nuevo-valor").value = valorCampo;
    modal.style.display = "block";
    
}
function abrirModalPassword() {
    modalPassword.style.display = "block";
}

// Cerrar si el usuario hace clic fuera de la ventana blanca
window.addEventListener('click', function(event) {
    if (event.target == modal) {
        document.getElementById("nuevo-valor").value = "";
        modal.style.display = "none";
    }
    if (event.target == modalPassword) {
        document.getElementById("contrasena").value = "";
        document.getElementById("nueva-contrasena").value = "";
        document.getElementById("confirmar-contrasena").value = "";
        modalPassword.style.display = "none";
    }
    if (event.target == modalError) {
        modalError.style.display = "none";
    }
});

// Eventos para cerrar al hacer clic en las X
btnCerrar.addEventListener('click', function() {
    document.getElementById("nuevo-valor").value = "";
    modal.style.display = "none";
});
btnCerrarError.addEventListener('click', function() {
    modalError.style.display = "none";
});
btnCerrarPassword.addEventListener('click', function() {
    document.getElementById("contrasena").value = "";
    document.getElementById("nueva-contrasena").value = "";
    document.getElementById("confirmar-contrasena").value = "";
    modalPassword.style.display = "none";
});

getNombreEditar = function() {
    var texto = spanCampo.innerText;
    if (texto === "Editar Usuario") {
        return "usuario";
    } else if (texto === "Editar Nombre") {
        return "nombre";
    } else if (texto === "Editar Apellidos") {
        return "apellidos";
    } else if (texto === "Editar Email") {
        return "email";
    }
    return "error";
}

