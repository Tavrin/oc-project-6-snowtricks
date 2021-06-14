/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/reset.css';
import './styles/app.css';

//js
import './javascript/utils';
import './javascript/header';
import './javascript/binder';
import './javascript/filler';
import './javascript/modal';
import './javascript/toggle-content';
import './javascript/CommentResponse';
// start the Stimulus application
import './bootstrap';
import CommentResponse from "./javascript/CommentResponse";

Array.from(document.querySelectorAll('.comment-response')).forEach(el => {
    let response = new CommentResponse(el);
    response.setListener();
})

