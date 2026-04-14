// Mostrar dropdown menu
function toggleMenu() {
    const dropdown = document.getElementById("menuDesplegable");
    if (dropdown) {
        dropdown.classList.toggle("show");
    }
}

window.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const menu = document.getElementById('menu');
    const avatarContainer = document.querySelector('.avatar-container');
    const dropdown = document.getElementById('menuDesplegable');

    if (menuToggle && menu) {
        menuToggle.addEventListener('click', function(event) {
            event.stopPropagation();
            menu.classList.toggle('show');
            if (dropdown) {
                dropdown.classList.remove('show');
            }
        });
    }

    if (avatarContainer) {
        avatarContainer.addEventListener('click', function(event) {
            event.stopPropagation();
            if (dropdown) {
                dropdown.classList.toggle('show');
            }
            if (menu) {
                menu.classList.remove('show');
            }
        });
    }

    // Ocultar dropdowns cuando se hace clic fuera
    document.addEventListener('click', function(event) {
        if (dropdown && !event.target.closest('.avatar-container')) {
            dropdown.classList.remove('show');
        }
        if (menu && !event.target.closest('#menu') && !event.target.closest('#menu-toggle')) {
            menu.classList.remove('show');
        }
    });

    // Si existen las opciones de avatar, añadimos funcion de mostrar/ocultar al hacer click
    if(document.querySelector('.opcion-avatar')){
        document.querySelectorAll('.opcion-avatar').forEach(opcion => {
            opcion.addEventListener('click', function() {
                if (opcion.querySelector('#radio-custom')) {
                    document.getElementById('archivo-avatar').style.display = 'block';
                } else {
                    document.getElementById('archivo-avatar').style.display = 'none';
                }
            });
        });
    }
});
