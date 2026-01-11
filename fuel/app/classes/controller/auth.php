<?php

use Fuel\Core\View;

class Controller_Auth extends Controller
{
	//ログインフォームの表示
	public function action_login()
	{
		return View::forge('auth/login');
	}
	//ログイン処理
	public function post_login()
	{
		// CSRFチェック
		if (! Security::check_token()) {
			return Response::forge('不正なリクエストです');
		}

		//バリデーション
		$val = Validation::forge();
		$val->add('email', 'メールアドレス')
			->add_rule('required')
			->add_rule('valid_email');

		$val->add('password', 'パスワード')
			->add_rule('required');

		$val->set_message('required', ':label は必須です');
		$val->set_message('valid_email', ':label の形式が正しくありません');

		if (! $val->run()) {

			$errors = [];

			foreach ($val->error() as $key => $error) {
				$errors[$key] = $error->get_message();
			}

			return View::forge('auth/login', [
				'errors' => $errors,
			]);
		}

		// フォームからの入力値を取得
		$email = Input::post('email');
		$password = Input::post('password');

		//ログイン処理
		if (\Auth::login($email, $password)) {
			return Response::redirect('/');
		}

		//ログイン失敗の処理
		Session::set_flash('error', 'メールアドレスまたはパスワードが違います');
		return Response::redirect('auth/login');
	}
}
