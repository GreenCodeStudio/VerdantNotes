import {Ajax} from "../../../Core/js/ajax";

export class show {
    constructor(page, data) {
        page.querySelector('.editBtn')?.addEventListener('click', () => {
            this.startEdit();
        });
        this.article = page.querySelector('article');

    }

    startEdit() {
        if (this.article.dataset.canEdit == '1') {

        }
        for (const nodeElement of this.article.querySelectorAll('[data-node]')) {
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
                        Ajax.VerdantPage.updateNode(node.node_id, node.verdant_page_node_version_id, node.content);
                    }
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
}
