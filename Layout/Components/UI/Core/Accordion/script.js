/**
 * @typedef {Object} AccordionOptions
 * @property {string} [selector='.accordion'] - CSS selector for the accordion container.
 * @property {string} [buttonClass='accordion__button'] - Class name for the accordion button.
 * @property {string} [contentClass='accordion__content'] - Class name for the accordion content.
 * @property {string} [activeClass='active'] - Class name for the active state of the accordion item.
 * @property {boolean} [multipleOpen=false] - Whether to allow multiple items to be open simultaneously.
 */
class Accordion {
    /**
     * @param {AccordionOptions} [options={}]
     */
    constructor(options = {}) {
        const defaults = {
            selector: '.accordion',
            buttonClass: 'accordion__button',
            contentClass: 'accordion__content',
            activeClass: 'active',
            multipleOpen: false
        };

        this.options = { ...defaults, ...options };
        this.accordionItems = document.querySelectorAll(this.options.selector);
        this.init();
    }

    init() {
        if (!this.accordionItems.length) {
            console.warn(`No accordion items found with selector: ${this.options.selector}`);
            return;
        }

        this.accordionItems.forEach(item => {
            const toggleButton = item.querySelector(`.${this.options.buttonClass}`);
            const content = item.querySelector(`.${this.options.contentClass}`);

            if (!toggleButton || !content) {
                console.warn('Accordion item missing required elements:', item);
                return;
            }

            item.addEventListener('click', (e) => this.handleClick(e, item, toggleButton, content));
        });
    }

    handleClick(event, item, toggleButton, content) {
        // Only toggle if clicking outside content area
        if (!content.contains(event.target)) {
            event.preventDefault();

            if (!this.options.multipleOpen) {
                this.closeOthers(item);
            }

            const isActive = item.classList.contains(this.options.activeClass);
            this.toggleState(item, toggleButton, !isActive);
        }
    }

    /**
     * Closes all other accordion items
     * @param {HTMLElement} exceptItem - Item to exclude from closing
     */
    closeOthers(exceptItem) {
        this.accordionItems.forEach(item => {
            if (item !== exceptItem && item.classList.contains(this.options.activeClass)) {
                const button = item.querySelector(`.${this.options.buttonClass}`);
                this.toggleState(item, button, false);
            }
        });
    }

    /**
     * Toggles the state of an accordion item
     * @param {HTMLElement} item
     * @param {HTMLElement} button
     * @param {boolean} active
     */
    toggleState(item, button, active) {
        item.classList.toggle(this.options.activeClass, active);
        button.classList.toggle(this.options.activeClass, active);
    }
}

// Advanced usage with options
document.addEventListener('DOMContentLoaded', () => {
    const accordion = new Accordion({
        multipleOpen: false,
    });
});