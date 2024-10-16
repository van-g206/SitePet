// Selecione os elementos do DOM
const navMenu = document.querySelector('.nav-menu');
const hamburgerMenu = document.querySelector('.hamburger-menu');


// Adicione eventos aos elementos
hamburgerMenu.addEventListener('click', () => {
  navMenu.classList.toggle('show');
});

function openMap() {
    const endereco = document.getElementById('endereco').value;
    const url = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(endereco)}`;
    
    // Abre o mapa em uma nova aba
    window.open(url, '_blank');

    // Aguarda 2 segundos e exibe o modal de confirmação
    setTimeout(() => {
      document.getElementById('confirmModal').style.display = 'flex';
    }, 2000); // 2000 ms = 2 segundos
  }

  document.getElementById('confirmYes').onclick = function() {
    // Fecha o modal
    document.getElementById('confirmModal').style.display = 'none';
    alert("Endereço confirmado! Você pode continuar com a adoção.");
  };

  document.getElementById('confirmNo').onclick = function() {
    // Fecha o modal
    document.getElementById('confirmModal').style.display = 'none';
    alert("Por favor, ajuste o endereço antes de continuar.");
  };

  