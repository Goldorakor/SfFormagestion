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

        // Incrémente l'index pour que le prochain apprenant ait un nom de champ unique (un identifiant unique)
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


/*

**          **
** cours JS **
**          **


*structure globale*
document => objet principal qui représente la page HTML entière : il permet d’interagir avec le DOM (Document Object Model) — donc d’accéder aux éléments de la page

addEventListener('DOMContentLoaded', function () { ... }) => “Quand le HTML est entièrement chargé, exécute ce code.”



*variables avec let et const*
let sert à déclarer une variable (on peut modifier sa valeur plus tard)

const sert à déclarer une constante (variable qu’on ne veut pas réassigner)
=> const empêche de réassigner la variable, pas de modifier ce que contient un objet ou un tableau

OBJET
const user = {
  nom: "Alice",
  age: 25
};

user.age = 26;      // ✅ OK : on MODIFIE une propriété
user.ville = "Lyon"; // ✅ OK : on AJOUTE une propriété
delete user.ville; // ✅ OK : on SUPPRIME une propriété

user = { nom: "Bob" }; // ❌ Erreur : Assignment to constant variable.

TABLEAU
const fruits = ["pomme", "banane"];

fruits.push("kiwi");      // ✅ OK
fruits[0] = "poire";      // ✅ OK

fruits = ["orange"]; // ❌ Erreur


*conditions*
if (!container || !addButton) return; :  si la condition est vraie, on exécute le bloc qui suit / si container est null ou undefined et si addButton est null ou undefined, on met fin à l'exécution de la fonction immédiatement



*compter les éléments* :
let index = container.querySelectorAll('.apprenant-entry').length;
querySelectorAll('.apprenant-entry') : sélectionne tous les éléments avec cette classe.
.length : donne le nombre total de ces éléments (comme .count() en PHP)



*gestion du clic*
addButton.addEventListener('click', function () {
    // ...
});
=> On écoute un événement click : Quand le bouton est cliqué, on exécute une fonction anonyme (function () { ... })



*manipulation du DOM*
container.dataset.prototype => accède à un attribut HTML personnalisé (data-prototype) et contient le HTML du champ à dupliquer avec le placeholder __name__.

replace(/__name__/g, index) => on remplace tous les __name__ dans le HTML par la valeur de l’index (ex : apprenantsInscrits[0], [1], etc.). le g veut dire “global” : toutes les occurrences.

document.createElement('div') : Crée un élément HTML (ici, une <div>). Ne l'ajoute pas encore au DOM — c’est prêt à l’emploi.

.classList.add(...) : Ajoute une ou plusieurs classes CSS à un élément HTML.

.innerHTML : Permet de lire ou d’écrire le HTML à l’intérieur d’un élément. - Ici, on insère le prototype transformé dans un élément temporaire.

.querySelector('[name*="[prix]"]') : Sélectionne un élément dont l’attribut name contient [prix]. - *= veut dire “contient”.

.appendChild() : Ajoute un élément enfant dans un autre élément.

.insertBefore(newElement, referenceElement) : Insère un élément juste avant un autre dans le DOM.



*incrémentation*
index++; : ajoute 1 à index => sert à garantir que chaque nouvel apprenant a un identifiant unique



*suppression dynamique*
container.addEventListener('click', function (e) {
    if (e.target.closest('.remove-apprenant-btn')) {
        e.target.closest('.apprenant-entry').remove();
    }
});
e : l'objet événement : il contient des infos sur ce qui a été cliqué
.target : élément précisement cliqué
.closest(selector) : remonte dans l’arbre DOM jusqu’à trouver l’ancêtre qui correspond au sélecteur => on cherche la ligne complète à supprimer
.remove() : supprime l’élément du DOM



Mot-clé / Méthode	Rôle
document => Accès au DOM (la page)
addEventListener => Écoute les événements (comme les clics)
let / const => Déclarer des variables
if / return => Logique conditionnelle
createElement => Crée un nouvel élément HTML
appendChild => Ajoute un élément au DOM
querySelector => Sélectionne un élément
innerHTML => Lit/écrit le HTML interne
replace() => Remplace du texte dans une chaîne
dataset => Accès aux attributs data-*
classList.add => Ajoute des classes CSS
remove() => Supprime un élément



Pourquoi __name__ ? → Pour avoir un champ unique à chaque ajout, et que Symfony comprenne chaque élément de la collection.

Pourquoi dataset.prototype ? → Car Symfony met le HTML modèle dans data-prototype, qu’on duplique côté JS.

Pourquoi écouter sur container pour la suppression ? → Car les boutons sont ajoutés dynamiquement, donc on écoute sur un parent déjà existant.

*/
