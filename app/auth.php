<?php
// app/auth.php

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Comprueba si el usuario está logueado.
 * Si no lo está, lo redirige al login.
 */
function require_login() {
    if (!isset($_SESSION['usuario_id'])) {
        // Redirigimos al login. Ajusta la ruta según dónde se llame al script.
        // Como estamos en /public/, la ruta relativa al login suele ser 'login.php'
        header('Location: login.php');
        exit;
    }
}

/**
 * Devuelve los datos del usuario actual (de la sesión).
 */
function current_user() {
    return $_SESSION['usuario_nombre'] ?? null;
}

/**
 * Comprueba si el usuario actual es admin.
 */
function is_admin() {
    return (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin');
}