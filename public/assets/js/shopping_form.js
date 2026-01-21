function ShoppingViewModel() {
    const self = this;

    self.isModalOpen = ko.observable(false);
    self.name = ko.observable('');
    self.quantity = ko.observable('');

    self.errors = ko.observableArray([]);
    self.fieldErrors = {
        name: ko.observableArray([]),
        quantity: ko.observableArray([])
    };

    self.openModal = function () {
        self.errors([]);
        self.fieldErrors.name([]);
        self.fieldErrors.quantity([]);
        self.isModalOpen(true);
    };

    self.closeModal = function () {
        self.isModalOpen(false);
        self.name('');
        self.quantity('');
    };

    self.submit = function () {
        self.errors([]);
        self.fieldErrors.name([]);
        self.fieldErrors.quantity([]);

        const token = document
            .getElementById('add-item-form')
            .querySelector('input[name="fuel_csrf_token"]').value;

        fetch('/shopping/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': token
            },
            body: JSON.stringify({
                name: self.name(),
                quantity: self.quantity()
            })
        })
            .then(async res => {
                let body;
                try {
                    body = await res.json();
                } catch {
                    throw new Error('Invalid JSON');
                }
                return { status: res.status, body };
            })
            .then(({ status, body }) => {

                if (status === 200 && body.status === 'ok') {
                    self.closeModal();
                    location.reload();
                    return;
                }

                if (body.errors && !Array.isArray(body.errors)) {
                    for (const key in body.errors) {
                        if (self.fieldErrors[key]) {
                            self.fieldErrors[key](body.errors[key]);
                        }
                    }
                    return;
                }

                if (Array.isArray(body.errors)) {
                    self.errors(body.errors);
                }
            })
            .catch(() => {
                self.errors(['通信エラーが発生しました']);
            });
    };
}

ko.applyBindings(
    new ShoppingViewModel(),
    document.getElementById('shopping-form')
);
