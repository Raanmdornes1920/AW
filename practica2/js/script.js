// Mostrar dropdown menu
function toggleMenu() {
    document.getElementById("menuDesplegable").classList.toggle("show");
}

// Ocultar dropdown menu
window.onclick = function(event) {
    if (!event.target.matches('.avatar')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}

function editarUsername() {
    alert("Función de edición de username no implementada aún.");
}

function editarNombre() {
    alert("Función de edición de nombre no implementada aún.");
}

function editarApellidos() {
    alert("Función de edición de apellidos no implementada aún.");
}

function editarEmail() {
    alert("Función de edición de email no implementada aún.");
}

function editarPassword() {
    alert("Función de edición de password no implementada aún.");
}


// Obtenemos los elementos
const modal = document.getElementById("modalEditar");
const btnCerrar = document.querySelector(".cerrar-modal");
const spanCampo = document.getElementById("campo-a-editar");
const modalPassword = document.getElementById("modalEditarPassword");
const btnCerrarPassword = document.querySelector(".cerrar-modal-pass");

// Función para abrir el modal (llámala desde tus botones de lápiz)
function abrirModal(nombreCampo) {
    spanCampo.innerText = nombreCampo; // Cambia el título dinámicamente
    modal.style.display = "block";
}

function abrirModalPassword() {
    modalPassword.style.display = "block";
}

// Evento para cerrar al hacer clic en la X
btnCerrar.onclick = function() {
    modal.style.display = "none";
}

btnCerrarPassword.onclick = function() {
    modalPassword.style.display = "none";
}

// Cerrar si el usuario hace clic fuera de la ventana blanca
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
    if (event.target == modalPassword) {
        modalPassword.style.display = "none";
    }
}

guardarCambios = function() {
    alert("Nuevo valor: " + nuevoValor);
}

getNombreEditar = function() {
    var texto = spanCampo.innerText;
    if (texto === "Editar Usuario") {
        return "username";
    } else if (texto === "Editar Nombre") {
        return "nombre";
    } else if (texto === "Editar Apellidos") {
        return "apellidos";
    } else if (texto === "Editar Email") {
        return "email";
    }
    return "error";
}