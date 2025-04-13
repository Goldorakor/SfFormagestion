document.addEventListener('DOMContentLoaded', function () {
    let container = document.getElementById('apprenants-container');
    let addButton = document.getElementById('add-apprenant');

    if (!container || !addButton) return;

    let index = container.querySelectorAll('.apprenant-entry').length;

    addButton.addEventListener('click', function () {
        let prototype = container.dataset.prototype;
        let newForm = prototype.replace(/__name__/g, index);
        let div = document.createElement('div');
        div.classList.add('apprenant-entry', 'form-ligne01');
        div.innerHTML = newForm + `
            <button type="button" class="remove-apprenant btn btn-danger">
                <i class="fa-solid fa-trash"></i>
            </button>
        `;
        container.appendChild(div);
        index++;
    });

    container.addEventListener('click', function (e) {
        if (e.target.closest('.remove-apprenant')) {
            e.target.closest('.apprenant-entry').remove();
        }
    });
});
