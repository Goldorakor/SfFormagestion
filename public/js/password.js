// On récupère l’élément de l’icône (l’œil) par son identifiant
const togglePassword = document.getElementById("togglePassword");

// On récupère le champ de mot de passe par son identifiant
const passwordInput = document.getElementById("password");

// On ajoute un écouteur d’événement au clic sur l’icône
togglePassword.addEventListener("click", function () {

    // On vérifie si le champ est actuellement de type "password"
    const isPassword = passwordInput.type === "password";
    // passwordInput est une référence à l'élément <input> du formulaire : const passwordInput = document.getElementById("password");
    // .type retourne le type de cet input. Il peut être "password" (texte masqué) ou "text" (texte visible).
    // On compare ce type à la chaîne "password" avec === (égalité stricte).
    // Le résultat est un booléen (soit true, soit false) que l'on stocke dans la constante isPassword

    // Si le champ est de type "password", on change le type en "text" (pour le rendre visible), sinon on repasse en "password"
    passwordInput.type = isPassword ? "text" : "password"; // opération ternaire

    // On alterne (toggle) les classes Font Awesome pour changer l’icône affichée
    // Si c’était l’œil, on passe à l’œil barré, et inversement
    this.classList.toggle("fa-eye"); // this désigne l’élément HTML sur lequel on a cliqué
    this.classList.toggle("fa-eye-slash"); // classList est une propriété qui donne accès à toutes les classes CSS de l'élément
});


/*

<i class="fa-solid fa-eye" id="togglePassword"></i>

this.classList contient les classes suivantes : ["fa-solid", "fa-eye"]

*/

/* 

.toggle("nom-de-classe")

C’est une fonction magique qui :

Ajoute une classe si elle n’est pas déjà là

Supprime une classe si elle est déjà là

*/