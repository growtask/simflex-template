class Accordion {
    constructor(selector = '.accordion') {
        this.accordionItems = document.querySelectorAll(selector);
        this.init();
    }

    init() {
        this.accordionItems.forEach(item => {
            const toggleButton = item.querySelector('.accordion__button');
            const content = item.querySelector('.accordion__content');

            item.addEventListener('click', (e) => this.handleClick(e, item, toggleButton, content));
        });
    }

    handleClick(event, item, toggleButton, content) {
        if (!content.contains(event.target)) {
            const isActive = item.classList.contains('active');

            item.classList.toggle('active', !isActive);
            toggleButton.classList.toggle('active', !isActive);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const accordion = new Accordion();
});
