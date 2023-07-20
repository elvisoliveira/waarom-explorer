const checkboxes = document.querySelectorAll('input[type="checkbox"]');
checkboxes.forEach(checkboxInput => {
    checkboxInput.addEventListener('change', filter);
});

for (const button of document.querySelectorAll("button#none, button#all")) {
    button.addEventListener("click", function() {
        for (const checkbox of checkboxes) {
            checkbox.checked = this.id === 'all';
        }
        filter();
    });
}

function filter() {
    let selected = [];
    for (const element of document.querySelectorAll("input[type='checkbox']:checked")) {
        selected.push(element.value);
    }
    document.querySelectorAll('tbody tr').forEach((row) => {
        let show = false;
        Array.from(row.querySelectorAll('span')).every((badge) => {
            if(selected.includes(badge.innerText.trim())) {
                show = true; return false;
            }
            return true;
        });
        row.style.display = show ? 'table-row' : 'none';
    });
}

filter();
