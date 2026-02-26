// Obtenemos los elementos
const modal = document.getElementById("modalEditar");
const modalEditarAvatar = document.getElementById("modalEditarAvatar");
const btnCerrarAvatar = document.querySelector(".cerrar-modal-avatar");
const btnCerrar = document.querySelector(".cerrar-modal");
const spanCampo = document.getElementById("campo-a-editar");
const modalPassword = document.getElementById("modalEditarPassword");
const btnCerrarPassword = document.querySelector(".cerrar-modal-pass");
const modalError = document.getElementById("modalError");
const btnCerrarError = document.querySelector(".cerrar-modal-error");

// Funciones para abrir el modal
function abrirModalAvatar() {
    modalEditarAvatar.style.display = "block";
}
function abrirModal(nombreCampo, valorBase64) {
    spanCampo.innerText = nombreCampo; // Cambia el título dinámicamente
    document.getElementById("label-nuevo-valor").innerText = nombreCampo + ": ";
    document.getElementById("campo-editar").value = nombreCampo;
    // Decodificar valorBase64 y para no romper con caracteres especiales
    document.getElementById("nuevo-valor").value = decodeURIComponent(window.atob(valorBase64)); 
    modal.style.display = "block";
    
}
function abrirModalPassword() {
    modalPassword.style.display = "block";
}

// Cerrar si el usuario hace clic fuera de la ventana blanca
window.addEventListener('click', function(event) {
    if (event.target == modalEditarAvatar) {
        document.getElementById("avatar-nuevo").value = "";
        modalEditarAvatar.style.display = "none";
    }
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
btnCerrarAvatar.addEventListener('click', function() {
    document.getElementById("avatar-nuevo").value = "";
    modalEditarAvatar.style.display = "none";
});
btnCerrar.addEventListener('click', function() {
    document.getElementById("nuevo-valor").value = "";
    modal.style.display = "none";
});
btnCerrar.addEventListener('click', function() {
    document.getElementById("nuevo-valor").value = "";
    modal.style.display = "none";
});
btnCerrarPassword.addEventListener('click', function() {
    document.getElementById("contrasena").value = "";
    document.getElementById("nueva-contrasena").value = "";
    document.getElementById("confirmar-contrasena").value = "";
    modalPassword.style.display = "none";
});
// Solo si el botón existe
if (btnCerrarError) {
    btnCerrarError.addEventListener('click', function() {
        modalError.style.display = "none";
    });
}