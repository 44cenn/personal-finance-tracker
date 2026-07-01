document.addEventListener("DOMContentLoaded", function () {
    const forms = document.querySelectorAll(".needs-validation-custom");

    forms.forEach(function (form) {
        form.addEventListener("submit", function (event) {
            let isValid = true;
            let errorMessage = "";

            const requiredInputs = form.querySelectorAll("[data-required='true']");

            requiredInputs.forEach(function (input) {
                if (input.value.trim() === "") {
                    isValid = false;
                    errorMessage = "Semua field wajib harus diisi.";
                }
            });

            const amountInput = form.querySelector("[data-amount='true']");

            if (amountInput) {
                const amountValue = Number(amountInput.value);

                if (amountValue <= 0) {
                    isValid = false;
                    errorMessage = "Jumlah uang harus lebih dari 0.";
                }
            }

            if (!isValid) {
                event.preventDefault();
                alert(errorMessage);
            }
        });
    });
});