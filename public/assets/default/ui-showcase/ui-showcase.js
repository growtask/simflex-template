/* Витрина UI-компонентов (Extensions/Content/tpl/ui.tpl): поведение страницы на Alpine.js. Подключается до alpine.min.js.
 * Страницу целиком рисует PHP; здесь только поиск по меню, тема страницы, вкладки тем и подсветка раздела
 * в «На странице». */
document.addEventListener('alpine:init', () => {
    window.Alpine.data('uiShowcase', () => ({
        // тема страницы (светлая или тёмная), хранится в браузере
        theme: 'light',
        // что показывать в вариантах: both, light, dark
        mode: 'both',
        query: '',
        active: '',

        init() {
            try {
                this.theme = localStorage.getItem('sfShowcaseTheme') === 'dark' ? 'dark' : 'light';
                this.mode = localStorage.getItem('sfShowcaseMode') || 'both';
            } catch (e) {
                // хранилище недоступно - остаются значения по умолчанию
            }
            this.observeSections();
        },

        setTheme(theme) {
            this.theme = theme;
            this.store('sfShowcaseTheme', theme);
        },

        setMode(mode) {
            this.mode = mode;
            this.store('sfShowcaseMode', mode);
        },

        store(key, value) {
            try {
                localStorage.setItem(key, value);
            } catch (e) {
                // не критично
            }
        },

        // пункт меню виден, если в названии или группе есть строка поиска
        matches(text) {
            const query = this.query.trim().toLowerCase();
            return query === '' || text.toLowerCase().includes(query);
        },

        // группа видна, если подходит хотя бы один её пункт
        anyMatch(texts) {
            return texts.some((text) => this.matches(text));
        },

        observeSections() {
            const sections = document.querySelectorAll('[data-docs-section]');
            if (!sections.length || !('IntersectionObserver' in window)) {
                return;
            }
            this.active = sections[0].id;
            const observer = new IntersectionObserver((entries) => {
                for (const entry of entries) {
                    if (entry.isIntersecting) {
                        this.active = entry.target.id;
                    }
                }
            }, {rootMargin: '0px 0px -70% 0px'});
            sections.forEach((section) => observer.observe(section));
        },
    }));
});
