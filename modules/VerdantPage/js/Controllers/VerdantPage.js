import {Ajax} from "../../../Core/js/ajax";
import {pageManager} from "../../../Core/js/pageManager";
import {ConfigurableWysiwygEditor, LineBreak, Paragraph} from "configurable-wysiwyg";
import {t} from "../../i18n.xml";

export class show {
    constructor(page, data, initInfo) {
        this.data = data;
        this.page = page;

        page.querySelector('.editBtn')?.addEventListener('click', () => {
            this.startEdit();
        });
        this.article = page.querySelector('article');
        this.pageId = this.article.dataset.id

        this.page.querySelector('.addNotebookButton')?.addEventListener('click', async () => {
            const name = prompt(t('ribbon.notebook.add.enterName'));
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

        if ('edit' in data._query || sessionStorage.isEditEnabled == '1') {
            this.startEdit();
        }
    }

    startEdit() {
        if (this.editStarted) {
            return;
        }
        this.editStarted = true;
        console.log('startEdit');
        if (this.article.dataset.canEdit == '1') {

        }
        for (const nodeElement of this.article.querySelectorAll('[data-node]')) {
            this.initNodeEdit(nodeElement);
        }
        const newTextNode = new ConfigurableWysiwygEditor();
        newTextNode.classList.add('newTextNode');
        newTextNode.placeholder = t('page.clickToAddText');
        newTextNode.onblur = async () => {
            if (newTextNode.getXmlDom().documentElement.children.length > 0) {
                let needRedirect = false;
                if (!this.pageId) {
                    await this.createNewPage();
                    needRedirect = true;
                }

                const node = {
                    node_id: crypto.randomUUID(),
                    verdant_page_node_version_id: null,
                    type: 'text/xml+configurable-wysiwyg',
                    content: {
                        xml: newTextNode.getXml()
                    }
                }
                const promise = Ajax.VerdantPage.updateNode(node.node_id, null, node.type, node.content, this.pageId, (getServerDate() / 1000) *(1<<16));
                if (needRedirect) {
                    await promise;
                    pageManager.goto(`/VerdantPage/show/${this.pageId}?edit`);
                } else {
                    const nodeElement = document.createElement('section');
                    nodeElement.dataset.node = JSON.stringify(node);
                    nodeElement.textContent = node.content.text;
                    this.initNodeEdit(nodeElement);
                    this.article.insertBefore(nodeElement, newTextNode);
                }
            }
            newTextNode.clear();
        }
        this.article.append(newTextNode);
        const editBtn = this.page.querySelector('.editBtn');
        if (editBtn) {
            editBtn.classList.add('active');
            editBtn.disabled = true;
        }
        const titleElement = this.page.querySelector('article>h1')
        console.log('titleElement', titleElement)
        titleElement.contentEditable = 'plaintext-only';
        if (titleElement.textContent.trim().length == 0) {
            titleElement.classList.add('placeholder');
            titleElement.textContent = t('page.clickToAddTitle');
        }
        titleElement.onfocus = () => {
            if (titleElement.classList.contains('placeholder')) {
                titleElement.classList.remove('placeholder');
                titleElement.textContent = '';
            }
        }
        titleElement.oninput = () => {
            document.querySelectorAll('aside .asidePageLink[data-id="' + this.pageId + '"]')?.forEach(el => el.textContent = titleElement.textContent || t('page.untitled'));
        }
        titleElement.onblur = async () => {
            if (titleElement.textContent.trim().length > 0 && titleElement.textContent != this.data.title) {
                let needRedirect = false;
                if (!this.pageId) {
                    await this.createNewPage();
                    needRedirect = true;
                }
                const promise = Ajax.VerdantPage.updateTitle(this.pageId, titleElement.textContent);
                if (needRedirect) {
                    await promise;
                    pageManager.goto(`/VerdantPage/show/${this.pageId}?edit`);
                }
            }
            if (titleElement.textContent.trim().length == 0) {
                titleElement.classList.add('placeholder');
                titleElement.textContent = t('page.clickToAddTitle');
            }
        }
        sessionStorage.isEditEnabled = '1';
    }

    initNodeEdit(nodeElement) {
        const node = JSON.parse(nodeElement.dataset.node);
        if (node.type == 'text/plain') {
            nodeElement.contentEditable = true;
            nodeElement.onchange = () => {
                console.log('changed', nodeElement.textContent);
                nodeElement.textContent = nodeElement.textContent;
            }
            nodeElement.oninput = () => {
                const {carret, text} = this.getPlainTextSelection(nodeElement);
                console.log('input', nodeElement.textContent);
                nodeElement.textContent = nodeElement.textContent;
                const selection = window.getSelection();
                selection.removeAllRanges();
                const range = document.createRange();
                range.setStart(nodeElement.firstChild, carret);
                range.setEnd(nodeElement.firstChild, carret);
                selection.addRange(range);
            }
            nodeElement.onblur = () => {
                console.log({node})
                if (node.content.text != nodeElement.textContent) {
                    node.content.text = nodeElement.textContent;
                    Ajax.VerdantPage.updateNode(node.node_id, node.verdant_page_node_version_id, node.type, node.content, this.pageId);
                }
            }
            nodeElement.onkeydown = e => {
                if ((e.key == 'Backspace' || e.key == 'Delete') && nodeElement.textContent.length == 0) {
                    e.preventDefault();
                    nodeElement.remove();
                    Ajax.VerdantPage.deleteNode(node.node_id);
                }
            }
            this.generateSortGrabber(nodeElement);
        } else if (node.type == 'text/xml+configurable-wysiwyg') {
            const editor = new ConfigurableWysiwygEditor();
            editor.addAllowedElement(new Paragraph());
            editor.addAllowedElement(new LineBreak());

            editor.onblur = () => {
                console.log({node})
                if (node.content.xml != editor.getXml()) {
                    node.content.xml = editor.getXml();
                    Ajax.VerdantPage.updateNode(node.node_id, node.verdant_page_node_version_id, node.type, node.content, this.pageId);
                }
            }
            editor.onkeydown = e => {
                if ((e.key == 'Backspace' || e.key == 'Delete') && editor.textContent.length == 0) {
                    e.preventDefault();
                    nodeElement.remove();
                    Ajax.VerdantPage.deleteNode(node.node_id);
                }
            }
            while (nodeElement.firstChild) {
                nodeElement.removeChild(nodeElement.firstChild);
            }

            this.generateSortGrabber(nodeElement);
            nodeElement.append(editor);
            editor.loadXml(node.content.xml);
        }
    }

    getPlainTextSelection(node) {
        let carret = null;
        let text = '';
        if (node instanceof Text) {
            text = node.textContent;
            const selection = window.getSelection();
            if (selection.rangeCount > 0) {
                const range = selection.getRangeAt(0);
                if (range.startContainer == node) {
                    carret = range.startOffset;
                }
            }
        } else if (node instanceof Element) {
            for (const child of node.childNodes) {
                const {text: childText, carret: childCarret} = this.getPlainTextSelection(child);
                text += childText;
                if (childCarret !== null) {
                    carret = text.length - childText.length + childCarret;
                }
            }
        }
        return {text, carret};
    }

    async createNewPage() {
        this.pageId = crypto.randomUUID();
        await Ajax.VerdantPage.createNewPage(this.pageId);
        if (!this.data.isUserLoggedIn) {
            const warning = document.createElement('div');
            warning.classList.add('articleWarning');
            warning.textContent = 'You are not logged in. Changes will be saved after you log in or create an account.';
            this.page.insertBefore(warning, this.page.querySelector('article'));
        }
    }
    generateSortGrabber(nodeElement){
        const sortGrabber = document.createElement('div');
        sortGrabber.classList.add('sortGrabber');
        nodeElement.prepend(sortGrabber);
        sortGrabber.addEventListener('pointerdown', eDown => {
            console.log('pointerdown', eDown);
            const moveHandler = e => {
                console.log('pointermove', e);
                nodeElement.style.position = 'relative';
                nodeElement.style.left = 0;
                nodeElement.style.top = e.clientY - eDown.clientY + 'px';
            }
            const upHandler = e => {
                console.log('pointerup', e);
                document.removeEventListener('pointermove', moveHandler);
                document.removeEventListener('pointerup', upHandler);
                nodeElement.style.position = '';
                nodeElement.style.left = '';
                nodeElement.style.top = '';
                const comparedNodes = [...nodeElement.parentNode.children].map(el => ({
                    diff: el.getBoundingClientRect().top - nodeElement.getBoundingClientRect().top - e.clientY + eDown.clientY,
                    el
                }))
                console.log('comparedNodes', comparedNodes)
                const closestNode = comparedNodes.reduce((prev, curr) => Math.abs(curr.diff) < Math.abs(prev.diff) ? curr : prev);
                console.log('closestNode', closestNode)
                if (closestNode.el != nodeElement) {
                    if (closestNode.diff < 0) {
                        closestNode.el.parentNode.insertBefore(nodeElement, closestNode.el.nextSibling);
                    } else {
                        closestNode.el.parentNode.insertBefore(nodeElement, closestNode.el);
                    }
                    const prevSort=nodeElement.previousElementSibling?.dataset.node ? JSON.parse(nodeElement.previousElementSibling.dataset.node).sortOrder : 0;
                    const nextSort=nodeElement.nextElementSibling?.dataset.node ? JSON.parse(nodeElement.nextElementSibling.dataset.node).sortOrder : (getServerDate() / 1000) *(1<<16);
                    console.log('prevSort',prevSort,'nextSort',nextSort)
                    const newSortOrder = (prevSort + nextSort) / 2;
                    const node = JSON.parse(nodeElement.dataset.node);
                    node.sortOrder = newSortOrder;
                    nodeElement.dataset.node = JSON.stringify(node);
                    Ajax.VerdantPage.updateNode(node.node_id, node.verdant_page_node_version_id, node.type, node.content, this.pageId, newSortOrder);
                }
            }
            document.addEventListener('pointermove', moveHandler);
            document.addEventListener('pointerup', upHandler);
        });
    }
}
