<?php
class AboutController
{
    public function index()
    {
        $team = ['Alice', 'Bob', 'Charlie'];
        $companyInfo = 'We are a modern PHP framework company.';
        // Pass variables to the view
        include __DIR__ . '/../views/about.php';
    }

}
