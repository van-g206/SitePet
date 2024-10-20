// Função para alternar a exibição das seções
function toggleSection(sectionId) {
    const sections = ['about', 'contact', 'adoption'];
    
    sections.forEach(id => {
        const section = document.getElementById(id);
        
        // Se a seção clicada for a que queremos mostrar
        if (id === sectionId) {
            section.style.display = section.style.display === 'block' ? 'none' : 'block';
        } else {
            section.style.display = 'none'; // Oculta outras seções
        }
    });
}

// Adiciona evento de clique em toda a tela
document.addEventListener('click', function(event) {
    const target = event.target;

    // Verifica se o clique foi em um link da navegação
    if (target.matches('nav a')) {
        event.stopPropagation(); // Impede a propagação para evitar o fechamento imediato
        const sectionId = target.getAttribute('href').substring(1); // Remove o "#" do ID
        toggleSection(sectionId);
    } else {
        // Oculta todas as seções ao clicar fora
        const sections = ['about', 'contact', 'adoption'];
        sections.forEach(id => {
            const section = document.getElementById(id);
            section.style.display = 'none';
        });
    }
});
