// Variables globales
let modal, modalEditarAvatar, btnCerrarAvatar, btnCerrar, spanCampo, modalPassword;
let btnCerrarPassword, modalError, btnCerrarError, btnCerrarEditAdmin, modalEditarUsuario;
let modalAdmin, modalAdminEditarAvatar, modalAdminPassword, modalAdminEditarRol;
let btnAdminCerrarAvatar, btnAdminCerrar, btnAdminCerrarPassword, btnCerrarRol;
let inputsId, modalEliminarusuario;

window.onload = function(){
    // Obtenemos los elementos
    modal = document.getElementById("modalEditar");
    modalEditarAvatar = document.getElementById("modalEditarAvatar");
    btnCerrarAvatar = document.querySelector(".cerrar-modal-avatar");
    btnCerrar = document.querySelector(".cerrar-modal");
    spanCampo = document.getElementById("campo-a-editar");
    modalPassword = document.getElementById("modalEditarPassword");
    btnCerrarPassword = document.querySelector(".cerrar-modal-pass");
    modalError = document.getElementById("modalError");
    btnCerrarError = document.querySelector(".cerrar-modal-error");

    btnCerrarEditAdmin = document.querySelector(".cerrar-modal-edit-admin");
    modalEditarUsuario = document.getElementById("contenedor-centro-edit-admin");
    modalAdmin = document.getElementById("modalAdminEditar");
    modalAdminEditarAvatar = document.getElementById("modalAdminEditarAvatar");
    modalAdminPassword = document.getElementById("modalAdminEditarPassword");
    modalAdminEditarRol = document.getElementById("modalAdminEditarRol");
    btnAdminCerrarAvatar = document.querySelector(".cerrar-modal-avatar");
    btnAdminCerrar = document.querySelector(".cerrar-modal");
    btnAdminCerrarPassword = document.querySelector(".cerrar-modal-pass");
    btnCerrarRol = document.querySelector(".cerrar-modal-rol");

    inputsId = document.querySelectorAll('.input-id-usuario');

    modalEliminarusuario = document.getElementById("modalAdminEliminarusuario");

    // Cerrar si el usuario hace clic fuera de la ventana blanca
    window.addEventListener('click', function(event) {

        const esZonaSegura = event.target.closest('.modal-contenido') ||
                            event.target.closest('.perfil-container-edit-admin');

        if (esZonaSegura) {
            return;
        }

        // Cerrar modales
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

        if (event.target == modalAdmin){
            document.getElementById("nuevo-valor").value = "";
            modalAdmin.style.display = "none";
        }
        if (event.target == modalAdminEditarRol){
            document.getElementById('select-rol-usuario').value = "";
            modalAdminEditarRol.style.display = "none";
        }
        if (event.target == modalAdminEditarAvatar){
            document.getElementById("avatar-nuevo").value = "";
            modalAdminEditarAvatar.style.display = "none";
        }
        if (event.target == modalAdminPassword) {
            document.getElementById("usuario-reset-contrasena").innerText = "";
            modalAdminPassword.style.display = "none";
        }

        if (event.target == modalError) {
            modalError.style.display = "none";
        }
        if (event.target == modalEditarUsuario) {
            id_a_editar = null;
            inputsId.forEach(input => {
                input.value = "";
            });

            modalEditarUsuario.style.display = "none";
        }
        if (event.target == modalEliminarusuario) {
            document.getElementById("span-nombre-usuario").innerText = null;
            document.getElementById("input-id-eliminar").value = null;
            modalEliminarusuario.style.display = "none";
        }
    });

    // Eventos para cerrar al hacer clic en las X
    if (!modalEditarUsuario && !modalEliminarusuario) {
        btnCerrarAvatar.addEventListener('click', function() {
            document.getElementById("avatar-nuevo").value = "";
            modalEditarAvatar.style.display = "none";
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
    }
    else if(modalEliminarusuario){
        document.querySelector(".cerrar-modal-del").addEventListener('click', function() {
            document.getElementById("span-nombre-usuario").innerText = null;
            document.getElementById("input-id-eliminar").value = null;
            modalEliminarusuario.style.display = "none";
        });
    }
    else{
        btnCerrarAvatar.addEventListener('click', function() {
            document.getElementById("avatar-nuevo").value = "";
            modalAdminEditarAvatar.style.display = "none";
        });
        btnCerrar.addEventListener('click', function() {
            document.getElementById("nuevo-valor").value = "";
            modalAdmin.style.display = "none";
        });
        btnCerrarPassword.addEventListener('click', function() {
            document.getElementById("usuario-reset-contrasena").innerText = "";
            modalAdminPassword.style.display = "none";
        });
        btnCerrarRol.addEventListener('click', function() {
            document.getElementById('select-rol-usuario').value = "";
            modalAdminEditarRol.style.display = "none";
        });
    }
    // Solo si el botón existe
    if (btnCerrarError) {
        btnCerrarError.addEventListener('click', function() {
            modalError.style.display = "none";
        });
    }

    // Opciones de Admin
    var id_a_editar;

    if (btnCerrarEditAdmin) {
        btnCerrarEditAdmin.addEventListener('click', function() {
            // Añadir Vaciar valores de los campos
            document.getElementById('contenedor-centro-edit-admin').style.display = "none";
        });
    }
}

// Funciones para abrir el modal
function abrirModalAvatar() {
    modalEditarAvatar.style.display = "block";
}

function abrirModal(nombreCampo, valorBase64) {
    spanCampo.innerText = nombreCampo; // Cambia el título dinámicamente
    document.getElementById("label-nuevo-valor").innerText = nombreCampo + ": ";
    
    if(nombreCampo == "Usuario" || nombreCampo == "Email"){
        document.getElementById("nuevo-valor").required = true;
    } else {
        document.getElementById("nuevo-valor").required = false;
    }
    
    document.getElementById("campo-editar-dato").value = nombreCampo;

    if(nombreCampo == "Usuario"){
        document.getElementById("campo-editar-dato").autocomplete = "username";
    }

    // Decodificar valorBase64 y para no romper con caracteres especiales
    document.getElementById("nuevo-valor").value = decodeURIComponent(window.atob(valorBase64));
    modal.style.display = "block";
}

function abrirModalPassword() {
    modalPassword.style.display = "block";
}

function abrirModalEditarUsuario(id, usuario){
    id_a_editar = id;

    inputsId.forEach(input => {
        input.value = id;
    });

    document.getElementById('Logo-Usuario').src = "../../../img/perfiles/" + usuario['avatar'];
    document.getElementById('nombre-usuario').innerText = usuario['nombre_usuario'];
    document.getElementById('nombre-usuario-edit').innerText = usuario['nombre'];
    document.getElementById('apellidos-usuario-edit').innerText = usuario['apellidos'];
    document.getElementById('email-usuario-edit').innerText = usuario['email'];
    document.getElementById('rol-usuario-edit').innerText = usuario['rol'];

    document.getElementById('contenedor-centro-edit-admin').style.display = "flex";
}

function abrirModalAdminAvatar() {
    modalAdminEditarAvatar.style.display = "block";
}
function abrirModalAdmin(nombreCampo) {
    spanCampo.innerText = nombreCampo; // Cambia el título dinámicamente
    document.getElementById("label-nuevo-valor").innerText = nombreCampo + ": ";
    document.getElementById("campo-editar").value = nombreCampo;

    switch(nombreCampo){
        case "Usuario":
            document.getElementById("campo-editar").autocomplete = "username";
            document.getElementById("nuevo-valor").value = diccionario_usuarios[id_a_editar]['nombre_usuario'];
            break;
        case "Nombre":
            document.getElementById("nuevo-valor").value = diccionario_usuarios[id_a_editar]['nombre'];
            break;
        case "Apellidos":
            document.getElementById("nuevo-valor").value = diccionario_usuarios[id_a_editar]['apellidos'];
            break;
        case "Email":
            document.getElementById("nuevo-valor").value = diccionario_usuarios[id_a_editar]['email'];
            break;
        case "Rol":
            document.getElementById("nuevo-valor").value = diccionario_usuarios[id_a_editar]['rol'];
            break;
    }

    modalAdmin.style.display = "block";
}
function abrirModalAdminRol() {

    if (document.activeElement) { // Desbuguear el elemento con focus
        document.activeElement.blur();
    }
    const select = document.getElementById('select-rol-usuario');

    document.getElementById('select-rol-usuario').value = diccionario_usuarios[id_a_editar]['rol'];
    modalAdminEditarRol.style.display = "block";


    select.blur(); // Des seleccionar el desplegable
}
function abrirModalAdminPassword() {

    document.getElementById('usuario-reset-contrasena').innerText = diccionario_usuarios[id_a_editar]['nombre_usuario'];
    modalAdminPassword.style.display = "block";
}

function CerrarEliminarUsuario(){
    document.getElementById("span-nombre-usuario").innerText = null;
    document.getElementById("input-id-eliminar").value = null;
    document.getElementById("modalAdminEliminarusuario").style.display = "none";
}

function abrirConfirmacionDelete(id, usuario, usuarioLogueado){
    document.getElementById("span-nombre-usuario").innerText = usuario;
    document.getElementById("input-id-eliminar").value = id;

    if(usuarioLogueado == usuario){
        document.getElementById('advertencia-propio-usuario').style.display = 'block';
    }
    else{
        document.getElementById('advertencia-propio-usuario').style.display = 'none';
    }

    document.getElementById("modalAdminEliminarusuario").style.display = "block";
}


// ----------------------------------- Funciones AJAX -----------------------------------

let alertaTimer = null; // Timer de alerta Bootstrap

// Funcion AJAX para editar datos
function enviarDatosFormulario(event) {
    event.preventDefault(); // Evita que la página se recargue
    
    const formulario = event.target;
    const formData = new FormData(formulario); // Captura todos los inputs automáticamente

    switch(formData.get("campo-editar")){
        
        case "Avatar":
            fetch('apoyo/editar_avatar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {// Validamos la respuesta AJAX
                if (!response.ok) throw new Error('Error en la red');
                return response.json(); // Si todo OK, lanzamos respuesta json al siguiente paso
            })
            .then(data => { // Operamos con datos json
                
                // 1. Cerrar el modal
                modalEditarAvatar.style.display = "none";

                // 2. Refrescar la parte del perfil que cambió y lanzar mensaje:
                if (data['error_editar_perfil'] === "Ninguno" && data['cambio'] === "Avatar") {
                    const elementoDato = document.getElementById('Logo-Usuario');
                    elementoDato.src = "../../../img/perfiles/" + data['nuevo_valor'];
                }

                timerAlertasBootstrap((data['error_editar_perfil'] === "Ninguno") ? (data['cambio'] === 'Password' ? 'Contraseña actualizada correctamente.' : data['cambio'] + ' actualizado correctamente.') : data['error_editar_perfil'], (data['error_editar_perfil'] === "Ninguno") ? 'success' : 'danger');
                
            })
            .catch(error => {
                
                console.error('Error:', error);
                //alert('Ocurrió un error al guardar los datos');
            });
            break;
        
        case "Password":
            fetch('apoyo/editar_password.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {// Validamos la respuesta AJAX
                if (!response.ok) throw new Error('Error en la red');
                return response.json(); // Si todo OK, lanzamos respuesta json al siguiente paso
            })
            .then(data => { // Operamos con datos json
                
                // 1. Cerrar el modal
                document.getElementById("contrasena").value = "";
                document.getElementById("nueva-contrasena").value = "";
                document.getElementById("confirmar-contrasena").value = "";
                modalPassword.style.display = "none";

                // 2. No hay que refrescar nada, porque Password no se muestra en el perfil
                console.log(data);
                timerAlertasBootstrap((data['error_editar_perfil'] === "Ninguno") ? (data['cambio'] === 'Password' ? 'Contraseña actualizada correctamente.' : data['cambio'] + ' actualizado correctamente.') : data['error_editar_perfil'], (data['error_editar_perfil'] === "Ninguno") ? 'success' : 'danger');
                
            })
            .catch(error => {
                
                console.error('Error:', error);
                //alert('Ocurrió un error al guardar los datos');
            });
            break;
        
        default:
            fetch('apoyo/editar_dato.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {// Validamos la respuesta AJAX
                if (!response.ok) throw new Error('Error en la red');
                return response.json(); // Si todo OK, lanzamos respuesta json al siguiente paso
            })
            .then(data => { // Operamos con datos json
                
                // 1. Cerrar el modal y limpiar el campo
                document.getElementById("nuevo-valor").value = "";
                modal.style.display = "none";
                if(data['error_editar_perfil'] === "Ninguno"){
                    document.getElementById(`btn-editar-${data['cambio'].toLowerCase()}`).onclick = function() { abrirModal(data['cambio'], btoa(data['nuevo_valor'])); };
                }

                // 2. Refrescar la parte del perfil que cambió y lanzar mensaje:
                if (data['error_editar_perfil'] === "Ninguno" && data['cambio'] !== "Password" && data['cambio'] !== "Avatar") {
                    const elementoDato = document.getElementById(`${data['cambio'].toLowerCase()}-perfil-usuario`);
                    elementoDato.innerText = data['nuevo_valor'];
                }
                else if(data['error_editar_perfil'] === "Ninguno" && data['cambio'] === "Avatar"){
                    const elementoAvatar = document.getElementById(`Logo-Usuario`);
                    elementoAvatar.src = "../../../img/perfiles/" + data['nuevo_valor'];
                }

                timerAlertasBootstrap((data['error_editar_perfil'] === "Ninguno") ? (data['cambio'] === 'Password' ? 'Contraseña actualizada correctamente.' : data['cambio'] + ' actualizado correctamente.') : data['error_editar_perfil'], (data['error_editar_perfil'] === "Ninguno") ? 'success' : 'danger');
                
            })
            .catch(error => {
                
                console.error('Error:', error);
                //alert('Ocurrió un error al guardar los datos');
            });
            break;
    }               
}

function cerrarAlertaBootstrap(){
    const alerta = document.getElementById('alerta-perfil');
    alerta.classList.remove('show');
    alerta.classList.add('d-none');
    clearTimeout(alertaTimer);
}

// Establece el timer de 10 segundos para cerrar la alerta de Bootstrap
function timerAlertasBootstrap(mensaje, tipo = 'success') {
    const alerta = document.getElementById('alerta-perfil');
    if (!alerta.classList.contains('d-none')) {
        clearTimeout(alertaTimer);
    }
    const texto = document.getElementById('alerta-mensaje');
    
    // 1. Configurar mensaje y colores
    texto.innerHTML = `<strong>${mensaje}</strong>`;
    alerta.className = `alert alert-${tipo} alert-dismissible fade show mx-auto mb-3`; // Aplicamos clases de Bootstrap
    alerta.classList.remove('d-none'); // La hacemos visible

    // 2. Auto-cierre tras 10 segundos
    alertaTimer = setTimeout(() => {
        // Usamos el objeto de Bootstrap para cerrarla con animación
        const bsAlert = bootstrap.Alert.getOrCreateInstance(alerta);
        if (alerta.classList.contains('show')) {
            alerta.classList.remove('show');
            alerta.classList.add('d-none')
        }
    }, 10000);
}

// ----------------------------------- Funciones AJAX -----------------------------------