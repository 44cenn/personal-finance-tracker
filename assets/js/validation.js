document.addEventListener("DOMContentLoaded", function () {
    const forms = document.querySelectorAll(".needs-validation-custom");

    forms.forEach(function (form) {
        form.addEventListener("submit", function (event) {
            let isValid = true;
            let errorMessage = form.dataset.errorRequired || "All required fields must be filled.";

            const requiredInputs = form.querySelectorAll("[data-required='true']");

            requiredInputs.forEach(function (input) {
                if (input.value.trim() === "") {
                    isValid = false;
                    // Pesan error sudah diatur di awal
                }
            });

            const amountInput = form.querySelector("[data-amount='true']");

            if (amountInput) {
                // Un-format the value before validation to handle thousand separators (e.g., "5.000.000")
                const rawValue = amountInput.value.replace(/\D/g, '');
                const amountValue = Number(rawValue);

                if (amountValue <= 0) {
                    isValid = false;
                    errorMessage = form.dataset.errorAmount || "Amount must be greater than 0.";
                }
            }

            if (!isValid) {
                event.preventDefault();
                alert(errorMessage);
            }
        });
    });
});