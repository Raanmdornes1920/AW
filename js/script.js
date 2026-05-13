// Mostrar dropdown menu
function toggleMenu() {
    const dropdown = document.getElementById("menuDesplegable");
    if (dropdown) {
        dropdown.classList.toggle("show");
    }
}

function passwordMatch(element) {
    if (document.getElementById('password').value != element.value) {
        element.setCustomValidity('Las contraseñas no coinciden');
    } else {
        element.setCustomValidity('');
    }
}

window.addEventListener('DOMContentLoaded', function () {
    const menuToggle = document.getElementById('menu-toggle');
    const menu = document.getElementById('menu');
    const avatarContainer = document.querySelector('.avatar-container');
    const dropdown = document.getElementById('menuDesplegable');

    if (menuToggle && menu) {
        menuToggle.addEventListener('click', function (event) {
            event.stopPropagation();
            menu.classList.toggle('show');
            if (dropdown) {
                dropdown.classList.remove('show');
            }
        });
    }

    if (avatarContainer) {
        avatarContainer.addEventListener('click', function (event) {
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
    document.addEventListener('click', function (event) {
        if (dropdown && !event.target.closest('.avatar-container')) {
            dropdown.classList.remove('show');
        }
        if (menu && !event.target.closest('#menu') && !event.target.closest('#menu-toggle')) {
            menu.classList.remove('show');
        }
    });

    // Si existen las opciones de avatar, añadimos funcion de mostrar/ocultar al hacer click
    if (document.querySelector('.opcion-avatar')) {
        document.querySelectorAll('.opcion-avatar').forEach(opcion => {
            opcion.addEventListener('click', function () {
                if (opcion.querySelector('#radio-custom')) {
                    document.getElementById('archivo-avatar').style.display = 'block';
                } else {
                    document.getElementById('archivo-avatar').style.display = 'none';
                }
            });
        });
    }

    // APLICACIÓN DEL CARrito con AJAX
    // 1. Buscamos TODOS los formularios que tengan nuestra clase especial
    const formulariosCarrito = document.querySelectorAll(".form-ajax-carrito");

    // 2. A cada uno de esos formularios, le añadimos un "vigilante"
    formulariosCarrito.forEach(form => {
        form.addEventListener("submit", function (e) {

            // 3. Bloqueamos el envío tradicional del formulario
            // Así evitamos que la página se recargue
            e.preventDefault();

            // 4. Recogemos todos los datos que tiene el formulario (el id del producto, la cantidad, etc.)
            const formData = new FormData(this);

            // 5. Le inyectamos nuestra "etiqueta secreta" ajax=1 que programamos antes en PHP
            formData.append("ajax", "1");

            // 6. Enviamos los datos al servidor en la sombra usando fetch()
            fetch(this.action, {
                method: "POST",
                body: formData
            })
                // 7. Cuando el servidor responda, le decimos que lea el JSON que creamos en PHP
                .then(response => response.json())
                .then(data => {

                    // 8. Si PHP nos responde que todo fue 'success'
                    if (data.status === "success") {

                        // Cambiamos el color del botón un segundito
                        // para que el cliente sepa que se ha añadido
                        const boton = this.querySelector('button[type="submit"]');
                        const textoOriginal = boton.innerText;
                        boton.innerText = "¡Añadido!";
                        boton.classList.replace("btn-primary", "btn-success");

                        setTimeout(() => {
                            boton.innerText = textoOriginal;
                            boton.classList.replace("btn-success", "btn-primary");
                        }, 1500);

                        // Actualizar el contador del carrito en la barra de navegación
                        if (data.num_items_carrito !== undefined) {
                            const badge = document.getElementById("contador-carrito-badge");
                            const texto = document.getElementById("contador-carrito-texto");
                            
                            if (badge) {
                                badge.innerText = data.num_items_carrito;
                                badge.classList.remove("d-none");
                            }
                            if (texto) {
                                texto.innerText = "(" + data.num_items_carrito + ")";
                            }
                        }

                        // 9. Comprobamos el veces_aplicable que nos ha mandado PHP
                        // y modificamos el banner directamente si ya hemos cumplido la oferta
                        const bannerContenedor = document.getElementById("contenedor-banner-oferta");

                        if (bannerContenedor && data.veces_aplicable > 0) {
                            // Cambiamos el contenido del div por el banner de éxito verde
                            bannerContenedor.innerHTML = `
                            <div class="alert alert-success d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
                                <div>
                                    <strong>¡Requisitos cumplidos!</strong> Puedes aplicar esta oferta a tu pedido actual.
                                    (Aplicable ${data.veces_aplicable} veces).
                                </div>
                                <form action="../pedidos/apoyo/procesar_carrito.php?id_oferta=${formData.get('id_oferta') || ''}" method="POST" class="mt-2 mt-md-0">
                                    <input type="hidden" name="accion" value="aplicar_oferta">
                                    <button type="submit" class="btn btn-success fw-bold">¡Aplicar Oferta Ahora!</button>
                                </form>
                            </div>
                        `;
                        }
                    }
                })
                .catch(error => {
                    console.error("Error en la petición AJAX:", error);
                    alert("Hubo un error al añadir el producto.");
                });
        });
    });


});
