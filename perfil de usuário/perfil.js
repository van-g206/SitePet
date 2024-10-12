const navMenu = document.querySelector('.nav-menu');
const hamburgerMenu = document.querySelector('.hamburger-menu');

hamburgerMenu.addEventListener('click', () => {
    navMenu.classList.toggle('show');
});

function toggleMenu() {
    const menu = document.querySelector('.nav-menu');
    menu.classList.toggle('show');
}