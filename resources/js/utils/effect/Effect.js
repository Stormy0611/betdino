export default class Effect {

    create() {
        throw new Error('create() method is not implemented');
    }

    createCanvas(insertAfter = null, contextId = '2d', insertAfterEnd = true, width = 142, height = 41) {
        const canvas = document.createElement('canvas');
        canvas.style.position = 'absolute';
        canvas.style.top = `0`;
        canvas.style.left = `0`;
        canvas.style.pointerEvents = 'none';

        const context = canvas.getContext(contextId);

        const data = {
            canvas: canvas,
            context: context
        };

        canvas.width = width;
        canvas.height = height;

        const element = insertAfter == null ? document.querySelector('#engineContainer') : insertAfter;
        element.insertAdjacentElement(insertAfterEnd ? 'afterend' : 'beforebegin', canvas);

        return data;
    }

}
