import {utils} from "./utils.js";
import CommentResponse from "./CommentResponse";
import ToggleContent from './toggle-content';

let commentsLoad = document.querySelector('#js-loadComments');

if (commentsLoad) {
    commentsLoad.addEventListener('click', (e) => {
        e.preventDefault();
        let container = document.querySelector('#comments-zone')
        let count = e.currentTarget.dataset.currentCount;
        let trickId = e.currentTarget.dataset.trickId;
        let target = e.currentTarget;
        let shimmers = setShimmers(container);

        getComments(container, count, trickId, target, null, 0, shimmers);

    })
}

let getComments = (container, count, trickId, target, commentId = null, level = 0, shimmers = null) => {
    let request = `/api/comments?count=${count}&id=${trickId}`;
    if (commentId) {
        request += `&commentId=${commentId}`;
    } else {
        level = 0;
    }

    utils.ajax(request).then(data => {

        if (shimmers) {
            Array.prototype.forEach.call( shimmers, function( node ) {
                node.parentNode.removeChild( node );
            });
        }

        if (500 === data.status) {
            utils.addFlash('Une erreur est survenue', 'danger')
            return;
        }

        data.response['comments'].forEach((comment) => {
            let commentContainer = setCommentItem(comment, container, level);
            level++;
            getComments(commentContainer, count, trickId, target, comment.id, level)
            });

        target.dataset.currentCount = (document.querySelectorAll('.comment-item').length - 1).toString();

        if (data.response['totalCount'] <= document.querySelectorAll('.comment-item').length) {
            target.style.display = 'none';
        }
    })
}

let setCommentItem = (comment, container, level) => {
    let commentItem = `
                    <div class="comment-item ${level % 2 === 0 ? 'answers-even': ''}">
                        <div class="comment-item-body">
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
                        <div id="answers-${comment.id}">
                        </div>
                    </div>
                `

    commentItem = document.createRange().createContextualFragment(commentItem);
    let body = commentItem.querySelector('.comment-item-body');

    body.addEventListener('mouseover', (e) => {
        e.stopPropagation();
        body.parentNode.style.borderLeft = 'solid 2px #5DCBFA';
    })
    body.addEventListener('mouseleave', (e) => {
        body.parentNode.style.borderLeft = '';
    })
    let response = commentItem.querySelector('.comment-response');
    container.appendChild(commentItem);
    new ToggleContent(response);
    response = new CommentResponse(response);
    response.setListener();

    return container.querySelector('#answers-'+comment.id);
};

const setShimmers = (container, count = 5) => {
    for (let i = 0; i < count; i++ ) {
        let commentItem = `
                    <div class="comment-item-shimmer ">
                        <div class="comment-content-shimmer shimmer"></div>
                        <div class="comment-metadata-shimmer-container">
                            <span class="comment-metadata-shimmer shimmer"></span>
                            <span class="comment-answer-shimmer shimmer"></span>
                        </div>
                    </div>
                `

        commentItem = document.createRange().createContextualFragment(commentItem);
        container.appendChild(commentItem);
    }

    return container.querySelectorAll('.comment-item-shimmer');
}