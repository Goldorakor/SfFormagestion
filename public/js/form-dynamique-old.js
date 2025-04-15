document.addEventListener('DOMContentLoaded', function () {
    function setupCollection(containerId, entryClass, buttonId, removeButtonClass) {
        const container = document.getElementById(containerId);
        const addButton = document.getElementById(buttonId);

        if (!container || !addButton) return;

        let index = container.querySelectorAll('.' + entryClass).length;

        addButton.addEventListener('click', function () {
            const prototype = container.dataset.prototype;
            const newForm = prototype.replace(/__name__/g, index);

            const temp = document.createElement('div');
            temp.innerHTML = newForm;
            const entry = temp.firstElementChild;

            entry.classList.add(entryClass);

            // Ajout bouton supprimer
            const removeBtnDiv = document.createElement('div');
            removeBtnDiv.classList.add('form-cell', 'remove-' + entryClass);
            removeBtnDiv.innerHTML = `
                <button type="button" class="btn btn-parme ${removeButtonClass}">
                    <i class="fa-solid fa-trash"></i>
                </button>
            `;
            entry.appendChild(removeBtnDiv);

            container.insertBefore(entry, addButton.closest('.form-ligne01'));
            index++;
        });

        container.addEventListener('click', function (e) {
            if (e.target.closest('.' + removeButtonClass)) {
                e.target.closest('.' + entryClass).remove();
            }
        });
    }

    // Gestion des apprenants
    setupCollection(
        'apprenants-container',
        'apprenant-entry',
        'add-apprenant',
        'remove-apprenant-btn'
    );

    // Gestion des modules
    setupCollection(
        'modules-container',
        'module-entry',
        'add-module',
        'remove-module-btn'
    );
});
