/* ========================================
   MARBELLA JETSKI - Interactive JavaScript
   Premium Water Sports Website - Summer 2026
   ======================================== */

// Initialize functions after DOM loads
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        try {
            initNavigation();
        } catch(e) { console.log('Nav error:', e); }
        
        try {
            initSmoothScroll();
        } catch(e) { console.log('Scroll error:', e); }
        
        try {
            initBackToTop();
        } catch(e) { console.log('Back-to-top error:', e); }
        
        try {
            initCookieConsent();
        } catch(e) { console.log('Cookie error:', e); }
        
        try {
            initServiceWorker();
        } catch(e) { console.log('SW error:', e); }
        
        try {
            initSwiper();
        } catch(e) { console.log('Swiper error:', e); }
        
        try {
            initStats();
        } catch(e) { console.log('Stats error:', e); }
        
        try {
            initGallery();
        } catch(e) { console.log('Gallery error:', e); }
        
        try {
            initFAQ();
        } catch(e) { console.log('FAQ error:', e); }
        
        try {
            initActivityFilter();
        } catch(e) { console.log('Filter error:', e); }

        try {
            initWeather();
        } catch(e) { console.log('Weather error:', e); }
    }, 100);
});

/* ========== Navigation ========== */
function initNavigation() {
    const navbar = document.getElementById('navbar');
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    const navLinks = document.querySelectorAll('.nav-link');
    
    // Scroll effect for navbar + promo banner
    const promoBanner = document.querySelector('.hero-promo-overlay');

    // Initial entrance: show banner after 1.2s
    if (promoBanner) {
        setTimeout(() => {
            promoBanner.classList.add('promo-visible');
        }, 1200);
    }

    window.addEventListener('scroll', () => {
        const scrollY = window.scrollY;
        const docHeight = document.documentElement.scrollHeight;
        const winHeight = window.innerHeight;
        const atBottom = (scrollY + winHeight) >= (docHeight - 100);

        // Navbar: smooth morph via CSS transitions
        if (scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }

        // Promo banner: position based on scroll distance
        if (promoBanner) {
            if (atBottom) {
                // At bottom of page: fade out, move to top (tight under header), then fade in
                promoBanner.classList.add('promo-hide');
                setTimeout(() => {
                    promoBanner.classList.remove('promo-bottom');
                    promoBanner.classList.add('promo-scrolled-top');
                    setTimeout(() => {
                        promoBanner.classList.remove('promo-hide');
                    }, 50);
                }, 400);
            } else if (scrollY <= 50) {
                // At top: show in original top position
                promoBanner.classList.remove('promo-bottom', 'promo-hide', 'promo-scrolled-top');
            } else if (scrollY > 400) {
                // Scrolled far enough: show at bottom of viewport
                promoBanner.classList.add('promo-bottom');
                promoBanner.classList.remove('promo-hide', 'promo-scrolled-top');
            } else {
                // In between (50-400px): hidden during transition
                promoBanner.classList.add('promo-hide');
                promoBanner.classList.remove('promo-bottom', 'promo-scrolled-top');
            }
        }
    });
    
    // Mobile menu toggle
    navToggle.addEventListener('click', () => {
        const isOpen = navMenu.classList.toggle('active');
        navToggle.classList.toggle('active');
        document.documentElement.classList.toggle('menu-open', isOpen);
    });
    
    // Close mobile menu on link click
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            navMenu.classList.remove('active');
            navToggle.classList.remove('active');
            document.documentElement.classList.remove('menu-open');
        });
    });

    // Close mobile menu on backdrop tap (clicking the overlay, not links)
    navMenu.addEventListener('click', (e) => {
        if (e.target === navMenu) {
            navMenu.classList.remove('active');
            navToggle.classList.remove('active');
            document.documentElement.classList.remove('menu-open');
        }
    });

    // Close mobile menu on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && navMenu.classList.contains('active')) {
            navMenu.classList.remove('active');
            navToggle.classList.remove('active');
            document.documentElement.classList.remove('menu-open');
        }
    });

    // Language dropdown: click toggle (hover doesn't work on mobile)
    document.querySelectorAll('.nav-lang-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const dropdown = btn.closest('.nav-lang-dropdown');
            // Close any other open dropdowns
            document.querySelectorAll('.nav-lang-dropdown.open').forEach(d => {
                if (d !== dropdown) d.classList.remove('open');
            });
            dropdown.classList.toggle('open');
        });
    });

    // Close language dropdown when clicking outside
    document.addEventListener('click', (e) => {
        document.querySelectorAll('.nav-lang-dropdown.open').forEach(d => {
            if (!d.contains(e.target)) d.classList.remove('open');
        });
    });
    
    // Active link on scroll
    window.addEventListener('scroll', () => {
        let current = '';
        const sections = document.querySelectorAll('section[id]');
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 150;
            const sectionHeight = section.offsetHeight;
            
            if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
                current = section.getAttribute('id');
            }
        });
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${current}`) {
                link.classList.add('active');
            }
        });
    });
}

/* ========== Smooth Scroll ========== */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            
            if (targetId === '#') return;
            
            const target = document.querySelector(targetId);
            if (target) {
                const offsetTop = target.offsetTop - 80;
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });
}

/* ========== Animated Stats Counter ========== */
function initStats() {
    const stats = document.querySelectorAll('.stat-number[data-count]');
    
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px'
    };
    
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const stat = entry.target;
                const target = parseInt(stat.getAttribute('data-count'));
                if (!isNaN(target)) {
                    animateCounter(stat, target);
                }
                statsObserver.unobserve(stat);
            }
        });
    }, observerOptions);
    
    stats.forEach(stat => {
        statsObserver.observe(stat);
    });
}

function animateCounter(element, target) {
    let current = 0;
    const increment = target / 100;
    const duration = 2000;
    const stepTime = duration / 100;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        
        if (target >= 1000) {
            element.textContent = Math.floor(current).toLocaleString();
        } else {
            element.textContent = Math.floor(current);
        }
    }, stepTime);
}

/* ========== Swiper Testimonials ========== */
function initSwiper() {
    if (typeof Swiper !== 'undefined') {
        new Swiper('.testimonials-swiper', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true
            },
            breakpoints: {
                640: {
                    slidesPerView: 2
                },
                1024: {
                    slidesPerView: 3
                }
            }
        });

        // Jetski option image carousels
        document.querySelectorAll('.jetski-carousel').forEach(el => {
            new Swiper(el, {
                slidesPerView: 1,
                loop: true,
                autoplay: { delay: 4000, disableOnInteraction: false },
                pagination: { el: el.querySelector('.swiper-pagination'), clickable: true },
                effect: 'fade',
                fadeEffect: { crossFade: true }
            });
        });
    }
}

/* ========== Gallery Lightbox ========== */
const galleryImages = [
    'https://marbellajetski.com/wp-content/uploads/2023/05/IMG_0052-scaled.webp',
    'assets/media/photos/cranchi-39.jpg',
    'assets/media/photos/azimut-39.jpg',
    'https://marbellajetski.com/wp-content/uploads/2023/05/IMG_3961-scaled.webp',
    'https://marbellajetski.com/wp-content/uploads/2021/07/IMG_6208-scaled.jpg',
    'https://marbellajetski.com/wp-content/uploads/2023/05/IMG_3846-scaled.webp',
    'https://marbellajetski.com/wp-content/uploads/2023/05/DSC_0055-scaled.webp',
    'https://marbellajetski.com/wp-content/uploads/2023/05/DSC_0031-scaled.webp',
    'assets/media/racing/racing-benalmadena-1.jpg',
    'assets/media/racing/racing-circuit-benidorm.png',
    'assets/media/racing/promo-racing.jpg',
    'assets/media/racing/dani-stiers-race-start.jpg',
    'assets/media/photos/blue-jetski.jpg',
    'assets/media/photos/high-res-jetski.jpg',
    'assets/media/photos/waterski-action.jpg',
    'assets/media/photos/yacht-action-1.jpg',
    'assets/media/photos/yacht-action-2.jpg',
    'assets/media/photos/waterski-action-2.jpg',
    'assets/media/photos/kayak-double.jpg',
    'assets/media/photos/paddle-surf.jpg',
    'assets/media/photos/catamaran-bali.jpg',
    'assets/media/photos/rinker-296-alt.jpg',
    'assets/media/photos/hidropedal-newbeetle.jpg'
];

let currentImageIndex = 0;

function initGallery() {
    const lightbox = document.getElementById('lightbox');
    
    // Close on overlay click
    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox) {
            closeLightbox();
        }
    });
    
    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (!lightbox.classList.contains('active')) return;
        
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') changeLightboxImage(-1);
        if (e.key === 'ArrowRight') changeLightboxImage(1);
    });
}

function openLightbox(index) {
    currentImageIndex = index;
    const lightbox = document.getElementById('lightbox');
    const lightboxImage = document.getElementById('lightboxImage');
    
    lightboxImage.src = galleryImages[index];
    lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const lightbox = document.getElementById('lightbox');
    lightbox.classList.remove('active');
    document.body.style.overflow = '';
}

function changeLightboxImage(direction) {
    currentImageIndex += direction;
    
    if (currentImageIndex >= galleryImages.length) {
        currentImageIndex = 0;
    } else if (currentImageIndex < 0) {
        currentImageIndex = galleryImages.length - 1;
    }
    
    const lightboxImage = document.getElementById('lightboxImage');
    lightboxImage.style.opacity = '0';
    
    setTimeout(() => {
        lightboxImage.src = galleryImages[currentImageIndex];
        lightboxImage.style.opacity = '1';
    }, 200);
}

// Make functions globally available
window.openLightbox = openLightbox;
window.closeLightbox = closeLightbox;
window.changeLightboxImage = changeLightboxImage;

/* ========== Boat Detail Modal ========== */
const boatData = {
    rinker: {
        name: 'Rinker 296 Captiva',
        badge: 'Value Pick',
        badgeClass: '',
        description: 'Spacious American cruiser with a comfortable cabin below deck. The Rinker 296 Captiva is perfect for small groups looking for a stylish day on the water around Puerto Banús and the Costa del Sol coastline.',
        specs: [
            { icon: 'fa-ruler', label: 'Length', value: '9.4 m' },
            { icon: 'fa-users', label: 'Capacity', value: '8 guests' },
            { icon: 'fa-bed', label: 'Cabins', value: '1 cabin' },
            { icon: 'fa-anchor', label: 'Dock', value: 'Puerto Banús Dock 5' }
        ],
        included: ['Professional Captain', 'Fuel', 'Welcome Drink', 'Sound System', 'Full Insurance', 'VAT Included'],
        pricing: [
            { duration: '1 hour', price: '€250' },
            { duration: '2 hours', price: '€400' },
            { duration: '3 hours', price: '€600' },
            { duration: '4 hours', price: '€800' },
            { duration: '6 hours', price: '€1,100' },
            { duration: '8 hours', price: '€1,400' }
        ],
        images: [
            'assets/media/photos/rinker-296-alt.jpg',
            'assets/media/photos/boats/rinker_captiva_296_img2.jpeg',
            'assets/media/photos/boats/rinker_captiva_296_img4.jpeg',
            'assets/media/photos/boats/rinker_captiva_296_img5.jpeg',
            'assets/media/photos/boats/rinker_captiva_296_img6.jpeg',
            'assets/media/photos/boats/rinker_captiva_296_img7.jpeg',
            'assets/media/photos/boats/rinker_captiva_296_img8.jpeg',
            'assets/media/photos/boats/rinker_captiva_296_img9.jpeg'
        ],
        whatsapp: 'https://api.whatsapp.com/send?phone=34655442232&text=Hi!%20I%27d%20like%20to%20book%20the%20Rinker%20296'
    },
    cranchi: {
        name: 'Cranchi 39',
        badge: 'Premium',
        badgeClass: 'premium',
        description: 'Elegant Italian open-concept design with a lower cabin. The Cranchi 39 offers a sophisticated experience ideal for celebrations, VIP occasions, and unforgettable days along the Marbella coast.',
        specs: [
            { icon: 'fa-ruler', label: 'Length', value: '12.3 m' },
            { icon: 'fa-users', label: 'Capacity', value: '9 guests' },
            { icon: 'fa-bed', label: 'Cabins', value: '1 lower cabin' },
            { icon: 'fa-flag', label: 'Origin', value: 'Italian Design' }
        ],
        included: ['Professional Captain', 'Fuel', 'Drinks & Wine', 'Paddleboard', 'Full Insurance', 'VAT Included'],
        pricing: [
            { duration: '1 hour', price: '€380' },
            { duration: '2 hours', price: '€550' },
            { duration: '3 hours', price: '€680' },
            { duration: '4 hours', price: '€850' },
            { duration: '6 hours', price: '€1,150' },
            { duration: '8 hours', price: '€1,550' }
        ],
        images: [
            'assets/media/photos/cranchi-39.jpg',
            'assets/media/photos/boats/cranchi_39_img1.jpeg',
            'assets/media/photos/boats/cranchi_39_img2.jpeg',
            'assets/media/photos/boats/cranchi_39_img3.jpeg',
            'assets/media/photos/boats/cranchi_39_img4.jpeg',
            'assets/media/photos/boats/cranchi_39_img5.jpeg',
            'assets/media/photos/boats/cranchi_39_img6.jpeg',
            'assets/media/photos/boats/cranchi_39_img7.jpeg',
            'assets/media/photos/boats/cranchi_39_img8.jpeg'
        ],
        whatsapp: 'https://api.whatsapp.com/send?phone=34655442232&text=Hi!%20I%27d%20like%20to%20book%20the%20Cranchi%2039'
    },
    azimut: {
        name: 'Azimut 39 Fly',
        badge: 'Flybridge Luxury',
        badgeClass: 'luxury',
        description: 'The ultimate luxury flybridge yacht. The Azimut 39 Fly features an expansive flybridge for sunbathing, an elegant salon, two cabins with WC, and comes fully equipped with champagne, wine, paddle surf, and a premium sound system.',
        specs: [
            { icon: 'fa-ruler', label: 'Length', value: '12.3 m' },
            { icon: 'fa-users', label: 'Capacity', value: '12 guests' },
            { icon: 'fa-layer-group', label: 'Decks', value: 'Flybridge + Salon' },
            { icon: 'fa-bed', label: 'Cabins', value: '2 cabins / 1 WC' }
        ],
        included: ['Professional Captain', 'Fuel', '2 Champagne Bottles', 'Wine', 'Paddle Surf', 'Full Insurance', 'Sound System', 'VAT Included'],
        pricing: [
            { duration: '1 hour', price: '€400' },
            { duration: '2 hours', price: '€600' },
            { duration: '3 hours', price: '€800' },
            { duration: '4 hours', price: '€1,000' },
            { duration: '6 hours', price: '€1,500' },
            { duration: '8 hours', price: '€1,800' }
        ],
        images: [
            'assets/media/photos/azimut-39.jpg',
            'assets/media/photos/boats/azimut_39_fly_img1.jpeg',
            'assets/media/photos/boats/azimut_39_fly_img2.jpeg',
            'assets/media/photos/boats/azimut_39_fly_img3.jpeg',
            'assets/media/photos/boats/azimut_39_fly_img4.jpeg',
            'assets/media/photos/boats/azimut_39_fly_img5.jpeg',
            'assets/media/photos/boats/azimut_39_fly_img6.jpeg',
            'assets/media/photos/boats/azimut_39_fly_img7.jpeg',
            'assets/media/photos/boats/azimut_39_fly_img8.jpeg'
        ],
        whatsapp: 'https://api.whatsapp.com/send?phone=34655442232&text=Hi!%20I%27d%20like%20to%20book%20the%20Azimut%2039%20Fly'
    },
    catamaran: {
        name: 'Catamaran Bali 4.0',
        badge: 'Catamaran',
        badgeClass: 'luxury',
        description: 'Spacious 2020 catamaran with an impressive 7-metre beam and twin Volvo engines. The Bali 4.0 offers 4 cabins, 4 bathrooms, and huge open-plan living — perfect for large groups and special celebrations on the Mediterranean.',
        specs: [
            { icon: 'fa-ruler', label: 'Length', value: '12.5 m' },
            { icon: 'fa-arrows-left-right', label: 'Beam', value: '7 m' },
            { icon: 'fa-users', label: 'Capacity', value: '12 + crew' },
            { icon: 'fa-bed', label: 'Cabins', value: '4 cabins / 4 WC' },
            { icon: 'fa-calendar', label: 'Year', value: '2020' }
        ],
        included: ['Professional Captain', 'Fuel', 'Towels', 'Paddle Surf', 'Snorkel', 'Rosé, Beer & Cava', 'Soft Drinks', 'Full Insurance'],
        pricing: [
            { duration: '2 hours', price: '€750' },
            { duration: '3 hours', price: '€1,000' },
            { duration: '4 hours', price: '€1,150' },
            { duration: '6 hours', price: '€1,750' },
            { duration: '8 hours', price: '€2,250' }
        ],
        images: [
            'assets/media/photos/catamaran-bali.jpg',
            'assets/media/photos/boats/catamaran_bali_40_2020_3_img1.jpeg',
            'assets/media/photos/boats/catamaran_bali_40_2020_3_img2.jpeg',
            'assets/media/photos/boats/catamaran_bali_40_2020_3_img3.jpeg',
            'assets/media/photos/boats/catamaran_bali_40_2020_3_img4.jpeg',
            'assets/media/photos/boats/catamaran_bali_40_2020_3_img5.jpeg',
            'assets/media/photos/boats/catamaran_bali_40_2020_3_img6.jpeg',
            'assets/media/photos/boats/catamaran_bali_40_2020_3_img7.jpeg',
            'assets/media/photos/boats/catamaran_bali_40_2020_3_img8.jpeg'
        ],
        whatsapp: 'https://api.whatsapp.com/send?phone=34655442232&text=Hi!%20I%27d%20like%20to%20book%20the%20Catamaran%20Bali%204.0'
    }
};

let currentBoatKey = null;
let currentBoatImageIndex = 0;

function openBoatModal(key) {
    const boat = boatData[key];
    if (!boat) return;
    
    currentBoatKey = key;
    currentBoatImageIndex = 0;
    
    // Populate badge
    const badge = document.getElementById('boatModalBadge');
    badge.textContent = boat.badge;
    badge.className = 'boat-modal-badge' + (boat.badgeClass ? ' ' + boat.badgeClass : '');
    
    // Title & description
    document.getElementById('boatModalTitle').textContent = boat.name;
    document.getElementById('boatModalDesc').textContent = boat.description;
    
    // Specs
    const specsEl = document.getElementById('boatModalSpecs');
    specsEl.innerHTML = boat.specs.map(s =>
        `<div class="boat-spec-item">
            <i class="fas ${s.icon}"></i>
            <div><span class="spec-label">${s.label}</span><br><span class="spec-value">${s.value}</span></div>
        </div>`
    ).join('');
    
    // Included
    const inclEl = document.getElementById('boatModalIncluded');
    inclEl.innerHTML = boat.included.map(item => `<li>${item}</li>`).join('');
    
    // Pricing
    const pricingEl = document.getElementById('boatModalPricing');
    pricingEl.innerHTML = boat.pricing.map(p =>
        `<div class="boat-price-row">
            <span class="price-duration">${p.duration}</span>
            <span class="price-amount">${p.price}</span>
        </div>`
    ).join('');
    
    // Book button — link to booking page with yacht filter
    document.getElementById('boatModalBookBtn').href = 'booking.html?yacht=' + key;
    
    // Gallery - main image
    const mainImg = document.getElementById('boatModalMainImg');
    mainImg.src = boat.images[0];
    mainImg.alt = boat.name;
    
    // Gallery - thumbnails
    const thumbsEl = document.getElementById('boatModalThumbs');
    thumbsEl.innerHTML = boat.images.map((img, i) =>
        `<div class="boat-thumb ${i === 0 ? 'active' : ''}" onclick="selectBoatImage(${i})">
            <img src="${img}" alt="${boat.name} photo ${i + 1}" loading="lazy">
        </div>`
    ).join('');
    
    // Counter
    updateBoatCounter();
    
    // Show modal
    const overlay = document.getElementById('boatModal');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeBoatModal() {
    const overlay = document.getElementById('boatModal');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
    currentBoatKey = null;
}

function selectBoatImage(index) {
    if (!currentBoatKey) return;
    currentBoatImageIndex = index;
    
    const boat = boatData[currentBoatKey];
    const mainImg = document.getElementById('boatModalMainImg');
    mainImg.style.opacity = '0';
    
    setTimeout(() => {
        mainImg.src = boat.images[index];
        mainImg.style.opacity = '1';
    }, 150);
    
    // Update active thumb
    const thumbs = document.querySelectorAll('.boat-thumb');
    thumbs.forEach((t, i) => t.classList.toggle('active', i === index));
    
    // Scroll active thumb into view  
    if (thumbs[index]) {
        thumbs[index].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }
    
    updateBoatCounter();
}

function changeBoatImage(direction) {
    if (!currentBoatKey) return;
    const boat = boatData[currentBoatKey];
    let newIndex = currentBoatImageIndex + direction;
    
    if (newIndex >= boat.images.length) newIndex = 0;
    else if (newIndex < 0) newIndex = boat.images.length - 1;
    
    selectBoatImage(newIndex);
}

function updateBoatCounter() {
    if (!currentBoatKey) return;
    const boat = boatData[currentBoatKey];
    document.getElementById('boatImgCounter').textContent =
        `${currentBoatImageIndex + 1} / ${boat.images.length}`;
}

// Keyboard: ESC to close boat modal
document.addEventListener('keydown', (e) => {
    const boatModal = document.getElementById('boatModal');
    if (!boatModal || !boatModal.classList.contains('active')) return;
    
    if (e.key === 'Escape') closeBoatModal();
    if (e.key === 'ArrowLeft') changeBoatImage(-1);
    if (e.key === 'ArrowRight') changeBoatImage(1);
});

// Make functions globally available
window.openBoatModal = openBoatModal;
window.closeBoatModal = closeBoatModal;
window.selectBoatImage = selectBoatImage;
window.changeBoatImage = changeBoatImage;

/* ========== Load More Videos ========== */
function toggleMoreVideos() {
    const grid = document.getElementById('videoGrid');
    const btn = document.getElementById('loadMoreVideos');
    
    if (grid.classList.contains('show-all')) {
        grid.classList.remove('show-all');
        btn.innerHTML = '<i class="fas fa-play-circle"></i> Load More Videos <span class="load-more-count">(3 more)</span>';
    } else {
        grid.classList.add('show-all');
        btn.innerHTML = '<i class="fas fa-chevron-up"></i> Show Less';
        // Refresh AOS for newly visible elements
        if (typeof AOS !== 'undefined') AOS.refresh();
    }
}

window.toggleMoreVideos = toggleMoreVideos;

/* ========== Load More Gallery ========== */
function toggleMoreGallery() {
    const grid = document.querySelector('.gallery-grid');
    const btn = document.getElementById('loadMoreGallery');
    
    if (grid.classList.contains('show-all')) {
        grid.classList.remove('show-all');
        btn.innerHTML = '<i class="fas fa-images"></i> Load More Photos <span class="load-more-count">(11 more)</span>';
    } else {
        grid.classList.add('show-all');
        btn.innerHTML = '<i class="fas fa-chevron-up"></i> Show Less';
        if (typeof AOS !== 'undefined') AOS.refresh();
    }
}

window.toggleMoreGallery = toggleMoreGallery;

/* ========== FAQ Accordion ========== */
function initFAQ() {
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        
        question.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            
            // Close all other items
            faqItems.forEach(otherItem => {
                otherItem.classList.remove('active');
                const btn = otherItem.querySelector('.faq-question');
                if (btn) btn.setAttribute('aria-expanded', 'false');
            });
            
            // Toggle current item
            if (!isActive) {
                item.classList.add('active');
                question.setAttribute('aria-expanded', 'true');
            }
        });
    });
}

/* ========== Activity Filter ========== */
function initActivityFilter() {
    const categoryBtns = document.querySelectorAll('.category-btn');
    const activityCards = document.querySelectorAll('.activity-card');
    
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Update active button
            categoryBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            const category = btn.dataset.category;
            
            // Filter activities
            activityCards.forEach(card => {
                const cardCategories = card.dataset.category;
                
                if (category === 'all' || cardCategories.includes(category)) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeInUp 0.5s ease';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
}

/* ========== Back to Top Button ========== */
function initBackToTop() {
    const backToTopBtn = document.getElementById('backToTop');
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 500) {
            backToTopBtn.classList.add('visible');
        } else {
            backToTopBtn.classList.remove('visible');
        }
    });
    
    backToTopBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

/* ========== Video Lazy Loading ========== */
document.addEventListener('DOMContentLoaded', () => {
    const video = document.getElementById('heroVideo');
    if (video) {
        video.play().catch(() => {
            // Autoplay was prevented, show a fallback
            console.log('Video autoplay prevented by browser');
        });
    }
});

/* ========== Scroll Progress Bar ========== */
// Removed

/* ========== Cookie Consent ========== */
function initCookieConsent() {
    // Check if consent was already given
    if (localStorage.getItem('cookieConsent')) return;
    
    const banner = document.createElement('div');
    banner.className = 'cookie-banner show';
    banner.innerHTML = `
        <p>
            <i class="fas fa-cookie-bite"></i>
            We use cookies to enhance your experience. By continuing to visit this site, you agree to our use of cookies.
            <a href="terms.html#cookies">Learn more</a>
        </p>
        <div class="cookie-buttons">
            <button class="cookie-btn accept" onclick="acceptCookies()">Accept All</button>
            <button class="cookie-btn decline" onclick="declineCookies()">Decline</button>
        </div>
    `;
    
    document.body.appendChild(banner);
}

function showNotification(message, type) {
    var n = document.createElement('div');
    n.className = 'notification notification-' + (type || 'info');
    n.style.cssText = 'position:fixed;top:20px;right:20px;z-index:10001;background:' + (type === 'success' ? '#10b981' : '#0ea5e9') + ';color:#fff;padding:14px 24px;border-radius:10px;font-weight:600;font-size:14px;box-shadow:0 4px 20px rgba(0,0,0,0.2);transform:translateY(-20px);opacity:0;transition:all 0.3s ease;';
    n.textContent = message;
    document.body.appendChild(n);
    requestAnimationFrame(function() { n.style.transform = 'translateY(0)'; n.style.opacity = '1'; });
    setTimeout(function() { n.style.transform = 'translateY(-20px)'; n.style.opacity = '0'; setTimeout(function() { n.remove(); }, 300); }, 3000);
}
window.showNotification = showNotification;

window.acceptCookies = function() {
    localStorage.setItem('cookieConsent', 'accepted');
    document.querySelector('.cookie-banner').remove();
    showNotification('Thanks! Cookie preferences saved.', 'success');
};

window.declineCookies = function() {
    localStorage.setItem('cookieConsent', 'declined');
    document.querySelector('.cookie-banner').remove();
};

/* ========== Service Worker Registration ========== */
function initServiceWorker() {
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => {
                    console.log('SW registered:', registration);
                })
                .catch(error => {
                    console.log('SW registration failed (expected in development):', error);
                });
        });
    }
}

/* ========== Image Lazy Loading ========== */
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                }
            }
        });
    });
    
    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

/* ========== Console Easter Egg ========== */
console.log(`
%c🌊 MARBELLA JETSKI 🌊
%cCosta del Sol's Premier Water Sports Experience

%c✨ Looking for a job? We're always looking for talented people!
   Contact us at: jetskimarbella@gmail.com

%c🏆 Built with passion for Summer 2026
   Owner: Daniel Stiers - Pro Racing Champion
   Since 1998 - Over 25 Years of Excellence

`, 
'font-size: 24px; font-weight: bold; color: #0ea5e9;',
'font-size: 14px; color: #374151;',
'font-size: 12px; color: #10b981;',
'font-size: 11px; color: #6b7280;'
);

/* ========== Performance Optimization ========== */
// Debounce function for scroll events
function debounce(func, wait = 10) {
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

// Throttle function for frequent events
function throttle(func, limit = 100) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

/* ========== Live Weather Widget (Open-Meteo — no API key) ========== */
function initWeather() {
    var el = document.getElementById('wx-temp');
    if (!el) return;

    // Marbella coordinates
    var lat = 36.5099, lon = -4.8854;
    var url = 'https://api.open-meteo.com/v1/forecast?latitude=' + lat
        + '&longitude=' + lon
        + '&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m'
        + '&daily=uv_index_max'
        + '&timezone=Europe%2FMadrid';

    fetch(url)
        .then(function(r) { return r.json(); })
        .then(function(d) {
            var c = d.current;
            if (!c) return;

            // Temperature
            var tempEl = document.getElementById('wx-temp');
            if (tempEl) tempEl.textContent = Math.round(c.temperature_2m) + '°C';

            // Weather description from WMO code
            var descEl = document.getElementById('wx-desc');
            if (descEl) descEl.textContent = wmoDesc(c.weather_code);

            // Wind speed (label is in the HTML)
            var windEl = document.getElementById('wx-wind');
            if (windEl) windEl.textContent = Math.round(c.wind_speed_10m);

            // Humidity
            var humEl = document.getElementById('wx-hum');
            if (humEl) humEl.textContent = Math.round(c.relative_humidity_2m) + '%';

            // Sea temp estimate (Marbella Mediterranean averages by month)
            var seaEl = document.getElementById('wx-sea');
            if (seaEl) {
                var month = new Date().getMonth(); // 0-11
                var seaTemps = [15, 14, 14, 15, 17, 20, 23, 25, 24, 21, 18, 16];
                seaEl.textContent = seaTemps[month] + '°C';
            }
        })
        .catch(function(err) {
            console.log('Weather fetch error:', err);
        });
}

function wmoDesc(code) {
    var lang = (document.documentElement.lang || 'en').slice(0, 2);
    var maps = {
        en: {
            0: 'Clear sky', 1: 'Mostly clear', 2: 'Partly cloudy', 3: 'Overcast',
            45: 'Foggy', 48: 'Rime fog',
            51: 'Light drizzle', 53: 'Drizzle', 55: 'Heavy drizzle',
            61: 'Light rain', 63: 'Rain', 65: 'Heavy rain',
            71: 'Light snow', 73: 'Snow', 75: 'Heavy snow',
            80: 'Light showers', 81: 'Showers', 82: 'Heavy showers',
            95: 'Thunderstorm', 96: 'Thunderstorm + hail', 99: 'Thunderstorm + hail',
            _default: 'Fair'
        },
        es: {
            0: 'Cielo despejado', 1: 'Mayormente despejado', 2: 'Parcialmente nublado', 3: 'Nublado',
            45: 'Niebla', 48: 'Niebla helada',
            51: 'Llovizna ligera', 53: 'Llovizna', 55: 'Llovizna fuerte',
            61: 'Lluvia ligera', 63: 'Lluvia', 65: 'Lluvia fuerte',
            71: 'Nieve ligera', 73: 'Nieve', 75: 'Nieve fuerte',
            80: 'Chubascos ligeros', 81: 'Chubascos', 82: 'Chubascos fuertes',
            95: 'Tormenta', 96: 'Tormenta con granizo', 99: 'Tormenta con granizo',
            _default: 'Buen tiempo'
        },
        fr: {
            0: 'Ciel dégagé', 1: 'Plutôt dégagé', 2: 'Partiellement nuageux', 3: 'Couvert',
            45: 'Brouillard', 48: 'Brouillard givrant',
            51: 'Bruine légère', 53: 'Bruine', 55: 'Forte bruine',
            61: 'Pluie légère', 63: 'Pluie', 65: 'Forte pluie',
            71: 'Neige légère', 73: 'Neige', 75: 'Forte neige',
            80: 'Averses légères', 81: 'Averses', 82: 'Fortes averses',
            95: 'Orage', 96: 'Orage avec grêle', 99: 'Orage avec grêle',
            _default: 'Beau temps'
        },
        nl: {
            0: 'Onbewolkt', 1: 'Overwegend helder', 2: 'Gedeeltelijk bewolkt', 3: 'Bewolkt',
            45: 'Mistig', 48: 'Rijpmist',
            51: 'Lichte motregen', 53: 'Motregen', 55: 'Zware motregen',
            61: 'Lichte regen', 63: 'Regen', 65: 'Zware regen',
            71: 'Lichte sneeuw', 73: 'Sneeuw', 75: 'Zware sneeuw',
            80: 'Lichte buien', 81: 'Buien', 82: 'Zware buien',
            95: 'Onweer', 96: 'Onweer met hagel', 99: 'Onweer met hagel',
            _default: 'Mooi weer'
        }
    };
    var m = maps[lang] || maps.en;
    return m[code] || m._default;
}

/* ========== Duration Selection on Homepage Pricing ========== */
function selectDuration(el) {
    // Remove selected from all rows in the same pricing table
    var table = el.closest('.pricing-table');
    if (!table) return;
    table.querySelectorAll('.price-row').forEach(function(row) {
        row.classList.remove('selected');
    });
    // Add selected to clicked row
    el.classList.add('selected');
    
    // Update the corresponding book button's href with the selected activity
    var activity = el.getAttribute('data-activity');
    if (!activity) return;
    
    // Find the book button in the same option-content container
    var optionContent = table.closest('.option-content');
    if (!optionContent) return;
    var bookBtn = optionContent.querySelector('.jetski-book-btn');
    if (!bookBtn) return;
    
    // Update the href - preserve the base path but change the activity param
    var basePath = bookBtn.getAttribute('href').split('?')[0];
    bookBtn.setAttribute('href', basePath + '?activity=' + activity);
}

/* F: Make service-card / jetski-option cards fully clickable */
document.querySelectorAll('.service-card, .jetski-option').forEach(function(card) {
    card.addEventListener('click', function(e) {
        if (e.target.closest('a')) return; // let real links work
        var link = card.querySelector('.service-link, .option-cta');
        if (link) link.click();
    });
});

