"use strict";

const utils = {};

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
    console.log(body);
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
                return Promise.reject({
                    status: response.status,
                    statusText: response.statusText
                });
            }
        })
        .then((data) => {
            return data;
        })
        .catch(function (error) {
            console.log('error', error);
        });
}

utils.addFlash = (alert) => {
    console.log(alert);
    if (alert.querySelectorAll('.flash-close').length > 0) {
        let button = alert.querySelectorAll('.flash-close')[0];
        button.addEventListener('click', utils.addCloseEventOnParent);
    }
}

export {utils};