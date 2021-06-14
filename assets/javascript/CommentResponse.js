"use strict";

import {utils} from "./utils.js";

export default class CommentResponse {
    constructor(elem) {
        this.elem = elem;
        this.target = elem.dataset.targetId ?? null;
        this.params = {
            pid: elem.dataset.pid ?? null,
            trickId: metadata.id ?? null
        }
        this.state = false;
    }

    setListener() {
        this.elem.addEventListener('click', (e) => {
            if (false === this.state) {
                utils.ajax('/api/comments/new', 'POST', JSON.stringify(this.params)).then(data => {
                    let target = document.querySelector(`#${this.target}`);
                    let test = document.createRange().createContextualFragment(data.data);
                    target.appendChild(test);
                    let form =  target.querySelector('form');
                    if (null !== form) {
                        form.addEventListener('submit', (e) => {this.setSubmitEvent(e, form)})
                    }

                    this.state = true;
                })
            }
        })
    }

    setSubmitEvent(e, form) {
        e.preventDefault();
        let formData = new FormData(form);
        utils.ajax('/api/comments/new', 'POST', formData).then(data => {
            if(data['0'] === 201) {
                location.reload();
            }
        })
    }
}