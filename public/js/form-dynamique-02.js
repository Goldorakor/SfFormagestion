// Cette fonction s'exécute lorsque le DOM est entièrement chargé, ce qui signifie que tous les éléments HTML sont disponibles.
document.addEventListener("DOMContentLoaded", function () {
    
    // Récupère l'élément HTML qui contient le nombre de modules disponibles.
    // On récupère l'élément qui possède l'attribut data-nb-modules (nbModulesInput -> correspond à l'élément où l'utilisateur indique le nombre de modules désirés)
    const nbModulesInput = document.querySelector("[data-nb-modules]");

    // On récupère le conteneur qui accueillera les champs pour les modules (modulesContainer est l'élément qui contiendra dynamiquement les champs pour chaque module)
    const modulesContainer = document.getElementById("modules-container");

    // Récupère le prototype HTML du champ module (élément de formulaire préformaté stocké dans l'attribut data-prototype du conteneur) à cloner pour ajouter de nouveaux champs.
    // Ce prototype contient le template pour créer un nouveau champ de module
    const prototype = modulesContainer.dataset.prototype;
    
    // Déclaration de la fonction qui met à jour dynamiquement le nombre de champs de module en fonction du nombre de modules renseigné
    function updateModulesFields() {

        // On récupère la valeur de l'input (nbModules) et on la convertit en entier, en base 10, et si la conversion échoue (Si la valeur n'est pas valide -> par exemple une chaîne vide), elle est remplacée par 0.
        const nbModules = parseInt(nbModulesInput.value, 10) || 0;

        // On compte le nombre actuel de champs pour les modules dans le conteneur (div avec la classe "module-entry") dans le conteneur
        let currentFields = modulesContainer.querySelectorAll(".module-entry").length;

        // --- Suppression des champs en trop ---
        // Tant qu'il y a plus de champs que nécessaire, on supprime le dernier enfant du conteneur
        while (currentFields > nbModules) {
            modulesContainer.lastChild.remove(); // Supprime le dernier enfant du conteneur (le dernier champ ajouté)
            currentFields--; // On décrémente le compteur de champs
        }

        // --- Ajout des champs manquants ---
        // Tant qu'il y a moins de champs que requis, on crée de nouveaux champs
        while (currentFields < nbModules) {

            // On crée un nouvel élément `div` pour un champ de module.
            let newField = document.createElement("div");

            // // On ajoute la classe "module-entry" pour la styliser ou la repérer facilement
            newField.classList.add("module-entry");

            // On utilise le prototype en remplaçant le placeholder "__name__" par le numéro du champ actuel (ex: module-0, module-1, etc.). Cela permet de rendre chaque champ unique.
            newField.innerHTML = prototype.replace(/__name__/g, currentFields);

            // Ajoute le nouveau champ à la fin du conteneur de module.
            modulesContainer.appendChild(newField);
            currentFields++; // // On incrémente le compteur de champs
        }
    }

    // Détecter les changements du champ `nbModules`:
    // On attache un écouteur d'événement sur l'input pour détecter tout changement (par exemple, lorsqu'on modifie le nombre de modules)
    // Chaque fois que l'utilisateur change la valeur, la fonction updateModulesFields sera appelée pour synchroniser le nombre de champs affichés
    nbModulesInput.addEventListener("change", updateModulesFields);
});
