import {utils} from "./utils.js";
import CommentResponse from "./CommentResponse";
import ToggleContent from './toggle-content';

let commentsLoad = document.querySelector('#js-loadComments');

if (commentsLoad) {
    commentsLoad.addEventListener('click', (e) => {
        e.preventDefault();

        let count = e.currentTarget.dataset.currentCount;
        let trickId = e.currentTarget.dataset.trickId;
        let target = e.currentTarget;
        utils.ajax(`/api/comments?count=${count}&id=${trickId}`).then(data => {
            if (500 === data.status) {
                utils.addFlash('Une erreur est survenue', 'danger')
                return;
            }

            let container = document.querySelector('#comments-zone')
            console.log(target);
            data.response['comments'].forEach((comment) => {
                let commentItem = `
                    <div class="comment-item answers-even">
                        <div class="w-100 d-f mb-1-5">
                            <div>
                                <span class="fw-900 d-b mb-0-5"><a class="comment-username" href="${comment.user? '/users/'+comment.user : '#'}">${comment.user ?? 'utilisateur supprimé'}</a></span>
                            </div>
                        </div>
                        <p class="ta-l w-100">${ comment.status === true ? comment.content : '<span class="text-muted fw-900 bcg-light2 p-0-5 br-25">Commentaire en attente de modération</span>' }</p>
                        <div class="comment-item-metadata">
                            <span class="comment-item-metadata-date-info">Posté le <span class="comment-item-metadata-date">${comment.createdAt}</span></span>
                            <span style="text-align: right" class="js-toggle comment-response" data-pid="${comment.id}" data-target-id="response-${comment.id}" data-type="display">Répondre</span>
                        </div>
                        <div class="d-n" id="response-${comment.id}">
                        </div>
                    </div>
                `

                commentItem = document.createRange().createContextualFragment(commentItem);
                let response = commentItem.querySelector('.comment-response');
                container.appendChild(commentItem);
                new ToggleContent(response);
                response = new CommentResponse(response);
                response.setListener();

            });

            target.dataset.currentCount = data.response['count'];
            console.log(data.response);
        });
    })
}
