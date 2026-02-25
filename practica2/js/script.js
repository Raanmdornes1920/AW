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