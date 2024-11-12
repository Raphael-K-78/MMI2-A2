function limitCheckbox() {
    // Récupère toutes les cases à cocher
    var checkboxes = document.getElementsByName('ship[]');
    var checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
    
    // Si deux cases sont cochées, désactive toutes les cases non cochées
    if (checkedCount >= 2) {
        checkboxes.forEach(checkbox => {
            if (!checkbox.checked) {
                checkbox.disabled = true;
            }
        });
    } else {
        // Sinon, réactive toutes les cases si moins de deux sont cochées
        checkboxes.forEach(checkbox => {
            checkbox.disabled = false;
        });
    }
    if (checkedCount >= 2) {
        document.getElementById("submit_create").disabled = false;
    }
    else{
        document.getElementById("submit_create").disabled = true;
    }
}
