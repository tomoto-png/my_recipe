<?php

class Controller_Shopping extends Controller_Base
{

	public function action_index()
	{
		list(, $user_id) = Auth::get_user_id();
		$shopping_items = Model_Shopping_List_Item::find_by_user($user_id);
		$this->template->title = '買い物リスト一覧';
		$this->template->content = View::forge('shopping/index')->set_safe('shopping_items', $shopping_items);
	}


	public function post_create()
	{
		$val = Validation::forge();

		$val->add('name', '材料名')
			->add_rule('required')
			->add_rule('max_length', 100);

		$val->add('quantity', '分量')
			->add_rule('max_length', 50);

		$val->set_message('required', ':label は必須です');
		$val->set_message('max_length', ':label は :param:1 文字以内で入力してください');

		$raw = file_get_contents('php://input');
		$data = json_decode($raw, true);

		if (! $val->run($data)) {
			foreach ($val->error() as $key => $error) {
				$errors[$key] = [$error->get_message()];
			}
			return Response::forge(
				json_encode([
					'status' => 'error',
					'errors' => $errors
				]),
				422,
				['Content-Type' => 'application/json']
			);
		}

		$token = \Input::headers('X-CSRF-Token');

		if (! \Security::check_token($token)) {
			return \Response::forge(
				json_encode([
					'status' => 'error',
					'errors' => ['不正なリクエストです'],
				]),
				403,
				['Content-Type' => 'application/json']
			);
		}


		try {
			DB::start_transaction();
			$now = date('Y-m-d H:i:s');
			list(, $user_id) = Auth::get_user_id();
			Model_Shopping_List_Item::create(
				[
					'name' => $data['name'],
					'quantity' => $data['quantity'] !== '' ? $data['quantity'] : null,
					'created_at' => $now,
					'updated_at' => $now
				],
				$user_id,
				$now
			);
			DB::commit_transaction();
		} catch (Exception $e) {

			DB::rollback_transaction();

			return Response::forge(
				json_encode([
					'status' => 'error',
					'errors' => ['登録中にエラーが発生しました']
				]),
				500,
				['Content-Type' => 'application/json']
			);
		}

		return Response::forge(
			json_encode(['status' => 'ok']),
			200,
			['Content-Type' => 'application/json']
		);
	}

	public function post_add()
	{
		// CSRFチェック
		if (! Security::check_token()) {
			throw new HttpBadRequestException();
		}
		$recipe_id = Input::post('recipe_id');

		$ingredients = Model_Recipe_Ingredient::find_by_recipe_id($recipe_id);

		if (! $ingredients) {
			Session::set_flash('error', '材料がありません');
			return Response::redirect_back();
		}

		$ingredient_ids = array_column($ingredients, 'id');
		list(, $user_id) = Auth::get_user_id();
		$now = date('Y-m-d H:i:s');

		Model_Shopping_List_Item::add_by_ingredients($ingredient_ids, $user_id, $now);

		Session::set_flash('message', '買い物リストに追加しました');
		return Response::redirect_back();
	}

	public function post_update($id)
	{
		if (! Security::check_token()) {
			throw new HttpBadRequestException();
		}

		list(, $user_id) = Auth::get_user_id();

		$checked = Input::post('checked') ? 1 : 0;

		Model_Shopping_List_Item::update_checked($id, $user_id, $checked);

		return Response::redirect_back();
	}

	public function post_delete($id)
	{
		// CSRFチェック
		if (! Security::check_token()) {
			throw new HttpBadRequestException();
		}

		list(, $user_id) = Auth::get_user_id();
		Model_Shopping_List_Item::delete_by_id_and_user($id, $user_id);

		Session::set_flash('message', '正常に削除しました');
		return Response::redirect_back();
	}
}
