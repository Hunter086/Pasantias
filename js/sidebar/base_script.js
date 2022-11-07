$(document).ready(function(){
const navbarToggle = document.getElementById('navbar-toggle');
const navbar = document.getElementById('navbar')

navbarToggle.addEventListener('click', e => {
    navbar.classList.toggle('active')
})

document.addEventListener('click', e => {

    
    
    const isDropdownButton = e.target.matches("[data-dropdown-button]")
    const isModalButton = e.target.matches("[data-modal-button]")
    const isModalDismiss = e.target.matches("[data-modal-dismiss]")

    if(isModalDismiss) {
        document.querySelectorAll("[data-modal]").forEach(modal => {
            modal.classList.remove("active")
        })
    }

    if (!isDropdownButton && e.target.closest("[data-dropdown]") != null) return
    if (!isModalButton && e.target.closest("[data-modal]") != null) return

    let currentDropdown
    if (isDropdownButton){
        currentDropdown = e.target.closest("[data-dropdown]")
        currentDropdown.classList.toggle("active")
    }
    let currentModal
    if (isModalButton){
        reference = e.target.getAttribute("href").slice(1)
        currentModal = document.getElementById(reference)
        /* if(window.screen.availWidth > 900){
            centerAbsoluteElement(currentModal.querySelector(".modal-content"))
        } else {
            fullscreenModal(currentModal.querySelector(".modal-content"))
        } */
        currentModal.classList.toggle("active")
    }
    document.querySelectorAll("[data-dropdown]").forEach(dropdown => {
        if(dropdown === currentDropdown) return
        dropdown.classList.remove("active")
    })
})

setTimeout(function () {
// Closing the alert
    $(".content-alert").animate({right: '-40%'},function() {
        $(".content-alert").css("display", "none");
    });
    }, 20000);
/* 
function centerAbsoluteElement(element){
    element.setAttribute('style',`left: calc(50vw - (${element.offsetWidth}px / 2)); top: calc(50vh - (${element.offsetHeight}px / 2))`)
    console.log("big screen")
}

function fullscreenModal(element){
    element.setAttribute('style','position: relative; width: 80%; margin:2rem; top: 0; left: 0')
    console.log("smol screen")
}
 */
});