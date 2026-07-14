document.addEventListener('DOMContentLoaded', () => {
    const numericInputs = document.querySelectorAll('input[data-format="numeric"]');

    numericInputs.forEach(input => {
        // Change to text to allow separators and improve mobile UX
        input.type = 'text';
        input.setAttribute('inputmode', 'numeric');

        const format = (value) => {
            if (!value) return '';
            // Remove all non-digit characters
            const raw = value.toString().replace(/\D/g, '');
            if (raw === '') return '';
            // Format with Indonesian standard for thousand separators
            return new Intl.NumberFormat('id-ID').format(raw);
        };

        // Format the initial value on page load (for edit forms)
        input.value = format(input.value);

        // Add event listener to format value as user types
        input.addEventListener('input', () => {
            input.value = format(input.value);
        });

        // Find the parent form to intercept submission
        const form = input.closest('form');
        if (form) {
            form.addEventListener('submit', () => {
                // Before submitting, remove formatting to send a clean number to the server
                const rawValue = input.value.replace(/\D/g, '');
                input.value = rawValue;
            });
        }
    });
});