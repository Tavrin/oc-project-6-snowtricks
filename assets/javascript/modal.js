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
            this.target = document.querySelector('#' + this.elem.dataset.targetId);
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
        this.loaded.form = false;
        this.open.form = false;
        utils.store.addKey(this.id+'-initiated', false);
        let modal = this.target.querySelector('.modal');
        if (modal.querySelector('#addMediaButton')) {
            modal.querySelector('#addMediaButton').remove();
        }
    }

    setModalData() {
        if ('confirm' === this.type) {
            this.setConfirm();
        }
        if ('image' === this.type || 'video' === this.type) {
            this.data.content.innerHTML =
                `
                <div id="gallery-view" class="modal-media-gallery-container">
                    <p id="ajaxStatus">chargement...</p>
                </div>
                <div id="form-view" class="d-n">
                    <p id="ajaxStatusNew">chargement...</p>
                </div>
                `
            ;
            let modal = this.target.querySelector('.modal');
            let newMediaButton = document.createElement('button');
            newMediaButton.id = 'addMediaButton';
            newMediaButton.innerText = `Ajouter une ${this.type}`;
            modal.insertBefore(newMediaButton, modal.children[2]);

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
        let slug = this.getTrickSlug();

        utils.ajax(`/api/tricks/${slug}/videos`).then(data => {
            if (this.target.querySelector('#ajaxStatus')) {
                document.querySelector('#ajaxStatus').style.display = 'none';
            }
            data.response.forEach(e => {
                let containerItem = document.createElement('div');
                containerItem.innerHTML =
                    `
                    <iframe width="560" height="315" src="${e.url}"
                    title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write;
                    encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    `
                ;
                this.galleryView.appendChild(containerItem);
            })
        })
    }

    addImageToContainer(item, container, type = 'add') {
        if ('add' === type) {
            type = `<button class="button-bb-wc modal-media-button-add" data-id="${item['id']}" >Ajouter à la figure</button>`;
        } else if('remove' === type) {
            type =
                `
                <button class="button-bb-wc modal-media-button-main" data-path="${item['file']}">Sélectionner comme média principal</button>
                <button class="button-bb-wc modal-media-button-remove" data-id="${item['id']}" >Supprimer de la figure</button>
                `
            ;
        }
        let containerItem = document.createElement('div');
        containerItem.innerHTML =
            `
            <img src="${item['file']}">
            <div class="modal-image-info">
                <p>${item.name}</p>
                ${type}
                <button class="button-bb-wc modal-media-button-delete" data-id="${item['id']}" >Modifier</button>
            </div>
            `
        ;

        containerItem.classList.add('modal-image-item');
        container.appendChild(containerItem);
    }

    addMediaToGallery() {
        if ('image' === this.type) {
            this.galleryView.innerHTML +=
                `
                <h3>Images associées à la figure</h3>
                <div id="trickImages" class="modal-media-gallery">
                </div>
                <hr>
                <h3>Autres images disponibles</h3>
                <div id="otherImages" class="modal-media-gallery">
                </div>
                `
            ;

            let trickImages = this.target.querySelector('#trickImages');
            let otherImages = this.target.querySelector('#otherImages');
            let slug = this.getTrickSlug();

            this.data.title.innerHTML = 'Galerie d\'images';
            this.target.querySelector('#addMediaButton').addEventListener('click', (e) => {this.modalButtonAddImage(e)});
            utils.ajax(`/api/tricks/${slug}/images`, 'GET').then(data => {
                if (this.target.querySelector('#ajaxStatus')) {
                    document.querySelector('#ajaxStatus').remove();
                }
                data.response.forEach(element => {
                    this.addImageToContainer(element, trickImages, 'remove');
                })

                console.log(trickImages.innerHTML);
                if (trickImages.children.length === 0) {
                    console.log('test');
                    trickImages.innerHTML =
                        `
                        <p>Aucune image associée à la figure</p>   
                        `
                }

                this.target.querySelectorAll('.modal-media-button-remove').forEach((e) => {
                    e.addEventListener('click', () => {this.removeMediaFromTrick(e)})
                });
            });

            utils.ajax(`/api/media/images?excluded-tricks=${slug}`).then(data => {

                data.response.forEach(element => {
                    this.addImageToContainer(element, otherImages);
                })

                this.target.querySelectorAll('.modal-media-button-main').forEach((e) => {
                    e.addEventListener('click', () => {this.setMainMedia(e)})
                });
                this.target.querySelectorAll('.modal-media-button-add').forEach((e) => {
                    e.addEventListener('click', () => {this.addMediaToTrick(e)})
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

        if (true === this.open.form) {
            this.resetModal();
            this.setModalData();
            return;
        }

        this.galleryView.style.display = this.galleryView.style.display === 'none' ? '' : 'none';

        if (false === this.loaded.form) {
            utils.ajax(`/api/media/images/new`).then(data => {
                if (200 === data.status) {
                    let form = document.createRange().createContextualFragment(data.response);
                    this.formView.appendChild(form);
                    this.loaded.form = true;
                    if (this.target.querySelector('#ajaxStatusNew')) {
                        document.querySelector('#ajaxStatusNew').style.display = 'none';
                    }
                    this.formView.querySelector('form').addEventListener('submit', (e) => this.createNewMedia(e))
                }
            })
        }

        this.formView.classList.toggle('d-n');

        this.open.form = !this.open.form ;
    }

    createNewMedia(e) {
        e.preventDefault();
        let form = this.formView.querySelector('form')
        const formData = new FormData(form);
        utils.ajax('/api/media/images/new', 'POST', formData).then(data => {
            if (201 === data.status) {
                this.resetModal();
                this.setModalData();
            }
        });
    }

    getTrickSlug()
    {
        let slug = null;
        if (this.elem.dataset.slug) {
            slug = this.elem.dataset.slug;
        } else if (metadata) {
            slug = metadata.slug ?? null ;
        }

        return slug;
    }

    removeMediaFromTrick(e) {
        let slug = this.getTrickSlug();
        utils.ajax(`/api/tricks/${slug}/images`, 'DELETE', JSON.stringify({id: e.dataset.id})).then(data => {
            if (200 !== data.status) {
                alert(data.response);
            } else {
                this.resetModal();
                this.setModalData();
            }
        })
    }

    addMediaToTrick(e) {
        let slug = this.getTrickSlug();
        utils.ajax(`/api/tricks/${slug}/images`, 'POST', JSON.stringify({id: e.dataset.id})).then(data => {
            if (201 !== data.status) {
                alert(data.response);
            } else {
                this.resetModal();
                this.setModalData();
            }
        })

    }

    setMainMedia(e) {
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