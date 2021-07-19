/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/reset.css';
import './styles/app.css';
import './styles/responsive.css';

//js
import './javascript/utils';
import './javascript/header';
import './javascript/binder';
import './javascript/filler';
import './javascript/modal';
import './javascript/toggle-content';
import './javascript/CommentResponse';
import './javascript/loadTricks';
import './javascript/loadComments';
import './javascript/search-tricks';
// start the Stimulus application
import './bootstrap';
import CommentResponse from "./javascript/CommentResponse";

Array.from(document.querySelectorAll('.comment-response')).forEach(el => {
    let response = new CommentResponse(el);
    response.setListener();
})

Array.from(document.querySelectorAll('.comment-item-body')).forEach(el => {
    el.addEventListener('mouseover', (e) => {
        e.stopPropagation();
        el.parentNode.style.borderLeft = 'solid 2px #5DCBFA';
    })
    el.addEventListener('mouseleave', (e) => {
        el.parentNode.style.borderLeft = '';
    })
})

let landingTitle = document.querySelector('#site-title');
if (landingTitle) {
    setTimeout(() => {
        landingTitle.style.color = 'black';
    }, 1300)
}

