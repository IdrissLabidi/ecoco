/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)

import 'bootstrap';
import './styles/app.css';
import './styles/command.css';
const $ = require('jquery');
global.$ = global.jQuery = $;

window.addEventListener("scroll", function() {
    var nav = document.querySelector("nav.navbar");
    var container = document.querySelector(".navbar-container");
    var offsetPosition = container.offsetHeight;
    if (window.scrollY > 50) {
        if (!nav.classList.contains("fixed-position")) {
            nav.classList.add("fixed-position");
        }
    } else {
        if (nav.classList.contains("fixed-position")) {
            nav.classList.remove("fixed-position");
        }
    }
});