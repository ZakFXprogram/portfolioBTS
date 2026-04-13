/**
 * Portfolio - JavaScript principal
 */

document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    initMobileMenu();
    
    // Clock Update
    initClock();
    
    // Smooth Scroll
    initSmoothScroll();
    
    // Skill bars animation
    initSkillBars();
    
    // Lazy loading images
    initLazyLoading();
});

/**
 * Mobile Menu
 */
function initMobileMenu() {
    const menuBtn = document.querySelector('.mobile-menu-btn');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (menuBtn && mobileMenu) {
        menuBtn.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
            const icon = menuBtn.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-bars');
                icon.classList.toggle('fa-times');
            }
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!mobileMenu.contains(e.target) && !menuBtn.contains(e.target)) {
                mobileMenu.classList.remove('active');
                const icon = menuBtn.querySelector('i');
                if (icon) {
                    icon.classList.add('fa-bars');
                    icon.classList.remove('fa-times');
                }
            }
        });
        
        // Close menu when clicking a link
        const mobileLinks = mobileMenu.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.remove('active');
            });
        });
    }
}

/**
 * Real-time Clock
 */
function initClock() {
    const timeElement = document.getElementById('currentTime');
    const dateElement = document.getElementById('currentDate');
    
    if (timeElement && dateElement) {
        function updateClock() {
            const now = new Date();
            
            // Format time
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            timeElement.textContent = `${hours}:${minutes}:${seconds}`;
            
            // Format date
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            };
            dateElement.textContent = now.toLocaleDateString('fr-FR', options);
        }
        
        // Update immediately and then every second
        updateClock();
        setInterval(updateClock, 1000);
    }
}

/**
 * Smooth Scroll
 */
function initSmoothScroll() {
    const links = document.querySelectorAll('a[href^="#"]');
    
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

/**
 * Skill Bars Animation
 */
function initSkillBars() {
    const skillBars = document.querySelectorAll('.skill-progress');
    
    if (skillBars.length === 0) return;
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const bar = entry.target;
                const width = bar.style.width;
                bar.style.width = '0';
                setTimeout(() => {
                    bar.style.transition = 'width 1s ease-out';
                    bar.style.width = width;
                }, 100);
                observer.unobserve(bar);
            }
        });
    }, { threshold: 0.5 });
    
    skillBars.forEach(bar => observer.observe(bar));
}

/**
 * Lazy Loading Images
 */
function initLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    if (images.length === 0) return;
    
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
}

/**
 * Form Validation Helper
 */
function validateForm(form) {
    const inputs = form.querySelectorAll('input[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('error');
        } else {
            input.classList.remove('error');
        }
    });
    
    return isValid;
}

/**
 * Toast Notification
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => toast.classList.add('show'), 100);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/**
 * Copy to Clipboard
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Copié dans le presse-papiers !', 'success');
    }).catch(() => {
        showToast('Erreur lors de la copie', 'error');
    });
}

/**
 * Debounce Function
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Scroll to Top
 */
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

/**
 * Project Modal
 */
let currentProjectImages = [];
let currentImageIndex = 0;

function openProjectModal(projectId) {
    const project = window.projectsData ? window.projectsData[projectId] : null;
    if (!project) return;
    
    const modal = document.getElementById('projectModal');
    const mainImage = document.getElementById('modalMainImage');
    const title = document.getElementById('modalTitle');
    const description = document.getElementById('modalDescription');
    const technologies = document.getElementById('modalTechnologies');
    const links = document.getElementById('modalLinks');
    const thumbnails = document.getElementById('galleryThumbnails');
    
    // Set project data
    title.textContent = project.title;
    description.innerHTML = project.description.replace(/\n/g, '<br>');
    
    // Set technologies
    technologies.innerHTML = project.technologies.map(tech => 
        `<span class="tag">${tech.trim()}</span>`
    ).join('');
    
    // Set links
    let linksHTML = '';
    if (project.url) {
        linksHTML += `<a href="${project.url}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
            <i class="fas fa-external-link-alt"></i> Visiter le site
        </a>`;
    }
    if (project.github_url) {
        linksHTML += `<a href="${project.github_url}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">
            <i class="fab fa-github"></i> Code source
        </a>`;
    }
    links.innerHTML = linksHTML;

    // Set competence blocks
    const competenceBlocksEl = document.getElementById('modalCompetenceBlocks');
    if (competenceBlocksEl && project.competence_blocks && project.competence_blocks.length > 0) {
        let blocksHTML = '<div class="modal-competence-blocks-inner">';
        project.competence_blocks.forEach(function(block) {
            const color = block.color || '#f97316';
            const icon = block.icon ? `<i class="${block.icon}"></i> ` : '';
            blocksHTML += `<span class="competence-block-badge" style="--block-color: ${color}">${icon}${block.name}</span>`;
        });
        blocksHTML += '</div>';
        if (project.slug) {
            blocksHTML += `<a href="/project/${project.slug}" class="btn-voir-competences">
                <i class="fas fa-graduation-cap"></i> Voir les compétences
            </a>`;
        }
        competenceBlocksEl.innerHTML = blocksHTML;
        competenceBlocksEl.style.display = 'block';
    } else if (competenceBlocksEl) {
        competenceBlocksEl.innerHTML = '';
        competenceBlocksEl.style.display = 'none';
    }
    
    // Set images
    currentProjectImages = project.images || [project.image];
    currentImageIndex = 0;
    
    const basePath = document.querySelector('meta[name="assets-path"]')?.content || '/assets';
    mainImage.src = `${basePath}/images/projects/${currentProjectImages[0]}`;
    mainImage.onerror = function() {
        this.src = `https://via.placeholder.com/800x500/1a1a2e/eee?text=${encodeURIComponent(project.title)}`;
    };
    
    // Set thumbnails
    if (currentProjectImages.length > 1) {
        thumbnails.innerHTML = currentProjectImages.map((img, index) => `
            <img src="${basePath}/images/projects/${img}" 
                 alt="Image ${index + 1}" 
                 class="${index === 0 ? 'active' : ''}"
                 onclick="setImage(${index})"
                 onerror="this.src='https://via.placeholder.com/150x100/1a1a2e/eee?text=${index + 1}'">
        `).join('');
        thumbnails.style.display = 'flex';
    } else {
        thumbnails.style.display = 'none';
    }
    
    // Show/hide nav buttons
    document.querySelector('.gallery-prev').style.display = currentProjectImages.length > 1 ? 'flex' : 'none';
    document.querySelector('.gallery-next').style.display = currentProjectImages.length > 1 ? 'flex' : 'none';
    
    // Show modal
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeProjectModal() {
    const modal = document.getElementById('projectModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

function setImage(index) {
    currentImageIndex = index;
    updateGalleryImage();
}

function prevImage() {
    currentImageIndex = (currentImageIndex - 1 + currentProjectImages.length) % currentProjectImages.length;
    updateGalleryImage();
}

function nextImage() {
    currentImageIndex = (currentImageIndex + 1) % currentProjectImages.length;
    updateGalleryImage();
}

function updateGalleryImage() {
    const mainImage = document.getElementById('modalMainImage');
    const basePath = document.querySelector('meta[name="assets-path"]')?.content || '/assets';
    mainImage.src = `${basePath}/images/projects/${currentProjectImages[currentImageIndex]}`;
    
    // Update thumbnail active state
    const thumbnails = document.querySelectorAll('#galleryThumbnails img');
    thumbnails.forEach((thumb, index) => {
        thumb.classList.toggle('active', index === currentImageIndex);
    });
}

/**
 * Image Lightbox - Agrandissement des images
 */
let lightboxImages = [];
let lightboxCurrentIndex = 0;

function openLightbox(imageSrc, allImages, startIndex) {
    const lightbox = document.getElementById('imageLightbox');
    const lightboxImg = document.getElementById('lightboxImage');
    const counter = document.getElementById('lightboxCounter');
    
    if (!lightbox || !lightboxImg) return;
    
    // Set images array
    if (allImages && allImages.length > 0) {
        lightboxImages = allImages;
        lightboxCurrentIndex = startIndex || 0;
    } else {
        lightboxImages = [imageSrc];
        lightboxCurrentIndex = 0;
    }
    
    // Set image
    lightboxImg.src = lightboxImages[lightboxCurrentIndex];
    
    // Update counter
    if (counter) {
        counter.textContent = `${lightboxCurrentIndex + 1} / ${lightboxImages.length}`;
    }
    
    // Show/hide nav buttons
    const prevBtn = document.querySelector('.lightbox-prev');
    const nextBtn = document.querySelector('.lightbox-next');
    const counterEl = document.querySelector('.lightbox-counter');
    
    if (lightboxImages.length <= 1) {
        if (prevBtn) prevBtn.style.display = 'none';
        if (nextBtn) nextBtn.style.display = 'none';
        if (counterEl) counterEl.style.display = 'none';
    } else {
        if (prevBtn) prevBtn.style.display = 'flex';
        if (nextBtn) nextBtn.style.display = 'flex';
        if (counterEl) counterEl.style.display = 'block';
    }
    
    // Show lightbox
    lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const lightbox = document.getElementById('imageLightbox');
    if (lightbox) {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function lightboxPrev() {
    if (lightboxImages.length <= 1) return;
    lightboxCurrentIndex = (lightboxCurrentIndex - 1 + lightboxImages.length) % lightboxImages.length;
    updateLightboxImage();
}

function lightboxNext() {
    if (lightboxImages.length <= 1) return;
    lightboxCurrentIndex = (lightboxCurrentIndex + 1) % lightboxImages.length;
    updateLightboxImage();
}

function updateLightboxImage() {
    const lightboxImg = document.getElementById('lightboxImage');
    const counter = document.getElementById('lightboxCounter');
    
    if (lightboxImg) {
        lightboxImg.src = lightboxImages[lightboxCurrentIndex];
    }
    if (counter) {
        counter.textContent = `${lightboxCurrentIndex + 1} / ${lightboxImages.length}`;
    }
}

// Make gallery main image open lightbox
function initGalleryLightbox() {
    const galleryMainImg = document.getElementById('modalMainImage');
    if (galleryMainImg) {
        galleryMainImg.addEventListener('click', function(e) {
            e.stopPropagation();
            const basePath = document.querySelector('meta[name="assets-path"]')?.content || '/assets';
            const allImages = currentProjectImages.map(img => `${basePath}/images/projects/${img}`);
            openLightbox(this.src, allImages, currentImageIndex);
        });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initGalleryLightbox();
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    // Lightbox has priority
    const lightbox = document.getElementById('imageLightbox');
    if (lightbox && lightbox.classList.contains('active')) {
        if (e.key === 'Escape') {
            closeLightbox();
        }
        if (e.key === 'ArrowLeft') {
            lightboxPrev();
        }
        if (e.key === 'ArrowRight') {
            lightboxNext();
        }
        return;
    }
    
    // Then project modal
    const projectModal = document.getElementById('projectModal');
    if (projectModal && projectModal.classList.contains('active')) {
        if (e.key === 'Escape') {
            closeProjectModal();
        }
        if (e.key === 'ArrowLeft') {
            prevImage();
        }
        if (e.key === 'ArrowRight') {
            nextImage();
        }
    }
});

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('projectModal');
    if (e.target === modal) {
        closeProjectModal();
    }
    
    // Close lightbox when clicking on background
    const lightbox = document.getElementById('imageLightbox');
    if (e.target === lightbox) {
        closeLightbox();
    }
});

// Export functions for global use
window.showToast = showToast;
window.copyToClipboard = copyToClipboard;
window.scrollToTop = scrollToTop;
window.openProjectModal = openProjectModal;
window.closeProjectModal = closeProjectModal;
window.prevImage = prevImage;
window.nextImage = nextImage;
window.setImage = setImage;
window.openLightbox = openLightbox;
window.closeLightbox = closeLightbox;
window.lightboxPrev = lightboxPrev;
window.lightboxNext = lightboxNext;
