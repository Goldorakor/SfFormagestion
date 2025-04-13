document.addEventListener('DOMContentLoaded', function () {
    let container = document.getElementById('modules-container');
    let addButton = document.getElementById('add-module');

    if (!container || !addButton) return;

    let index = container.querySelectorAll('.module-entry').length;

    addButton.addEventListener('click', function () {
        let prototype = container.dataset.prototype;
        let newForm = prototype.replace(/__name__/g, index);
        let div = document.createElement('div');
        div.classList.add('module-entry', 'form-ligne01');
        div.innerHTML = newForm + `
            <button type="button" class="remove-module btn btn-danger">
                <i class="fa-solid fa-trash"></i>
            </button>
        `;
        container.appendChild(div);
        index++;
    });

    container.addEventListener('click', function (e) {
        if (e.target.closest('.remove-module')) {
            e.target.closest('.module-entry').remove();
        }
    });
});
