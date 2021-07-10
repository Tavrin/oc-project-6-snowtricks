import {utils} from "./utils.js";
let tricksLoad = document.querySelector('#js-loadTricks');

if (tricksLoad) {
    tricksLoad.addEventListener('click', (e) => {
        e.preventDefault();

        let count = document.querySelectorAll('.trick-item').length;

        utils.ajax(`/api/tricks/?count=${count}`).then(data => {
            if (500 === data.status) {
                utils.addFlash('Une erreur est survenue', 'danger')
                return;
            }

            let container = document.querySelector('#tricks-listing')

            data.response['tricks'].forEach((trick) => {
                let trickItem = `
                    <div class="trick-item">
                        <div class="featured-item-image">
                            <a href="/tricks/${trick.slug}"><img src="${trick['mainMedia']}" alt="" class="listing_media"></a>
                        </div>
                        <span class="trick-item-info"><a href="/tricks/${trick.slug}">${trick.name}</a></span>
                    </div>
                `

                trickItem = document.createRange().createContextualFragment(trickItem);
                container.appendChild(trickItem);
            })

            if (document.querySelectorAll('.trick-item').length >= tricksLoad.dataset.totalcount) {
                tricksLoad.style.display = 'none';
            }
            console.log(data.response);
        });
    })
}
