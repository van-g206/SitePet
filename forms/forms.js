
  const senhaInput = document.getElementById('senha');
  const senhaError = document.getElementById('senha-error');

  senhaInput.addEventListener('blur', () => {
    if (senhaInput.value === '') {
      senhaError.textContent = 'Este campo é obrigatório';
      senhaInput.classList.add('invalid');
    } else {
      senhaError.textContent = '';
      senhaInput.classList.remove('invalid');
    }
  });
