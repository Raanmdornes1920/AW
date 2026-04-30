/*$(document).ready(function()
{
    $("#botonGetUsuariosTest").on("click", showUsersTest);

    $("#botonGetUsuarios").on("click", showUsers);
})

function showUsersTest()
{
    $.ajax({
        url: 'https://reqres.in/api/users?per_page=12',
        method: 'GET',
        headers: 
        {
            'x-api-key': 'reqres_aec19625519d45d58e805f33e24c9ad3'
        },
        success: function (response) 
        {
            $("#listaUsuariosTest").empty();
            
            var usersList = $("#listaUsuariosTest");

            $.each(response.data, function(index, element)
            {
                usersList.append(
                    '<div class="col-6 col-md-4 col-lg-3">'
                    +       '<p>' + element.first_name + '</p>'
                    +       '<img src="https://i.pravatar.cc/150?u=' + element.id + '">'
                    + '</div>'
                );
            });
        },
        error: function (err) 
        {
            console.error(err.responseText);
        }
    });
}

function showUsers()
{

    $.get('http://localhost/Ejemplo1V2/includes/Apis/userApi.php', {"action" : "getAllUsers" }, function(response)
    {
        var htmlTable = 
            '<table class="table table-bordered">'
          + '   <thead>'
          + '       <tr>'
          + '           <th>Id</th>'
          + '           <th>Usuario</th>'
          + '           <th>Nombre</th>'
          + '           <th>Acciones</th>'
          + '        </tr>'
          + '   </thead>'
          + '   <tbody>';

        $.each(response, function(index, element)
        {
            htmlTable += '<tr name="' + element.id + '">';

            htmlTable += '<td>' + element.id + '</td>';
            htmlTable += '<td>' + element.nombreUsuario + '</td>';
            htmlTable += '<td>' + element.nombre + '</td>';
            htmlTable += '<td>' 
                      +       '<a class="btn btn-primary btn-sm mx-1" href="updateUser.php?id=' + element.id + '">'
                      +             'Actualizar'
                      +       '</a>'
                      +       '<a class="btn btn-warning btn-sm mx-1" href="enableUser.php?id=' + element.id + '">'
                      +             'Habilitar'
                      +       '</a>'
                      +       '<a id="btnEliminarUser" name="' + element.id + '" class="btn btn-danger btn-sm" role="button">'
                      +             'Eliminar'
                      +       '</a>'
                      + '</td>';

            htmlTable += '</tr>';
        });

        htmlTable += '</tbody></table>';

        $("#listaUsuariosTest").empty();

        var usersList = $("#listaUsuariosTest");
        
        usersList.append(htmlTable);
    });
}

$(document).on("click", "#btnEliminarUser", function(event)
{
    var userId = event.currentTarget.name;
    
    DeleteUser(userId);
});

var DeleteUser = function(userId)
{
    $.post('http://localhost/Class10/Ejemplo1V2/includes/Apis/userApi.php', {"action" : "deleteUser", "idUser" : userId }, function(result)
    {
        if (result == "success")
        {
            $('tr[name="' + userId + '"]').hide("slow");
        }
    });
}*/













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
    document.getElementById("campo-editar").value = nombreCampo;

    if(nombreCampo == "Usuario"){
        document.getElementById("campo-editar").autocomplete = "username";
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
