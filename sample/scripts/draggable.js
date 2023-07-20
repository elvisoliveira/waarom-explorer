window.onload = function() {
    draggable(document.getElementById('draggable'));
}

let ignore = false;

window.addEventListener ('click', function (event) {
    if (ignore) event.stopPropagation();
    ignore = false;
}, true);

function draggable(el) {
    el.addEventListener('mousedown', function(e) {
        var offsetX = e.clientX - parseInt(window.getComputedStyle(this).left);
        var offsetY = e.clientY - parseInt(window.getComputedStyle(this).top);
        function mouseMoveHandler(e) {
            Object.assign(el.style, {
                top: (e.clientY - offsetY) + 'px',
                left: (e.clientX - offsetX) + 'px',
                right: 'unset',
                bottom: 'unset'
            });
            ignore = true;
        }
        function reset() {
            window.removeEventListener('mousemove', mouseMoveHandler);
            window.removeEventListener('mouseup', reset);
        }
        window.addEventListener('mousemove', mouseMoveHandler);
        window.addEventListener('mouseup', reset);
    });
}
