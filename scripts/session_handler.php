<?php
session_start();

function is_session_valid() {
    if (!isset($_SESSION['token']) || !isset($_SESSION['token_expiry']) || !isset($_SESSION['user_id'])) {
        return false;
    }

    if ($_SESSION['token_expiry'] < time()) {
        // Token has expired
        session_unset();
        session_destroy();
        return false;
    }

    return true;
}

if (!is_session_valid()) {
    header('Location: ../pages/login.html');
    exit();
}




