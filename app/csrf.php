<?php
// app/csrf.php

/**
 * Genera un token CSRF y lo guarda en la sesión.
 * Si ya existe, devuelve el mismo.
 */
function csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Genera el campo hidden HTML con el token dentro.
 */
function csrf_field() {
    $token = csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Comprueba si el token enviado por POST es válido.
 */
function check_csrf() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $token_post = $_POST['csrf_token'] ?? '';
    $token_sesion = $_SESSION['csrf_token'] ?? '';
    
    // hash_equals compara de forma segura para evitar ataques de temporización
    if (!hash_equals($token_sesion, $token_post)) {
        die("Error de seguridad: Token CSRF inválido. Vuelve atrás e inténtalo de nuevo.");
    }
}