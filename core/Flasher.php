<?php
/**
 * Flasher Class
 * Helper for rendering Bootstrap alert notifications
 */

class Flasher
{
    public static function setFlash($message, $action, $type = 'success')
    {
        $_SESSION['flash'] = [
            'message' => $message,
            'action'  => $action,
            'type'    => $type
        ];
    }

    public static function flash()
    {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            $typeClass = $flash['type'] === 'danger' ? 'alert-danger' : ($flash['type'] === 'warning' ? 'alert-warning' : 'alert-success');
            
            echo '<div class="alert ' . $typeClass . ' alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                    <strong>' . htmlspecialchars($flash['message']) . '</strong> ' . htmlspecialchars($flash['action']) . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
            unset($_SESSION['flash']);
        }
    }
}
