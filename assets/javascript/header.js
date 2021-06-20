"use strict";

import {utils} from "./utils.js";

class HeaderNav {
    constructor() {
        this.burger = document.querySelector('#userDropdown');
        this.userLinks = document.querySelector('#userDropdownLinks');
    }

    init() {
        document.querySelectorAll('.flash').forEach(utils.addFlash, this);
        this.toggleBurger();
    }

    toggleBurger() {
        if (this.burger) {
            window.addEventListener('mouseup', (e) => {
                if (typeof e.origin === "undefined" || e.origin === utils.getHost()) {
                    if (this.userLinks.classList.contains('active') && !this.burger.contains(e.target)) {
                        this.setBurgerVisuals();
                    }
                }
            })
            this.burger.addEventListener('click', () => {
                this.setBurgerVisuals();
            })
        }
    }

    setBurgerVisuals() {
        this.burger.children[0].classList.toggle('fa-user');
        this.burger.children[0].classList.toggle('fa-times');
        this.userLinks.classList.toggle('active');
    }
}

let header = new HeaderNav();
header.init();
