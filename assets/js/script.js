// assets/js/script.js

document.addEventListener('DOMContentLoaded', function() {
    // Animation pour les cartes d'énigmes
    const enigmaCards = document.querySelectorAll('.enigma-card');
    enigmaCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = `all 0.5s ease ${index * 0.1}s`;
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100);
    });

    // Gestion de l'affichage des indices
    const showHintButtons = document.querySelectorAll('.show-hint');
    showHintButtons.forEach(button => {
        button.addEventListener('click', function() {
            const hint = this.nextElementSibling;
            hint.style.display = hint.style.display === 'none' ? 'block' : 'none';
        });
    });

    // Confirmation avant soumission pour la dernière énigme
    const finalForm = document.querySelector('#enigme5 form');
    if (finalForm) {
        finalForm.addEventListener('submit', function(e) {
            if (!confirm('Êtes-vous sûr de votre réponse ? Ceci est la dernière énigme.')) {
                e.preventDefault();
            }
        });
    }

    // Affichage d'un message de félicitations si le jeu est terminé
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('completed') === '1') {
        setTimeout(() => {
            alert('Félicitations ! Vous avez sauvé le laboratoire !');
        }, 500);
    }

    // Gestion du mode sombre/nuit pour le laboratoire
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function() {
            document.body.classList.toggle('night-mode');
            localStorage.setItem('darkMode', document.body.classList.contains('night-mode'));
        });

        // Charger la préférence utilisateur
        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('night-mode');
        }
    }

    // Animation pour la barre de progression
    const progressBar = document.querySelector('.progress-bar');
    if (progressBar) {
        setTimeout(() => {
            progressBar.style.width = progressBar.style.width;
        }, 300);
    }
});

// Fonction pour afficher un indice après un certain temps
function showTimeBasedHint() {
    setTimeout(() => {
        const hint = document.querySelector('.time-based-hint');
        if (hint) {
            hint.style.display = 'block';
            hint.style.animation = 'fadeIn 1s';
        }
    }, 300000); // 5 minutes
}

// Initialiser l'indice basé sur le temps
showTimeBasedHint();