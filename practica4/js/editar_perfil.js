window.onload = function(){
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

    const btnCerrarEditAdmin = document.querySelector(".cerrar-modal-edit-admin");
    const modalEditarUsuario = document.getElementById("contenedor-centro-edit-admin");
    const modalAdmin = document.getElementById("modalAdminEditar");
    const modalAdminEditarAvatar = document.getElementById("modalAdminEditarAvatar");
    const modalAdminPassword = document.getElementById("modalAdminEditarPassword");
    const modalAdminEditarRol = document.getElementById("modalAdminEditarRol");
    const btnAdminCerrarAvatar = document.querySelector(".cerrar-modal-avatar");
    const btnAdminCerrar = document.querySelector(".cerrar-modal");
    const btnAdminCerrarPassword = document.querySelector(".cerrar-modal-pass");
    const btnCerrarRol = document.querySelector(".cerrar-modal-rol");

    const inputsId = document.querySelectorAll('.input-id-usuario');

    const modalEliminarusuario = document.getElementById("modalAdminEliminarusuario");

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
    document.getElementById("campo-editar").value = nombreCampo;
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

