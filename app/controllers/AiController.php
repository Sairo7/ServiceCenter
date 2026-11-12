<?php
/**
 * AiController Class
 * Handles AI Customer Assistant Chatbot UI and Secure Backend Proxy
 */

class AiController extends Controller
{
    /**
     * Chatbot UI Page
     */
    public function index()
    {
        $user = Auth::user();
        $aiModel = $this->model('AiModel');

        $history = [];
        if ($user) {
            $history = $aiModel->getConversationHistory($user['id']);
        }

        $data = [
            'title' => 'AI Customer Assistant - Konsultasi Layanan IT TKJ',
            'active_menu' => 'ai_assistant',
            'user' => $user,
            'history' => $history
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('ai/index', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * SECURE BACKEND PROXY AJAX POST: Process customer query
     */
    public function ask()
    {
        // Set JSON header for AJAX
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Read raw JSON or POST data
            $rawInput = file_get_contents('php://input');
            $dataInput = json_decode($rawInput, true);

            $userMessage = trim($dataInput['message'] ?? ($_POST['message'] ?? ''));

            if (empty($userMessage)) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Pesan tidak boleh kosong.'
                ]);
                exit;
            }

            $aiModel = $this->model('AiModel');
            $user = Auth::user();
            $userId = $user['id'] ?? null;
            $userRole = Auth::check() ? Auth::role() : 'guest';

            // Generate high-precision response using Smart Engine with role and user awareness
            $response = $aiModel->generateSmartFallbackResponse($userMessage, $userRole, $user);

            // Log to database ai_conversations if user is logged in or system default
            if ($userId) {
                $aiModel->saveConversation($userId, $userMessage, $response['text']);
            }

            echo json_encode([
                'success' => true,
                'text' => $response['text'],
                'recommended_service' => $response['recommended_service'] ?? null
            ]);
            exit;
        }

        echo json_encode([
            'success' => false,
            'error' => 'Method Invalid'
        ]);
        exit;
    }
}
