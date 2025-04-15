document.addEventListener('DOMContentLoaded', function () {
    // Récupère l'élément contenant les apprenants inscrits
    let container = document.getElementById('apprenants-container');
    
    // Récupère le bouton pour ajouter un apprenant
    let addButton = document.getElementById('add-apprenant');

    // Si le conteneur ou le bouton n'existent pas, on arrête l'exécution du code
    if (!container || !addButton) return;

    // Récupère le nombre d'entrées d'apprenants déjà présentes pour définir l'index du prochain ajout
    let index = container.querySelectorAll('.apprenant-entry').length;

    // Ajoute un écouteur d'événements au bouton "Ajouter un apprenant"
    addButton.addEventListener('click', function () {
        // Récupère le prototype (HTML de base) pour un nouvel apprenant
        let prototype = container.dataset.prototype;
        
        // Remplace le placeholder __name__ par l'index actuel (utile pour les noms de champs uniques)
        let newForm = prototype.replace(/__name__/g, index);

        // Crée un élément div pour encapsuler la nouvelle ligne
        let div = document.createElement('div');
        div.classList.add('apprenant-entry', 'form-ligne01');  // Ajoute des classes pour le style

        // Crée un élément temporaire pour récupérer les champs du prototype sans les ajouter tout de suite
        let temp = document.createElement('div');
        temp.innerHTML = newForm;

        // Sélectionne les champs apprenant et prix à partir du prototype
        const apprenantField = temp.querySelector('[name*="[apprenant]"]');
        const prixField = temp.querySelector('[name*="[prix]"]');

        // Crée la cellule pour le champ "apprenant" et y insère le champ récupéré
        const divApprenant = document.createElement('div');
        divApprenant.classList.add('form-cell', 'apprenant-inscrit');
        divApprenant.appendChild(apprenantField);

        // Crée la cellule pour le champ "prix" et y insère le champ récupéré
        const divPrix = document.createElement('div');
        divPrix.classList.add('form-cell', 'apprenant-prix');
        divPrix.appendChild(prixField);

        // Crée la cellule pour le bouton de suppression avec l'icône poubelle
        const divRemove = document.createElement('div');
        divRemove.classList.add('form-cell', 'remove-apprenant');
        divRemove.innerHTML = `
            <button type="button" class="btn btn-parme remove-apprenant-btn">
                <i class="fa-solid fa-trash"></i>
            </button>
        `;

        // Assemble toutes les cellules dans la ligne de l'apprenant
        div.appendChild(divApprenant);
        div.appendChild(divPrix);
        div.appendChild(divRemove);

        // Ajoute cette ligne au conteneur avant le bouton "ajouter un apprenant"
        container.insertBefore(div, addButton.parentElement);

        // Incrémente l'index pour que le prochain apprenant ait un nom de champ unique
        index++;
    });

    // Gestion du clic sur le bouton de suppression (icône poubelle)
    container.addEventListener('click', function (e) {
        // Vérifie si l'élément cliqué est un bouton de suppression
        if (e.target.closest('.remove-apprenant-btn')) {
            // Si c'est le cas, on supprime la ligne correspondante
            e.target.closest('.apprenant-entry').remove();
        }
    });
});
