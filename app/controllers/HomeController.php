<?php
/**
 * HomeController Class
 * Public Landing Page Controller for TKJ Service Unit
 */

class HomeController extends Controller
{
    public function index()
    {
        $serviceModel = $this->model('ServiceModel');
        $services = $serviceModel->getAllServices();

        $data = [
            'title' => 'Landing Page - TKJ Service Center',
            'services' => $services,
            'active_menu' => 'home'
        ];

        $this->view('templates/header', $data);
        $this->view('templates/navbar', $data);
        $this->view('home/index', $data);
        $this->view('templates/footer', $data);
    }
}
