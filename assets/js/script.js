// ============================================
// CHANGEMENT DE LANGUE FR/EN
// ============================================

let currentLang = 'fr';
const langBtn = document.getElementById('lang-btn');

// Fonction pour changer la langue
function switchLanguage() {
  // Alterner entre FR et EN
  currentLang = currentLang === 'fr' ? 'en' : 'fr';
  
  // Changer le texte du bouton
  langBtn.textContent = currentLang === 'fr' ? 'EN' : 'FR';
  
  // Récupérer tous les éléments avec data-fr et data-en
  const elements = document.querySelectorAll('[data-fr][data-en]');
  
  elements.forEach(element => {
    const frText = element.getAttribute('data-fr');
    const enText = element.getAttribute('data-en');
    
    // Changer le contenu selon la langue
    if (currentLang === 'fr') {
      element.textContent = frText;
    } else {
      element.textContent = enText;
    }
  });
  
  // Changer les placeholders des inputs
  const nameInput = document.querySelector('input[type="text"]');
  const emailInput = document.querySelector('input[type="email"]');
  const textarea = document.querySelector('textarea');
  const submitBtn = document.querySelector('form .btn');
  
  if (currentLang === 'fr') {
    nameInput.placeholder = 'Nom';
    emailInput.placeholder = 'Email';
    textarea.placeholder = 'Message';
    submitBtn.textContent = 'Envoyer';
  } else {
    nameInput.placeholder = 'Name';
    emailInput.placeholder = 'Email';
    textarea.placeholder = 'Message';
    submitBtn.textContent = 'Send';
  }
  
  // Animation du bouton
  langBtn.style.transform = 'scale(0.9)';
  setTimeout(() => {
    langBtn.style.transform = 'scale(1)';
  }, 100);
}

// Écouteur d'événement sur le bouton
langBtn.addEventListener('click', switchLanguage);

// ============================================
// SMOOTH SCROLL POUR LES LIENS DE NAVIGATION
// ============================================

const navLinks = document.querySelectorAll('.navbar nav ul li a');

navLinks.forEach(link => {
  link.addEventListener('click', (e) => {
    e.preventDefault();
    const targetId = link.getAttribute('href');
    const targetSection = document.querySelector(targetId);
    
    if (targetSection) {
      targetSection.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    }
  });
});

// ============================================
// ANIMATION DES BARRES DE COMPÉTENCES AU SCROLL
// (Si tu veux réactiver les barres plus tard)
// ============================================

// Observer pour détecter quand la section skills est visible
const skillsSection = document.querySelector('#skills');

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      // Ajouter une classe pour déclencher l'animation
      const skillCards = document.querySelectorAll('.skill-card');
      skillCards.forEach((card, index) => {
        setTimeout(() => {
          card.style.opacity = '0';
          card.style.transform = 'translateY(30px)';
          
          setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
          }, 50);
        }, index * 100);
      });
      
      observer.unobserve(entry.target);
    }
  });
}, { threshold: 0.2 });

if (skillsSection) {
  observer.observe(skillsSection);
}

// ============================================
// ANIMATION DES CARTES PROJETS AU SCROLL
// ============================================

const projectCards = document.querySelectorAll('.project-card');

const projectObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = '0';
      entry.target.style.transform = 'translateY(30px)';
      
      setTimeout(() => {
        entry.target.style.transition = 'all 0.6s ease';
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }, 100);
      
      projectObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.1 });

projectCards.forEach(card => {
  projectObserver.observe(card);
});

// ============================================
// FORMULAIRE DE CONTACT
// ============================================

const contactForm = document.querySelector('form');

contactForm.addEventListener('submit', (e) => {
  e.preventDefault();
  
  // Récupérer les valeurs
  const name = contactForm.querySelector('input[type="text"]').value;
  const email = contactForm.querySelector('input[type="email"]').value;
  const message = contactForm.querySelector('textarea').value;
  
  // Validation simple
  if (name && email && message) {
    // Message de confirmation
    const confirmMsg = currentLang === 'fr' 
      ? '✅ Message envoyé ! Je vous répondrai bientôt.' 
      : '✅ Message sent! I will reply soon.';
    
    alert(confirmMsg);
    
    // Réinitialiser le formulaire
    contactForm.reset();
  } else {
    const errorMsg = currentLang === 'fr' 
      ? '❌ Veuillez remplir tous les champs.' 
      : '❌ Please fill in all fields.';
    
    alert(errorMsg);
  }
});

// ============================================
// EFFET PARALLAXE SUBTLE SUR LE HERO
// ============================================

window.addEventListener('scroll', () => {
  const hero = document.querySelector('.hero');
  const scrolled = window.pageYOffset;
  
  if (hero && scrolled < window.innerHeight) {
    hero.style.transform = `translateY(${scrolled * 0.5}px)`;
    hero.style.opacity = 1 - (scrolled / window.innerHeight) * 0.5;
  }
});

// ============================================
// NAVBAR CHANGE AU SCROLL
// ============================================

const navbar = document.querySelector('.navbar');

window.addEventListener('scroll', () => {
  if (window.scrollY > 100) {
    navbar.style.background = 'rgba(10, 10, 15, 0.98)';
    navbar.style.boxShadow = '0 5px 20px rgba(0, 217, 255, 0.1)';
  } else {
    navbar.style.background = 'rgba(10, 10, 15, 0.95)';
    navbar.style.boxShadow = 'none';
  }
});