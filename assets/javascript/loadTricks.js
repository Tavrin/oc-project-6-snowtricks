import {utils} from "./utils.js";
let tricksLoad = document.querySelector('#js-loadTricks');

if (tricksLoad) {
    tricksLoad.addEventListener('click', (e) => {
        e.preventDefault();

        let count = document.querySelectorAll('.trick-item').length;
        let container = document.querySelector('#tricks-listing');
        let shimmers = setShimmers(container);
        utils.ajax(`/api/tricks/?count=${count}`).then(data => {
            Array.prototype.forEach.call( shimmers, function( node ) {
                node.parentNode.removeChild( node );
            });

            if (500 === data.status) {
                utils.addFlash('Une erreur est survenue', 'danger')
                return;
            }

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
            if (document.querySelectorAll('.trick-item').length >= 15) {
                document.querySelector('.landing-zone-arrow-up').classList.remove('d-n');
            }
            console.log(data.response);
        });
    })
}

const setShimmers = (container, count = 5) => {
    for (let i = 0; i < count; i++ ) {
        let trickItem = `
                    <div class="trick-item-shimmer">
                        <div class="featured-item-image-shimmer shimmer">
                        </div>
                        <span class="trick-item-info-shimmer shimmer"></span>
                    </div>
                `

        trickItem = document.createRange().createContextualFragment(trickItem);
        container.appendChild(trickItem);
    }

    return container.querySelectorAll('.trick-item-shimmer');
}
