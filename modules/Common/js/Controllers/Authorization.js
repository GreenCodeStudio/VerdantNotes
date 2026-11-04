import {Ajax} from "../../../Core/js/ajax";
import {pageManager} from "../../../Core/js/pageManager";

export class index {
    constructor(page, data) {
        const form = page.querySelector('form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (form.classList.contains('mode-start')) {
                const isAccount = await Ajax.Authorization.usernameExists(form.username.value);
                if (isAccount) {
                    form.classList.remove('mode-start');
                    form.classList.add('mode-login');
                } else {
                    form.classList.remove('mode-start');
                    form.classList.add('mode-register');
                }
            } else if (form.classList.contains('mode-login')) {
                await Ajax.Authorization.login(form.username.value, form.password.value);
                pageManager.goto('/');
            } else if (form.classList.contains('mode-register')) {
                await Ajax.User.register(form.username.value, form.password.value);
                pageManager.goto('/');
            }
        });
    }
}
