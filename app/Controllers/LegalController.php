<?php

namespace App\Controllers;

use App\Core\Controller;

class LegalController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display Terms and Conditions page
     */
    public function terms(): void
    {
        $this->view('legal/terms');
    }

    /**
     * Display Privacy Policy page
     */
    public function privacy(): void
    {
        $this->view('legal/privacy');
    }
}
