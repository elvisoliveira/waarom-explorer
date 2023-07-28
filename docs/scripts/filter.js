const checkboxes = document.querySelectorAll('input[type="checkbox"]');
checkboxes.forEach(checkboxInput => {
    checkboxInput.addEventListener('change', filter);
});

document.querySelector('input[type="number"]').addEventListener('input', filter);

for (const button of document.querySelectorAll('button#none, button#all')) {
    button.addEventListener('click', function() {
        for (const checkbox of checkboxes) {
            checkbox.checked = this.id === 'all';
        }
        filter();
    });
}

function filter() {
    const selected = Array.from(document.querySelectorAll("input[type='checkbox']:checked")).map(element => element.value);
    document.querySelectorAll('tbody tr').forEach((row) => {
        const columns = Array.from(row.querySelectorAll('td')).reverse();
        const unnasignedWeeks = columns[0].innerText;
        const threshold = document.getElementById('threshold').value;
        const show = Array.from(row.querySelectorAll('span')).some((badge) => {
            return selected.includes(badge.innerText.trim());
        });
        row.style.display = +unnasignedWeeks <= +threshold && show ? 'table-row' : 'none';
    });
    document.querySelector('tfoot tr').style.display = document.querySelectorAll('tr[style*="display: table-row"]').length ? 'none' : 'table-row';
}

filter();
