"use strict";

const utils = {};
let header = document.querySelector('.header');

utils.addCloseEventOnParent = (e) => {
    e.currentTarget.parentNode.style.display = 'none';
}

utils.closeTarget = (e, target) => {
    return target.classList.style.display = 'none';
}

utils.getHost = () => {
    return data.host;
}

utils.slugify = text =>
    text
        .toString()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .trim()
        .replace(/\s+/g, '-')
        .replace(/[^\w-]+/g, '')
        .replace(/--+/g, '-')

utils.ajax = (link, method = 'GET', body = null) => {
    return fetch(link, {
        method: method,
        body: body,
        headers : {
            Accept: "*/*"
        }
    })
        .then((response) => {
            if (response.ok) {
                return response.json();
            } else {
                return response.json();
            }
        })
        .then((data) => {
            return data;
        })
        .catch(function (error) {
            console.log('error', error);
            return error;
        });
}

utils.store = {
    state: {
    },
    addKey(key, value = true) {
        this.state[key] = value;
    },
    getKey(key) {
        if ('undefined' == typeof key) {
            return  null;
        }
        return this.state[key] ?? null;
    }
}

utils.addFlash = (message, type = 'success') => {
    let flash;
    if (type && 'danger' === type) {
        flash = `
                    <div class="flash flash-danger ajax-flash" role="alert">
                        ${message}
                        <button class="flash-close"></button>
                    </div>
                `
        flash = document.createRange().createContextualFragment(flash);
    } else {
        flash = `
                    <div class="flash flash-success ajax-flash" role="alert">
                        ${message}
                        <button class="flash-close"></button>
                    </div>
                `
        flash = document.createRange().createContextualFragment(flash);
    }

    this.setFLashEvent(flash);
    header.appendChild(flash);
}

utils.setFLashEvent = (flash) => {
    if (flash.querySelectorAll('.flash-close').length > 0) {
        let button = flash.querySelectorAll('.flash-close')[0];
        button.addEventListener('click', utils.addCloseEventOnParent);
    }

}

export {utils};