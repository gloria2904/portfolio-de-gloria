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
// ANIMATION DES CARTES DE COMPÉTENCES AU SCROLL
// ============================================

const skillsSection = document.querySelector('#skills');

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
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
// FORMULAIRE DE CONTACT FONCTIONNEL
// ============================================

const contactForm = document.querySelector('form');

if (contactForm) {
  contactForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Récupérer les valeurs du formulaire
    const formData = {
      name: contactForm.querySelector('input[type="text"]').value.trim(),
      email: contactForm.querySelector('input[type="email"]').value.trim(),
      message: contactForm.querySelector('textarea').value.trim()
    };
    
    // Validation côté client
    if (!formData.name || !formData.email || !formData.message) {
      showNotification(
        currentLang === 'fr' 
          ? '❌ Veuillez remplir tous les champs.' 
          : '❌ Please fill in all fields.',
        'error'
      );
      return;
    }
    
    if (!isValidEmail(formData.email)) {
      showNotification(
        currentLang === 'fr'
          ? '❌ Veuillez entrer un email valide'
          : '❌ Please enter a valid email',
        'error'
      );
      return;
    }
    
    // Désactiver le bouton pendant l'envoi
    const submitBtn = contactForm.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = currentLang === 'fr' ? 'Envoi...' : 'Sending...';
    
    try {
      const response = await fetch('api/submit-contact.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
      });
      
      const result = await response.json();
      
      if (result.success) {
        showNotification(
          currentLang === 'fr' 
            ? '✅ Message envoyé avec succès ! Je vous répondrai bientôt.' 
            : '✅ Message sent successfully! I will reply soon.',
          'success'
        );
        contactForm.reset();
      } else {
        showNotification(
          result.message || (currentLang === 'fr' ? 'Erreur lors de l\'envoi' : 'Error sending message'),
          'error'
        );
      }
    } catch (error) {
      console.error('Erreur:', error);
      showNotification(
        currentLang === 'fr' 
          ? '❌ Erreur de connexion. Veuillez réessayer.' 
          : '❌ Connection error. Please try again.',
        'error'
      );
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = originalBtnText;
    }
  });
}

// ============================================
// FONCTIONS UTILITAIRES
// ============================================

// Validation email
function isValidEmail(email) {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
}

// Système de notifications
function showNotification(message, type = 'info') {
  // Supprimer les notifications existantes
  const existingNotif = document.querySelector('.notification');
  if (existingNotif) {
    existingNotif.remove();
  }
  
  // Créer la notification
  const notification = document.createElement('div');
  notification.className = `notification notification-${type}`;
  notification.textContent = message;
  
  // Ajouter les styles
  notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 16px 24px;
    border-radius: 8px;
    color: white;
    font-weight: 500;
    z-index: 10000;
    animation: slideIn 0.3s ease;
    max-width: 400px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  `;
  
  // Couleurs selon le type
  if (type === 'success') {
    notification.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
  } else if (type === 'error') {
    notification.style.background = 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
  } else {
    notification.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
  }
  
  // Ajouter au DOM
  document.body.appendChild(notification);
  
  // Supprimer après 5 secondes
  setTimeout(() => {
    notification.style.animation = 'slideOut 0.3s ease';
    setTimeout(() => notification.remove(), 300);
  }, 5000);
}

// Ajouter les animations CSS pour les notifications
const style = document.createElement('style');
style.textContent = `
  @keyframes slideIn {
    from {
      transform: translateX(400px);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
  
  @keyframes slideOut {
    from {
      transform: translateX(0);
      opacity: 1;
    }
    to {
      transform: translateX(400px);
      opacity: 0;
    }
  }
`;
document.head.appendChild(style);

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