document.addEventListener('DOMContentLoaded', function () {
    // Récupère l'élément contenant les modules ajoutés
    let container = document.getElementById('modules-container');
    
    // Récupère le bouton pour ajouter un module
    let addButton = document.getElementById('add-module');

    // Si le conteneur ou le bouton n'existent pas, on arrête l'exécution du code
    if (!container || !addButton) return;

    // Récupère le nombre d'entrées de modules déjà présentes pour définir l'index du prochain ajout
    let index = container.querySelectorAll('.module-entry').length;

    // Ajoute un écouteur d'événements au bouton "Ajouter un module"
    addButton.addEventListener('click', function () {
        // Récupère le prototype (HTML de base) pour un nouveau module
        let prototype = container.dataset.prototype;
        
        // Remplace le placeholder __name__ par l'index actuel (utile pour les noms de champs uniques)
        let newForm = prototype.replace(/__name__/g, index);

        // Crée un élément div pour encapsuler la nouvelle ligne
        let div = document.createElement('div');
        div.classList.add('module-entry', 'form-ligne01');  // Ajoute des classes pour le style

        // Crée un élément temporaire pour récupérer les champs du prototype sans les ajouter tout de suite
        let temp = document.createElement('div');
        temp.innerHTML = newForm;

        // Sélectionne les champs module, duree, dateDebut et dateFin à partir du prototype
        const moduleField = temp.querySelector('[name*="[module]"]');
        const dureeField = temp.querySelector('[name*="[duree]"]');
        const dateDebutField = temp.querySelector('[name*="[dateDebut]"]');
        const dateFinField = temp.querySelector('[name*="[dateFin]"]');

        // Crée la cellule pour le champ "module" et y insère le champ récupéré
        const divModule = document.createElement('div');
        divModule.classList.add('form-cell', 'module-nomModule');
        divModule.appendChild(moduleField);

        // Crée la cellule pour le champ "prix" et y insère le champ récupéré
        const divDuree = document.createElement('div');
        divDuree.classList.add('form-cell', 'module-duree');
        divDuree.appendChild(dureeField);

        // Crée la cellule pour le champ "prix" et y insère le champ récupéré
        const divDateDebut = document.createElement('div');
        divDateDebut.classList.add('form-cell', 'module-dateDebut');
        divDateDebut.appendChild(dateDebutField);

        // Crée la cellule pour le champ "prix" et y insère le champ récupéré
        const divDateFin = document.createElement('div');
        divDateFin.classList.add('form-cell', 'module-dateFin');
        divDateFin.appendChild(dateFinField);

        // Crée la cellule pour le bouton de suppression avec l'icône poubelle
        const divRemove = document.createElement('div');
        divRemove.classList.add('form-cell', 'remove-module');
        divRemove.innerHTML = `
            <button type="button" class="btn btn-parme remove-module-btn">
                <i class="fa-solid fa-trash"></i>
            </button>
        `;

        // Assemble toutes les cellules dans la ligne du module
        div.appendChild(divModule);
        div.appendChild(divDuree);
        div.appendChild(divDateDebut);
        div.appendChild(divDateFin);
        div.appendChild(divRemove);

        // Ajoute cette ligne au conteneur avant le bouton "ajouter un module"
        container.insertBefore(div, addButton.parentElement);

        // Incrémente l'index pour que le prochain module ait un nom de champ unique
        index++;
    });

    // Gestion du clic sur le bouton de suppression (icône poubelle)
    container.addEventListener('click', function (e) {
        // Vérifie si l'élément cliqué est un bouton de suppression
        if (e.target.closest('.remove-module-btn')) {
            // Si c'est le cas, on supprime la ligne correspondante
            e.target.closest('.module-entry').remove();
        }
    });
});
