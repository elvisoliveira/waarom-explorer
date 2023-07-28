const comparer = (idx, asc) => (a, b) => ((v1, v2) => v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2))(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));
const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;
document.querySelectorAll('thead th').forEach(th => th.addEventListener('click', (() => {
    const table = th.closest('table');
    const tbody = table.querySelector('tbody');
    Array.from(tbody.querySelectorAll('tr')).sort(comparer(Array.from(th.parentNode.children).indexOf(th), this.asc = !this.asc)).forEach(tr => tbody.appendChild(tr));
})));

document.querySelectorAll('tbody tr').forEach((row) => {
    let columns = Array.from(row.querySelectorAll('td')).reverse();
    let count = 0;
    columns.every((column) => {
        if(column.querySelector('span')) {
            return false;
        }
        column.innerText = '-';
        count++;
        return true;
    });
    columns[0].innerText = count - 1;
    row.querySelector('i').addEventListener('click', function() {
        row.style.display = 'none';
    });
});

document.querySelectorAll('span[helper]').forEach((data) => {
    data.addEventListener('mouseover', function() {
        const helper = data.getAttribute('helper');
        document.querySelectorAll('tbody th').forEach((row) => {
            if(row.innerText == helper) {
                const index = Array.from(data.closest('tr').childNodes).indexOf(data.parentNode);
                const cols = Array.from(row.parentNode.childNodes);
                cols[index].id = 'red';
            }
        });
        this.addEventListener('mouseout', function() {
            document.getElementById('red')?.removeAttribute('id');
        });
    });
});