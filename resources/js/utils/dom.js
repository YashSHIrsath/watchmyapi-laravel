/**
 * Generic DOM finding utility.
 */
export const $ = (selector) => document.querySelector(selector);
export const $$ = (selector) => document.querySelectorAll(selector);

/**
 * Event listener helper.
 */
export const on = (element, event, handler) => {
    if (element) {
        element.addEventListener(event, handler);
    }
};
