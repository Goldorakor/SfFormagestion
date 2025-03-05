

document.addEventListener("DOMContentLoaded", function () {
    const nbPlacesInput = document.querySelector("[data-nb-places]");
    const apprenantsContainer = document.getElementById("apprenants-container");
    const prototype = apprenantsContainer.dataset.prototype;
    
    function updateApprenantsFields() {
        const nbPlaces = parseInt(nbPlacesInput.value, 10) || 0;
        let currentFields = apprenantsContainer.querySelectorAll(".apprenant-entry").length;

        // Supprimer les champs en trop
        while (currentFields > nbPlaces) {
            apprenantsContainer.lastChild.remove();
            currentFields--;
        }

        // Ajouter les champs manquants
        while (currentFields < nbPlaces) {
            let newField = document.createElement("div");
            newField.classList.add("apprenant-entry");
            newField.innerHTML = prototype.replace(/__name__/g, currentFields);
            apprenantsContainer.appendChild(newField);
            currentFields++;
        }
    }

    // Détecter les changements du champ `nbPlaces`
    nbPlacesInput.addEventListener("change", updateApprenantsFields);
});



// On attend que le DOM soit entièrement chargé pour exécuter le script
document.addEventListener("DOMContentLoaded", function () {

    // On récupère l'élément qui possède l'attribut data-nb-places (nbPlacesInput -> correspond à l'élément où l'utilisateur indique le nombre de places désirées)
    const nbPlacesInput = document.querySelector("[data-nb-places]"); 

    // On récupère le conteneur qui accueillera les champs pour les apprenants (apprenantsContainer est l'élément qui contiendra dynamiquement les champs pour chaque apprenant)
    const apprenantsContainer = document.getElementById("apprenants-container");

    // On récupère le prototype HTML stocké dans l'attribut data-prototype du conteneur
    // Ce prototype contient le template pour créer un nouveau champ d'apprenant, avec un placeholder "__name__" à remplacer
    const prototype = apprenantsContainer.dataset.prototype;
    
    // Déclaration de la fonction qui met à jour les champs des apprenants en fonction du nombre de places renseigné
    function updateApprenantsFields() {
        // On convertit la valeur de l'input en entier, en base 10, et si la conversion échoue, on utilise 0
        const nbPlaces = parseInt(nbPlacesInput.value, 10) || 0;
        
        // On compte le nombre actuel de champs (div avec la classe "apprenant-entry") dans le conteneur
        let currentFields = apprenantsContainer.querySelectorAll(".apprenant-entry").length;

        // --- Suppression des champs en trop ---
        // Tant qu'il y a plus de champs que nécessaire, on supprime le dernier enfant du conteneur
        while (currentFields > nbPlaces) {
            // On enlève le dernier champ dans le conteneur
            apprenantsContainer.lastChild.remove();
            // On décrémente le compteur de champs
            currentFields--;
        }

        // --- Ajout des champs manquants ---
        // Tant qu'il y a moins de champs que requis, on crée de nouveaux champs
        while (currentFields < nbPlaces) {
            // On crée une nouvelle div pour représenter un champ d'apprenant
            let newField = document.createElement("div");

            // On ajoute la classe "apprenant-entry" pour la styliser ou la repérer facilement
            newField.classList.add("apprenant-entry");

            // On utilise le prototype en remplaçant le placeholder "__name__" par le numéro du champ actuel
            newField.innerHTML = prototype.replace(/__name__/g, currentFields);

            // On ajoute le nouveau champ dans le conteneur
            apprenantsContainer.appendChild(newField);
            
            // On incrémente le compteur de champs
            currentFields++;
        }
    }

    // On attache un écouteur d'événement sur l'input pour détecter tout changement (par exemple, lorsqu'on modifie le nombre de places)
    // Ainsi, à chaque modification, la fonction updateApprenantsFields sera appelée pour synchroniser le nombre de champs affichés
    nbPlacesInput.addEventListener("change", updateApprenantsFields);
});
