// 材料1行分のデータ構造（オブジェクト）」を定義している
// name / quantity は input の初期値（エラー時や編集時に使用）
function Ingredient($name, $quantity) {
    this.name = ko.observable($name || '');
    this.quantity = ko.observable($quantity || '');
}

function Step($description) {
    this.description = ko.observable($description || '');
}

function RecipeFormViewModel() {
    const self = this;

    // 材料
    const initialIngredients = window.initialIngredients || [];

    self.ingredients = ko.observableArray(
        initialIngredients.length
            ? initialIngredients.map(ingredient => new Ingredient(ingredient.name, ingredient.quantity))
            : [new Ingredient()]
    );

    self.addIngredient = function () {
        self.ingredients.push(new Ingredient());
    };

    self.removeIngredient = function (ingredient) {
        // 材料が1行以上ある場合に削除を許可する
        if (self.ingredients().length > 1) {
            self.ingredients.remove(ingredient);
        }
    };

    // 手順
    const initialSteps = window.initialSteps || [];

    self.steps = ko.observableArray(
        initialSteps.length
            ? initialSteps.map(step => new Step(step.description))
            : [new Step()]
    );

    self.addStep = function () {
        self.steps.push(new Step());
    };

    self.removeStep = function (step) {
        if (self.steps().length > 1) {
            self.steps.remove(step);
        }
    };

    //画像
    self.imagePreview = ko.observable(window.initialImagePath ?? null);

    self.triggerFileInput = () => {
        document.getElementById('image_path').click();
    };

    self.onImageChange = (_, event) => {
        const file = event.target.files[0];
        if (!file) return;

        // 画像以外は弾く
        if (!file.type.startsWith('image/')) {
            alert('画像ファイルを選択してください');
            event.target.value = '';
            self.imagePreview(null);
            return;
        }

        const reader = new FileReader();
        reader.onload = e => {
            self.imagePreview(e.target.result);
        };
        reader.readAsDataURL(file);
    };
}

// Knockout を HTML に適用する
// 使用するために必須
ko.applyBindings(
    new RecipeFormViewModel(),
    document.getElementById('recipe-form')
);