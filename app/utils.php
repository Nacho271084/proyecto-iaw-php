<?php
function e($texto) {
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

function old($campo, $defecto = '') {
    return $_POST[$campo] ?? $defecto;
}
