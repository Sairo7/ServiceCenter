<?php
/**
 * Auth Middleware Class
 * Security & Role-Based Access Control (RBAC) Helper
 */

class Auth
{
    /**
     * Check if user is currently authenticated
     */
    public static function check()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Get array of logged-in user data
     */
    public static function user()
    {
        if (!self::check()) {
            return null;
        }

        return [
            'id'        => $_SESSION['user_id'] ?? null,
            'name'      => $_SESSION['user_name'] ?? 'User',
            'username'  => $_SESSION['user_username'] ?? '',
            'email'     => $_SESSION['user_email'] ?? '',
            'role'      => $_SESSION['user_role'] ?? 'customer',
            'phone'     => $_SESSION['user_phone'] ?? ''
        ];
    }

    /**
     * Get current user role string
     */
    public static function role()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user_role'] ?? null;
    }

    /**
     * Check if user has specific role or roles
     */
    public static function hasRole($allowedRoles)
    {
        if (!self::check()) {
            return false;
        }

        $currentRole = self::role();
        if (is_array($allowedRoles)) {
            return in_array($currentRole, $allowedRoles);
        }

        return $currentRole === $allowedRoles;
    }

    /**
     * Guard Route: Requires User to be Logged In
     */
    public static function requireLogin()
    {
        if (!self::check()) {
            Flasher::setFlash('Akses terbatas!', 'Silakan login terlebih dahulu untuk membuka halaman tersebut.', 'warning');
            header('Location: ' . BASE_URL . '/index.php?url=auth/login');
            exit;
        }
    }

    /**
     * Guard Route: Requires User to have specific Role(s)
     * If unauthenticated -> redirect to login.
     * If unauthorized role -> render 403 Forbidden page.
     */
    public static function requireRole($allowedRoles)
    {
        self::requireLogin();

        if (!self::hasRole($allowedRoles)) {
            header('Location: ' . BASE_URL . '/index.php?url=auth/forbidden');
            exit;
        }
    }
}
