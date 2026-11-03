import {Ajax} from "../../../Core/js/ajax";

export class show {
    constructor(page, data) {
        page.querySelector('.editBtn')?.addEventListener('click', () => {
            this.startEdit();
        });
        this.article = page.querySelector('article');
        this.pageId = this.article.dataset.id

    }

    startEdit() {
        if (this.article.dataset.canEdit == '1') {

        }
        for (const nodeElement of this.article.querySelectorAll('[data-node]')) {
            this.initNodeEdit(nodeElement);
        }
        const newPlainTextNode = document.createElement('section');
        newPlainTextNode.classList.add('newPlainTextNode');
        newPlainTextNode.contentEditable = true;
        newPlainTextNode.textContent = 'Click here to add a new text'
        newPlainTextNode.onfocus = () => {
            newPlainTextNode.textContent = '';
        }
        newPlainTextNode.onblur = async () => {
            if (newPlainTextNode.textContent.trim().length > 0) {
                if(!this.pageId){
                    await this.createNewPage();
                }

                const node = {
                    node_id: crypto.randomUUID(),
                    verdant_page_node_version_id: null,
                    type: 'text/plain',
                    content: {
                        text: newPlainTextNode.textContent
                    }
                }
                Ajax.VerdantPage.updateNode(node.node_id, null, node.type, node.content, this.pageId);
                const nodeElement = document.createElement('section');
                nodeElement.dataset.node = JSON.stringify(node);
                nodeElement.textContent= node.content.text;
                this.initNodeEdit(nodeElement);
                this.article.insertBefore(nodeElement, newPlainTextNode);
            }
            newPlainTextNode.textContent = 'Click here to add a new text';
        }
        this.article.append(newPlainTextNode);
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
        this.pageId=crypto.randomUUID();
        await Ajax.VerdantPage.createNewPage(this.pageId);
    }
}
