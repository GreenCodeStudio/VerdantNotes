import {Ajax} from "../../../Core/js/ajax";
import {pageManager} from "../../../Core/js/pageManager";

export class show {
    constructor(page, data) {
        this.page = page;
        this.page.querySelector('.addNotebookButton')?.addEventListener('click', async () => {
            const name = prompt('Enter notebook name');
            if (name !== null) {
                const id = await Ajax.VerdantNotebook.insert({name});
                pageManager.goto(`/VerdantNotebook/show/${id}`);
            }
        });
        this.page.querySelector('.addPageButton')?.addEventListener('click', async () => {
            const id = crypto.randomUUID();
            await Ajax.VerdantPage.createNewPage(id, this.page.querySelector('article').dataset.notebookId);
            pageManager.goto(`/VerdantPage/show/${id}?edit`);
        });

    }

}
