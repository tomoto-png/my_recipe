<?php

use Fuel\Core\Model;
use Fuel\Core\Response;

class Controller_Recipe extends Controller_Base
{
	public function action_index()
	{
		list(, $user_id) = Auth::get_user_id();
		$params = [
			'keyword'     => Input::get('keyword'),
			'category_id' => Input::get('category_id'),
		];

		$recipes = Model_Recipe::find_by_user($user_id, $params);
		$recipe_ids = array_column($recipes, 'id');
		$ingredient_rows = Model_Recipe_Ingredient::find_by_recipe_ids($recipe_ids);
		$category_options = ['' => 'すべて'] + Model_Category::get_list();

		$ingredients_by_recipe = [];
		foreach ($ingredient_rows as $row) {
			$rid = $row['recipe_id'];
			$ingredients_by_recipe[$rid][] = $row['name'];
		}

		$this->template->title = 'レシピ一覧';
		$this->template->content = View::forge('recipe/index', ['recipes' => $recipes, 'ingredients' => $ingredients_by_recipe, 'categories' => $category_options]);
	}

	public function action_view($id)
	{
		list(, $user_id) = Auth::get_user_id();

		$recipe = Model_Recipe::find_by_id($id, $user_id);


		if (empty($recipe)) {
			throw new HttpNotFoundException();
		}

		$ingredients = Model_Recipe_Ingredient::find_by_recipe_id($recipe['id']);
		$steps = Model_Recipe_Step::find_by_recipe_id($recipe['id']);

		$this->template->title = 'レシピ詳細';
		$this->template->content = View::forge('recipe/view', ['recipe' => $recipe, 'ingredients' => $ingredients, 'steps' => $steps]);
	}

	public function action_create()
	{
		//カテゴリーの一覧取得
		$category_options = Model_Category::get_list();

		$this->template->title = 'レシピ登録';

		$this->template->content = View::forge('recipe/create', ['category_options' => $category_options]);
	}
	public function post_create()
	{
		// CSRFチェック
		if (! \Security::check_token()) {
			throw new \HttpBadRequestException();
		}

		$category_options = Model_Category::get_list();

		$errors = [];
		$val = Validation::forge();

		//基本バリデーション
		$val->add('title', 'レシピ名')
			->add_rule('required');

		$val->add('category', 'カテゴリー')
			->add_rule('required')
			->add_rule('numeric_min', 1);

		//メッセージ設定
		$val->set_message('required', ':label は必須です');
		$val->set_message('numeric_min', ':labelを正しく選択してください');

		//画像バリデーション
		if ($_FILES['image_path']['error'] === UPLOAD_ERR_NO_FILE) {
			$errors['image_path'] = '画像を選択してください';
		} else {
			Upload::process([
				'path' => DOCROOT . 'uploads/recipes',
				'randomize' => true,
				'ext_whitelist' => ['jpg', 'jpeg', 'png', 'gif'],
				'max_size' => 2 * 1024 * 1024,
			]);

			if (! Upload::is_valid()) {
				$errors['image_path'] = '画像は jpg / jpeg / png / gif（2MB以下）でアップロードしてください';
			}
		}

		if (! $val->run()) {
			foreach ($val->error() as $key => $error) {
				$errors[$key] = $error->get_message();
			}
		}

		//材料バリデーション
		$ingredients = Input::post('ingredients');
		$clean_ingredients = [];

		// 材料の空行を除去し、有効な材料のみを抽出
		if (is_array($ingredients) && isset($ingredients['name']) && is_array($ingredients['name'])) {
			foreach ($ingredients['name'] as $i => $name) {
				$name = trim($name);
				// 前後の空白を除去してチェック
				if ($name === '') continue;

				$quantity = trim($ingredients['quantity'][$i] ?? '');

				$clean_ingredients[] = [
					'name' => $name,
					'quantity' => $quantity
				];
			}
		}

		if (empty($clean_ingredients)) {
			$errors['ingredients'] = '材料を1つ以上入力してください';
		}

		//手順バリデーション
		$steps = Input::post('steps');
		$clean_steps = [];

		// 手順の空行を除去し、有効な手順のみを抽出
		if (is_array($steps)) {
			foreach ($steps as $step) {
				$step = trim($step);
				// 前後の空白を除去してチェック
				if ($step === '') continue;

				$clean_steps[] = $step;
			}
		}

		if (empty($clean_steps)) {
			$errors['steps'] = '手順を1つ以上入力してください';
		}

		//エラーがあればフォームに戻す
		if (! empty($errors)) {
			$this->template->content = View::forge('recipe/create', [
				'errors' => $errors,
				'category_options' => $category_options,
				'clean_ingredients' => $clean_ingredients,
				'clean_steps' => $clean_steps,
			]);
			return;
		}

		$image_path = null;

		try {
			// 画像の登録処理
			Upload::save();
			$files = Upload::get_files();

			if (empty($files)) {
				throw new \Exception('画像の保存に失敗しました');
			}

			$file = $files[0];
			$image_path = 'uploads/recipes/' . $file['saved_as'];

			// ログインユーザーを取得
			list(, $user_id) = Auth::get_user_id();
			$now = date('Y-m-d H:i:s');

			\DB::start_transaction();

			// レシピ登録
			$recipe_id = Model_Recipe::create([
				'user_id'     => $user_id,
				'title'       => Input::post('title'),
				'category_id' => Input::post('category'),
				'image_path'  => $image_path,
				'created_at'  => $now,
				'updated_at'  => $now,
			]);

			// 材料登録
			Model_Recipe_Ingredient::create(
				$recipe_id,
				$clean_ingredients,
				$now
			);

			// 手順登録
			Model_Recipe_Step::create(
				$recipe_id,
				$clean_steps,
				$now
			);

			\DB::commit_transaction();
		} catch (\Exception $e) {

			// DBを元に戻す
			\DB::rollback_transaction();

			// 画像ファイルが保存されていたら削除
			if ($image_path && file_exists(DOCROOT . $image_path)) {
				\File::delete(DOCROOT . $image_path);
			}

			Session::set_flash('error', '登録中にエラーが発生しました。もう一度お試しください。');
			return Response::redirect('recipe/create');
		}

		return Response::redirect('recipe/index');
	}

	public function post_delete($id)
	{
		// CSRFチェック
		if (! \Security::check_token()) {
			throw new \HttpBadRequestException();
		}
		list(, $user_id) = Auth::get_user_id();

		try {
			Model_Recipe::delete($id, $user_id);
		} catch (\Exception $e) {
			Session::set_flash('error', '削除に失敗しました');
			return Response::redirect_back();
		}

		return Response::redirect('recipe/index');
	}
}
