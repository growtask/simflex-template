class TextInput {
    constructor(element) {
        this.input = element.querySelector('.text-input__field');
        this.resetButton = element.querySelector('.text-input__reset');

        if (this.resetButton) {
            this.resetButton.removeAttribute('aria-hidden');
            this.resetButton.setAttribute('aria-label', 'Clear input');

            this.input.addEventListener('input', () => this.toggleResetButton());
            this.resetButton.addEventListener('click', () => this.handleReset());

            this.toggleResetButton();
        }
    }

    toggleResetButton() {
        const hasValue = this.input.value.length > 0;
        this.resetButton.style.display = hasValue ? 'block' : 'none';
        this.resetButton.setAttribute('aria-hidden', (!hasValue).toString());
    }

    handleReset() {
        this.input.value = '';
        this.input.classList.remove('has-value');
        this.input.dispatchEvent(new Event('input'));
        this.input.focus();
    }
}

document.querySelectorAll('.text-input').forEach(element => {
    new TextInput(element);
});