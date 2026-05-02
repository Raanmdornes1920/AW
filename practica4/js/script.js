// Mostrar dropdown menu
function toggleMenu() {
    const dropdown = document.getElementById("menuDesplegable");
    if (dropdown) {
        dropdown.classList.toggle("show");
    }
}

function passwordMatch(element) {
    if(document.getElementById('password').value != element.value) {
            element.setCustomValidity('Las contraseñas no coinciden');
    } else {
            element.setCustomValidity('');
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

    // TODO: Ajax para añadir producto al carrito
    const formsIncluirCarrito = document.querySelectorAll('.form-incluir-carrito');
    const formDetalleProducto = document.getElementById('form-detalle-producto');

    if(formsIncluirCarrito) {
        formsIncluirCarrito.forEach(form => {
            form.addEventListener('submit', function(event) {
                actualizarNumItemsCarrito(event);
            });
        });
    }

    if(formDetalleProducto) {
        formDetalleProducto.addEventListener('submit', function(event) {
            actualizarNumItemsCarrito(event);
        });
    }
});

function actualizarNumItemsCarrito(event) {
    event.preventDefault();

    const formulario = event.target;
    const formData = new FormData(formulario); // Captura todos los inputs automáticamente
    
    fetch('../pedidos/apoyo/procesar_carrito.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {// Validamos la respuesta AJAX
        if (!response.ok) throw new Error('Error en la red');
        return response.json(); // Si todo OK, lanzamos respuesta json al siguiente paso
    })
    .then(data => { // Operamos con datos json
        // Actualizamos el número de elementos en el carrito
        const numItemsCarrito = document.getElementById('num-items-carrito');
        const liCarrito = document.getElementById('li-carrito');
        if (data.carrito > 0) {
            numItemsCarrito.classList.remove('d-none');
            numItemsCarrito.innerText = data.carrito;
            liCarrito.innerHTML = `<i class="bi bi-cart3"></i> Mi Carrito ${(data.carrito > 0) ? "("+data.carrito+")" : ""}`;
        } else {
            numItemsCarrito.classList.add('d-none');
        }
    });
}