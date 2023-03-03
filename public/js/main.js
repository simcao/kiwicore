/*
 *
 * This file is part of the Kiwicore package.
 *
 * (c) Simcao EI <dev@simcao.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 *  2023
 */

/**
 * User toogle menu
 *
 * @param e
 */
document.getElementById("toogle-menu").onclick = function(e){
    const element = document.getElementsByClassName("username-popup")[0];
    e.preventDefault();
    element.style.visibility = element.style.visibility === "visible" ? "hidden" : "visible";
}

/**
 * Modal confirmation
 *
 * https://jsfiddle.net/ojvf4uac/
 *
 * @type {HTMLElement}
 */
const confirmationModal = document.getElementById("confirmationModal");
const confirmationModalTrigger = document.getElementById("confirmationModalTrigger");

if(confirmationModalTrigger != null)
{
    confirmationModalTrigger.onclick = function () {
        let confirmationButton = confirmationModal.getElementsByClassName("btn-success")[0];

        confirmationButton.setAttribute('href', confirmationModal.getAttribute("data-confirmedPath"));
        confirmationModal.style.display = "block";
    }

    window.onclick = function(event) {
        if (event.target === confirmationModal) {
            confirmationModal.style.display = "none";
            console.log('hello');
        }
    }
}

/**
 * Close toast button
 *
 * @type {Element}
 */
const toast = document.getElementsByClassName("toast")[0];
const toastCloseButton = document.getElementsByClassName("toast-close")[0];

if (toast != null && toastCloseButton != null) {
    toastCloseButton.onclick = function () {
        toast.style.display = "none";
    }
}

/**
 * Photo slider
 */

function slider() {
    const images = document.querySelectorAll('.image-slider img');
    const prevButton = document.querySelector('.image-slider .prev');
    const nextButton = document.querySelector('.image-slider .next');
    const intervalTime = 5000;
    let index = 0;

    function changeImage() {
        const currentImage = images[index];
        currentImage.style.opacity = 0;
        index = (index + 1) % images.length;
        const nextImage = images[index];
        nextImage.style.opacity = 1;
    }

    function previousImage() {
        const currentImage = images[index];
        currentImage.style.opacity = 0;
        index = (index - 1 + images.length) % images.length;
        const previousImage = images[index];
        previousImage.style.opacity = 1;
    }

    function nextImage() {
        const currentImage = images[index];
        currentImage.style.opacity = 0;
        index = (index + 1) % images.length;
        const nextImage = images[index];
        nextImage.style.opacity = 1;
    }

    prevButton.addEventListener('click', previousImage);
    nextButton.addEventListener('click', nextImage);

    setInterval(changeImage, intervalTime);
}
slider();

