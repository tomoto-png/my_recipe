<?php

class Controller_Base extends Controller_Template
{
    public $template = 'template_loggedin';

    public function before()
    {
        parent::before();

        if (!Auth::check()) {
            return Response::redirect('auth/login');
        }

        $this->template->header = View::forge('partial/header');
    }
}
