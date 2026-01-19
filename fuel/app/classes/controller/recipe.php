<?php

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
		$category_options = ['' => 'すべて'] + Model_Category::find_all();

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
		$this->template->title = 'レシピ登録';

		$this->template->content = View::forge('recipe/create', ['category_options' => Model_Category::find_all()]);
	}
	public function post_create()
	{
		// CSRFチェック
		if (! \Security::check_token()) {
			throw new \HttpBadRequestException();
		}

		[$errors, $clean_ingredients, $clean_steps]
			= Service_Recipe_Form::validate(true);

		//エラーがあればフォームに戻す
		if (! empty($errors)) {
			$this->template->content = View::forge('recipe/create', [
				'errors' => $errors,
				'category_options' => Model_Category::find_all(),
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
			Model_Recipe_Ingredient::insertIngredients(
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

			Session::set_flash('error', '登録に失敗しました');
			return Response::redirect('recipe/create');
		}

		return Response::redirect('recipe/index');
	}

	public function action_edit($id)
	{
		list(, $user_id) = Auth::get_user_id();

		// レシピ取得
		$recipe = Model_Recipe::find_or_fail($id, $user_id);
		$ingredients = Model_Recipe_Ingredient::find_by_recipe_id($id);
		$steps = Model_Recipe_Step::find_by_recipe_id($id);

		$this->template->content = View::forge('recipe/edit', [
			'recipe' => $recipe,
			'ingredients' => $ingredients,
			'steps' => $steps,
			'category_options' => Model_Category::find_all(),
		]);
	}

	public function post_edit($id)
	{
		// CSRFチェック
		if (!\Security::check_token()) {
			throw new \HttpBadRequestException();
		}

		list(, $user_id) = Auth::get_user_id();
		$recipe = Model_Recipe::find_or_fail($id, $user_id);

		[$errors, $clean_ingredients, $clean_steps]
			= Service_Recipe_Form::validate(false);

		//エラーがあればフォームに戻す
		if (! empty($errors)) {
			$this->template->content = View::forge('recipe/edit', [
				'errors' => $errors,
				'recipe' => $recipe,
				'category_options' => Model_Category::find_all(),
				'ingredients' => $clean_ingredients,
				'steps' => $clean_steps,
			]);
			return;
		}

		try {
			$now = date('Y-m-d H:i:s');

			$old_image_path = $recipe['image_path'];

			$image_path = $old_image_path;

			\DB::start_transaction();

			if ($_FILES['image_path']['error'] !== UPLOAD_ERR_NO_FILE) {
				Upload::save();
				$files = Upload::get_files();
				if (empty($files)) {
					throw new \Exception('画像の保存に失敗しました');
				}
				$file = Upload::get_files()[0];
				$image_path = 'uploads/recipes/' . $file['saved_as'];
			}

			Model_Recipe::update(
				$id,
				$user_id,
				[
					'title' => Input::post('title'),
					'category_id' => Input::post('category'),
					'image_path' => $image_path,
					'updated_at' => $now,
				]
			);

			\DB::commit_transaction();

			if ($image_path !== $old_image_path && file_exists(DOCROOT . $old_image_path)) {
				\File::delete(DOCROOT . $old_image_path);
			}

			Model_Recipe_Ingredient::update($id, $clean_ingredients, $now);
			Model_Recipe_Step::update($id, $clean_steps, $now);
		} catch (\Exception $e) {

			\DB::rollback_transaction();

			// ★ 新しくアップした画像だけ削除
			if ($image_path !== $old_image_path && file_exists(DOCROOT . $image_path)) {
				\File::delete(DOCROOT . $image_path);
			}

			Session::set_flash('error', '更新に失敗しました');
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
