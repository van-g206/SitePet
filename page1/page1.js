// Selecione os elementos do DOM
const navMenu = document.querySelector('.nav-menu');
const hamburgerMenu = document.querySelector('.hamburger-menu');


// Adicione eventos aos elementos
hamburgerMenu.addEventListener('click', () => {
  navMenu.classList.toggle('show');
});

