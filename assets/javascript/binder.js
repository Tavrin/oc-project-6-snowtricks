"use strict";

let utils = window.utils;

class Binder {
    constructor(elem) {
        elem.value ? this.value = elem.value : this.value = null;
        elem.dataset.target ? this.target = elem.dataset.target : this.target = null;
        this.attribute = elem.dataset.targetAttribute;
        this.type = elem.dataset.type;
        elem.dataset.options ? this.options = JSON.parse(elem.dataset.options) : this.options = null;
        this.elem = elem;
    }

    init() {
        if (this.target && 'string' === typeof this.target) {
            this.target = document.querySelector(`#${this.target}`);
        }
        if ('text' === this.type || !this.type) {
            this.setTextEvent(this.elem);
        }

        if ('image' === this.type || !this.type) {
            this.setImageEvent(this.elem);
        }
    }

    setTextEvent(elem) {
        elem.addEventListener('keyup', () => {
            this.value = elem.value;
            if (this.options['slugify']) {
                this.value = utils.slugify(this.value);
            }
            this.target.value = this.value;
        })
    }

    setImageEvent(elem) {
        elem.addEventListener('change', (e) => {
            if (elem.dataset.from === 'file') {
                let file    = elem.files[0];
                let reader  = new FileReader();
                reader.onloadend = () => {
                    if (this.target.classList.contains('d-n')) {
                        this.target.classList.remove('d-n');
                    }
                    this.target.src = reader.result;
                }

                if (file) {
                    reader.readAsDataURL(file);
                } else {
                    this.target.src = "";
                }
            } else if(elem.dataset.from === 'modal') {
                this.target.src = elem.value;
                this.target.classList.remove('d-n');
            }

        })
    }
}

document.querySelectorAll('.js-binder').forEach((element) => {
    let binder = new Binder(element);
    binder.init();
})