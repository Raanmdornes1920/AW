// Mostrar dropdown menu
function toggleMenu() {
    document.getElementById("menuDesplegable").classList.toggle("show");
}

// Ocultar dropdown menu
window.addEventListener('click', function(event) {
    if (!event.target.matches('.avatar')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
});

// Si existen las opciones de avatar, añadimos funcion de mosrar/ocultar al hacer click
if(document.querySelector(".opcion-avatar")){
    
    document.querySelectorAll(".opcion-avatar").forEach(opcion => {
        
        opcion.addEventListener('click', function() {
            
            if (opcion.querySelector("#radio-custom")) {
                document.getElementById("archivo-avatar").style.display = "block";
            } else {
                document.getElementById("archivo-avatar").style.display = "none";
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    function toggleMenu() {
        const menu = document.getElementById('menu');
        menu.classList.toggle('show');
    }

    // Vincular el evento al botón
    const menuToggle = document.querySelector('.menu-toggle');
    menuToggle.addEventListener('click', toggleMenu);
});