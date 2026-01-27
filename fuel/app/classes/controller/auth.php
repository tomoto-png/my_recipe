<?php

use Fuel\Core\Validation;

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

			foreach ($val->error() as $field => $error) {
				$errors[$field] = $error->get_message();
			}

			return View::forge('auth/login', [
				'errors' => $errors,
			]);
		}

		// フォームの入力値を取得
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
	//新規登録フォームの表示
	public function action_register()
	{
		return View::forge('auth/register');
	}
	//新規登録処理
	public function post_register()
	{
		// CSRFチェック
		if (! Security::check_token()) {
			return Response::forge('不正なリクエストです');
		}

		//バリデーション
		$val = Validation::forge();
		$val->add_callable('Validation_User');

		$val->add('email', 'メールアドレス')
			->add_rule('required')
			->add_rule('valid_email')
			->add_rule('unique_email');

		$val->add('password', 'パスワード')
			->add_rule('required')
			->add_rule('min_length', 8);

		$val->add('password_confirm', 'パスワード（確認）')
			->add_rule('required')
			->add_rule('match_field', 'password');

		// エラーメッセージ定義
		$val->set_message('required', ':label は必須です');
		$val->set_message('valid_email', ':label の形式が正しくありません');
		$val->set_message('min_length', ':label は :param:1 文字以上で入力してください');
		$val->set_message('match_field', ':label が一致しません');
		$val->set_message('unique_email', ':label はすでに使用されています');

		if (! $val->run()) {
			$errors = [];
			foreach ($val->error() as $field => $error) {
				$errors[$field] = $error->get_message();
			}
			return View::forge('auth/register', [
				'errors' => $errors,
			]);
		}

		//フォームの入力値を取得
		$email = Input::post('email');
		$password = Input::post('password');

		//ユーザー登録処理
		try {
			Auth::create_user(
				$email,
				$password,
				$email
			);
		} catch (Exception $e) {
			Session::set_flash('error', '登録に失敗しました');
			return Response::redirect('auth/register');
		}

		//登録後のログイン
		Auth::login($email, $password);
		return Response::redirect('/');
	}
}
