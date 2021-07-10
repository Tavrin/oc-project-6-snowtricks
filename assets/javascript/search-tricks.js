import {utils} from "./utils";
let searchTricksForm = document.querySelector('#search-tricks-form');

console.log(searchTricksForm);
if (searchTricksForm) {
    searchTricksForm.addEventListener('submit', (e) => {
        e.preventDefault()
        let trickName = searchTricksForm.querySelector('input[name="search_tricks_form[name]"]').value ?? null;
        let groupId = searchTricksForm.querySelector('#search_tricks_form_trickGroup').value ?? null;

        let request = '?count=0&limit=100';
        if (null !== trickName) {
            request += '&trickName=' + trickName;
        }
        if (null !== groupId) {
            request += '&groupId=' + groupId;
        }
        utils.ajax(`/api/tricks/${request}`).then(data => {
            console.log(data);
            if (500 === data.status) {
                utils.addFlash('Une erreur est survenue', 'danger')
                return;
            }

            let container = document.querySelector('#tricks-listing');
            container.innerHTML = '';
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

        });
    })
}