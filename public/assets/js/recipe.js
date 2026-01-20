document.addEventListener('DOMContentLoaded', () => {
    const clearBtn = document.querySelector('.recipe-search__btn');
    const input = document.querySelector('input[name="keyword"]');

    if (!clearBtn || !input) return;

    // ×ボタンの表示切り替え
    const toggleClearBtn = () => {
        clearBtn.style.display = input.value ? 'block' : 'none';
    };

    toggleClearBtn();
    input.addEventListener('input', toggleClearBtn);

    clearBtn.addEventListener('click', () => {
        input.value = '';
        toggleClearBtn();
        input.form.submit();
    });
});
