<?php

class Controller_Base extends Controller_Template
{
    public $template = 'layout_auth';
    protected $user_id;

    public function before()
    {
        parent::before();

        if (!Auth::check()) {
            return Response::redirect('auth/login');
        }

        list(, $this->user_id) = Auth::get_user_id();

        $this->template->header = View::forge('partial/header');
    }
}
