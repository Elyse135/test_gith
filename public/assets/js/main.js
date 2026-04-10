const facts = [
  [
    { title: 'Design éditorial', text: 'Une interface avec du relief, des contrastes francs et une lecture immédiate.' },
    { title: 'Navigation simple', text: 'Un parcours court avec des ancres claires pour aller directement à l’essentiel.' },
    { title: 'Déploiement facile', text: 'Un dossier public qui peut être publié sans outil de build ni configuration lourde.' },
  ],
  [
    { title: 'Rapide à personnaliser', text: 'Les textes, couleurs et sections se modifient en quelques minutes.' },
    { title: 'Léger et stable', text: 'Aucune bibliothèque externe côté script, donc peu de risques au déploiement.' },
    { title: 'Compatible mobile', text: 'La mise en page s’adapte aux écrans étroits avec une structure responsive.' },
  ],
];

const factCards = document.getElementById('factCards');
const shuffleButton = document.getElementById('shuffleFacts');
const year = document.getElementById('year');

let activeSet = 0;

function renderFacts(index) {
  factCards.innerHTML = facts[index]
    .map((fact) => `
      <article class="info-card">
        <h3>${fact.title}</h3>
        <p>${fact.text}</p>
      </article>
    `)
    .join('');
}

shuffleButton.addEventListener('click', () => {
  activeSet = activeSet === 0 ? 1 : 0;
  renderFacts(activeSet);
});

year.textContent = new Date().getFullYear();
renderFacts(activeSet);