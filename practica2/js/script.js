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