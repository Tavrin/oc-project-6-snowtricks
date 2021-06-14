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
            let flash;
            let header = document.querySelector('.header');
            if(data['0'] === 201) {
                flash = `
                    <div class="flash flash-success ajax-flash" role="alert">
                        Commentaire rajouté avec succès
                        <button class="flash-close"></button>
                    </div>
                `
                flash = document.createRange().createContextualFragment(flash);
            } else {
                flash = `
                    <div class="flash flash-danger ajax-flash" role="alert">
                        Le commentaire n'a pas pu être ajouté
                        <button class="flash-close"></button>
                    </div>
                `
                flash = document.createRange().createContextualFragment(flash);
            }

            utils.addFlash(flash);
            header.appendChild(flash);

            let message = `
            <div id="" class="comment-item new">
                <div class="w-100 d-f mb-1-5">
                    <div>
                        <span class="fw-900 d-b mb-0-5"><a href="#">${metadata.user}</a></span>
                    </div>
                </div>
                <p class="ta-l w-100">${this.message}</p>
                </div>
            </div>
            `
            message = document.createRange().createContextualFragment(message);

            let container = document.querySelector(`#answers-${this.params.pid}`);
            console.log(container);
            container.insertBefore(message, container.firstChild);
            this.target.remove();
            this.state = false;
        })
    }
}