class TextInput {
    constructor(element) {
        this.input = element.querySelector('.text-input__field');
        this.resetButton = element.querySelector('.text-input__reset');

        if (this.resetButton) {
            this.resetButton.addEventListener('click', () => this.handleReset());
        }
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