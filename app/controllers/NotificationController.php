<?php
/**
 * NotificationController Class
 * Manages AJAX Notification APIs & Notification Center View
 */

class NotificationController extends Controller
{
    /**
     * View Notification History Page
     */
    public function index()
    {
        Auth::requireLogin();

        $user = Auth::user();
        $notifModel = $this->model('NotificationModel');

        $notifications = $notifModel->getAllNotifications($user['id'], 50);
        $unreadCount   = $notifModel->getUnreadCount($user['id']);

        $data = [
            'title' => 'Pusat Notifikasi - TKJ Service Center',
            'active_menu' => 'notifications',
            'user' => $user,
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('notification/index', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * AJAX GET API: Fetch unread count & unread notifications for navbar dropdown
     */
    public function unread_api()
    {
        header('Content-Type: application/json');

        if (!Auth::check()) {
            echo json_encode(['success' => false, 'unread_count' => 0, 'items' => []]);
            exit;
        }

        $user = Auth::user();
        $notifModel = $this->model('NotificationModel');

        $unreadCount = $notifModel->getUnreadCount($user['id']);
        $items       = $notifModel->getUnreadNotifications($user['id'], 8);

        // Format relative date string
        $formattedItems = [];
        foreach ($items as $item) {
            $formattedItems[] = [
                'id' => $item['id'],
                'title' => htmlspecialchars($item['title']),
                'message' => htmlspecialchars($item['message']),
                'type' => $item['type'],
                'link_url' => $item['link_url'] ? (BASE_URL . '/' . ltrim($item['link_url'], '/')) : '#',
                'time_ago' => $this->timeAgo($item['created_at'])
            ];
        }

        echo json_encode([
            'success' => true,
            'unread_count' => $unreadCount,
            'items' => $formattedItems
        ]);
        exit;
    }

    /**
     * AJAX POST API: Mark notification as read
     */
    public function mark_read_api()
    {
        header('Content-Type: application/json');

        if (!Auth::check()) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rawInput = file_get_contents('php://input');
            $dataInput = json_decode($rawInput, true) ?: $_POST;

            $user = Auth::user();
            $notifModel = $this->model('NotificationModel');

            $notifId = intval($dataInput['id'] ?? 0);
            $markAll = isset($dataInput['mark_all']) ? true : false;

            if ($markAll) {
                $notifModel->markAllAsRead($user['id']);
                echo json_encode(['success' => true, 'message' => 'Semua notifikasi ditandai dibaca']);
                exit;
            }

            if ($notifId > 0) {
                $notifModel->markAsRead($notifId, $user['id']);
                echo json_encode(['success' => true]);
                exit;
            }
        }

        echo json_encode(['success' => false, 'error' => 'Invalid request']);
        exit;
    }

    /**
     * Helper relative time string generator (Indonesian)
     */
    private function timeAgo($datetime)
    {
        $time = strtotime($datetime);
        $diff = time() - $time;

        if ($diff < 60) {
            return 'Baru saja';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return $mins . ' mnt lalu';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' jam lalu';
        } elseif ($diff < 2592000) {
            $days = floor($diff / 86400);
            return $days . ' hari lalu';
        } else {
            return date('d M Y', $time);
        }
    }
}
