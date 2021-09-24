"use strict";

const FLASH_INFO = 'info';
const FLASH_SUCC = 'success';
const FLASH_ERRO = 'danger';
const FLASH_WARN = 'warning';

/**
 * Move nav bar above the given element. DO NOT USE THIS DIRECTLY.
 * @param {*} ele The given element
 */
function moveMeUp(ele) {
    let target = document.getElementsByTagName("nav")[0];
    if (target) {
        target.after(ele);
    }
}

/**
 * Create an HTML element with the given source string.
 * @param {*} htmlString The source string
 * @returns The HTML element
 */
function createElementFromHTML(htmlString) {
    var div = document.createElement('div');
    div.innerHTML = htmlString.trim();

    return div.firstChild; 
}

/**
 * Add a client-side ONLY flash message.
 * 
 * Available severity: FLASH_INFO, FLASH_SUCC, FLASH_ERRO, FLASH_WARN
 * @param {*} msg The message
 * @param {*} level The severity (Default: FLASH_INFO)
 */
function addFlash(msg, level = FLASH_INFO) {
    var template =
        `<div class="alert alert-${level} alert-client alert-dismissible fade show" role="alert">
            ${msg}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;

    var container = document.getElementById("flash");
    var elem = createElementFromHTML(template);
    new bootstrap.Alert(elem);
    container.appendChild(elem);
}
