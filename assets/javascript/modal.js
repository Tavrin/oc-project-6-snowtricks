"use strict";
import {utils} from "./utils.js";
import {ModalError} from "./exceptions/ModalError.js";

class Modal {
    constructor(elem) {
        this.elem = elem;
        this.button = {
            target: null,
            initialized: false
        };
        this.target = null;
        this.id = null;
        this.data = {
            title: null,
            content: null
        }
        this.type = null;
        this.galleryView = null;
        this.formView = null;
        this.open = {
            "form" : false
        };

        this.loaded = {
            'gallery' : false,
            'form' : false
        };
    }

    init() {
        if (this.elem.dataset.type) {
            this.type = this.elem.dataset.type;
        } else {
            throw new ModalError('no modal type');
        }
        if (this.elem.dataset.targetId) {
            this.id = this.elem.dataset.targetId;
            this.target =document.querySelector('#' + this.elem.dataset.targetId);
            this.button.target = this.target.querySelector('#closeModalButton');

            this.addListeners();
        } else {
            throw new ModalError('no target modal id');
        }

        this.data.title = this.target.querySelector('#modal-title');
        this.data.content = this.target.querySelector('#modal-content');
    }

    addListeners() {
        if (!utils.store.getKey(this.id+'-button')) {
            this.button.target.addEventListener('click', () => {
                this.target.classList.toggle('d-n');
            })

            utils.store.addKey(this.id+'-button');
        }

        this.elem.addEventListener('click', (e) => {
            if ('confirm' === this.type) {
                e.preventDefault();
            }

            if (this.target) {
                this.target.classList.toggle('d-n');

                console.log(utils.store.getKey(this.id+'-type'));
                if (this.type !== utils.store.getKey(this.id+'-type')) {
                    utils.store.addKey(this.id+'-type', this.type);
                    this.resetModal();
                } else if (null === utils.store.getKey(this.id+'-type')){
                    utils.store.addKey(this.id+'-type', this.type);
                }

                if (false === utils.store.getKey(this.id+'-initiated')) {
                    this.setModalData();
                }
            }
        })
    }

    resetModal() {
        this.data.title.innerHTML = '';
        this.data.content.innerHTML = '';
        utils.store.addKey(this.id+'-initiated', false);
    }

    setModalData() {
        if ('confirm' === this.type) {
            this.setConfirm();
        }
        if ('image' === this.type || 'video' === this.type) {
            let galleryView = document.createElement('div');
            galleryView.id = 'gallery-view';
            let formView = document.createElement('div');
            formView.id = 'form-view';
            let ajaxStatus = document.createElement('p');
            ajaxStatus.id = 'ajaxStatus';
            ajaxStatus.innerText = 'chargement...';
            galleryView.appendChild(ajaxStatus);
            ajaxStatus.id = 'ajaxStatusNew';
            formView.appendChild(ajaxStatus);
            this.data.content.appendChild(galleryView);
            this.data.content.appendChild(formView);
            this.galleryView = this.target.querySelector('#gallery-view');
            this.formView = this.target.querySelector('#form-view');
            if ('image' === this.type) {
                this.addMediaToGallery();
            }
            if ('video' === this.type) {
                this.setVideo();
            }
        }

        utils.store.addKey(this.id+'-initiated', true);
    }

    setConfirm() {
        this.data.title.innerHTML = 'Confirmation de suppression';
        let cancelButton = document.createElement('button');
        cancelButton.id = 'modalCancelButton';
        cancelButton.innerText = 'Annuler';
        cancelButton.addEventListener('click', () => {
            this.target.classList.toggle('d-n');
        })
        this.data.content.appendChild(cancelButton);
        let confirmButton = document.createElement('button');
        confirmButton.innerText = 'Confirmer';
        confirmButton.id = 'modalConfirmButton';
        confirmButton.addEventListener('click', () => {
            window.location.href = this.url;
        })
        this.url = this.elem.href;
        this.data.content.appendChild(confirmButton);

    }

    setVideo() {
        this.data.title.innerHTML = 'Galerie vidéo';
        let slug = null;
        if (this.elem.dataset.slug) {
            slug = this.elem.dataset.slug;
        } else if (metadata) {
            slug = metadata.slug ?? null ;
        }
        utils.ajax(`/api/tricks/${slug}/videos`).then(data => {
            if (this.target.querySelector('#ajaxStatus')) {
                document.querySelector('#ajaxStatus').style.display = 'none';
            }
            data.response.forEach(e => {
                console.log(data.response);
                let containerItem = document.createElement('div');
                containerItem.innerHTML = `<iframe width="560" height="315" src="https://www.youtube.com/embed/watch?v=arzLq-47QFA"
                                            title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write;
                                             encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
                this.galleryView.appendChild(containerItem);
            })
        })
    }

    addMediaToGallery() {
        if ('image' === this.type) {
            this.data.title.innerHTML = 'Galerie d\'images';
            let newImage = this.target.querySelector('#addImageButton');
            newImage.addEventListener('click', (e) => {this.modalButtonAddImage(e)});

                utils.ajax('/api/media/images').then(data => {
                    if (this.target.querySelector('#ajaxStatus')) {
                        document.querySelector('#ajaxStatus').style.display = 'none';
                    }
                    console.log(data);
                    data.response.forEach(element => {
                        console.log(element);
                        let containerItem = document.createElement('div');
                        let imageInfo = document.createElement('div');
                        imageInfo.innerHTML = `
                            <p>${element.name}</p>
                            <button class="button-bb-wc modal-media-button" data-path="${element['file']}" >Sélectionner</button>
                            `;
                        containerItem.classList = 'maw-22 d-f p-1 fd-c ai-c jc-sb';
                        let item = document.createElement('img');
                        item.src = element['file'];
                        item.classList = 'maw-100 of-cov';
                        containerItem.appendChild(item);
                        containerItem.appendChild(imageInfo);
                        this.galleryView.appendChild(containerItem);
                    })

                    document.querySelectorAll('.modal-media-button').forEach((e) => {
                        e.addEventListener('click', () => {this.modalButtonGetMedias(e)})
                    });
                    this.loaded.gallery = true;
                });
        }
    }

    modalButtonAddImage(e = null) {
        if (!this.open.form) {
            e.currentTarget.textContent = 'Retour';
        } else if (e) {
            e.currentTarget.textContent = 'Ajouter une image';
        }

        this.galleryView.classList.toggle('d-n');
        if (false === this.loaded.form) {
            let iframe = document.createElement('iframe');
            iframe.src = '/modal/media/images/new'
            iframe.classList.add('iframe')
            this.loaded.form = true;
            this.formView.appendChild(iframe);
        }

        this.formView.classList.toggle('d-n');
        if (true === this.open.form) {
            this.addMediaToGallery();
        }
        this.open.form = !this.open.form ;
    }

    createNewMedia(e, form) {
        const formData = new FormData(form);
        utils.ajax('/api/medias/new', 'POST', formData).then(data => {
            if ('ok' === data.response) {
                this.loaded.gallery = false;
                this.galleryView.innerHTML = '';
                let button = this.target.querySelector('#addImageButton');
                let event = new Event('click');
                button.dispatchEvent(event);
            }
        });
    }

    modalButtonGetMedias(e) {
        this.target.classList.toggle('d-n');
        let inputTarget = document.querySelector('#trick_form_mainMedia');
        let divShow = document.querySelector('#mediaShow');
        if (divShow.classList.contains('d-n')) {
            divShow.classList.remove('d-n');
        }
        inputTarget.value = e.dataset.path;
        let event = new Event('change');
        inputTarget.dispatchEvent(event);
    }
}

document.querySelectorAll('.js-modal').forEach((element) => {
    let modal = new Modal(element);
    modal.init();
})