// Cette fonction s'exécute lorsque le DOM est entièrement chargé, ce qui signifie que tous les éléments HTML sont disponibles.
document.addEventListener("DOMContentLoaded", function () {
    
    // Récupère l'élément HTML qui contient le nombre de places disponibles.
    // On récupère l'élément qui possède l'attribut data-nb-places (nbPlacesInput -> correspond à l'élément où l'utilisateur indique le nombre de places désirées)
    const nbPlacesInput = document.querySelector("[data-nb-places]");

    // On récupère le conteneur qui accueillera les champs pour les apprenants (apprenantsContainer est l'élément qui contiendra dynamiquement les champs pour chaque apprenant)
    const apprenantsContainer = document.getElementById("apprenants-container");

    // Récupère le prototype HTML du champ apprenant (élément de formulaire préformaté stocké dans l'attribut data-prototype du conteneur) à cloner pour ajouter de nouveaux champs.
    // Ce prototype contient le template pour créer un nouveau champ d'apprenant
    const prototype = apprenantsContainer.dataset.prototype;
    
    // Déclaration de la fonction qui met à jour dynamiquement le nombre de champs d'apprenant en fonction du nombre de places renseigné
    function updateApprenantsFields() {

        // On récupère la valeur de l'input (nbPlaces) et on la convertit en entier, en base 10, et si la conversion échoue (Si la valeur n'est pas valide -> par exemple une chaîne vide), elle est remplacée par 0.
        const nbPlaces = parseInt(nbPlacesInput.value, 10) || 0;

        // On compte le nombre actuel de champs pour les apprenants dans le conteneur (div avec la classe "apprenant-entry") dans le conteneur
        let currentFields = apprenantsContainer.querySelectorAll(".apprenant-entry").length;

        // --- Suppression des champs en trop ---
        // Tant qu'il y a plus de champs que nécessaire, on supprime le dernier enfant du conteneur
        while (currentFields > nbPlaces) {
            apprenantsContainer.lastChild.remove(); // Supprime le dernier enfant du conteneur (le dernier champ ajouté)
            currentFields--; // On décrémente le compteur de champs
        }

        // --- Ajout des champs manquants ---
        // Tant qu'il y a moins de champs que requis, on crée de nouveaux champs
        while (currentFields < nbPlaces) {

            // On crée un nouvel élément `div` pour un champ d'apprenant.
            let newField = document.createElement("div");

            // // On ajoute la classe "apprenant-entry" pour la styliser ou la repérer facilement
            newField.classList.add("apprenant-entry");

            // On utilise le prototype en remplaçant le placeholder "__name__" par le numéro du champ actuel (ex: apprenant-0, apprenant-1, etc.). Cela permet de rendre chaque champ unique.
            newField.innerHTML = prototype.replace(/__name__/g, currentFields);

            // Ajoute le nouveau champ à la fin du conteneur d'apprenant.
            apprenantsContainer.appendChild(newField);
            currentFields++; // // On incrémente le compteur de champs
        }
    }

    // Détecter les changements du champ `nbPlaces`:
    // On attache un écouteur d'événement sur l'input pour détecter tout changement (par exemple, lorsqu'on modifie le nombre de places)
    // Chaque fois que l'utilisateur change la valeur, la fonction updateApprenantsFields sera appelée pour synchroniser le nombre de champs affichés
    nbPlacesInput.addEventListener("change", updateApprenantsFields);
});
