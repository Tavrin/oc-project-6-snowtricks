"use strict";

import {utils} from "./utils.js";

export default class CommentResponse {
    constructor(elem) {
        this.elem = elem;
        this.target = elem.dataset.targetId ?? null;
        this.params = {
            pid: elem.dataset.pid ?? null,
            trickId: metadata.id ?? null
        };
        this.message = '';
        this.state = false;
    }

    setListener() {
        this.target = document.querySelector(`#${this.target}`);
        this.elem.addEventListener('click', (e) => {
            if (false === this.state) {
                utils.ajax('/api/comments/new', 'POST', JSON.stringify(this.params)).then(data => {
                    let test = document.createRange().createContextualFragment(data.data);
                    this.target.appendChild(test);
                    let form =  this.target.querySelector('form');
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
        this.message = formData.get('comment_form[content]');
        utils.ajax('/api/comments/new', 'POST', formData).then(data => {
            if(data['status'] === 201) {
                location.reload();
            } else {
                utils.addFlash('Le commentaire n\'a pas pu être ajouté', 'danger');
            }

            this.state = false;
        })
    }
}