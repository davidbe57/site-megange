document.addEventListener('DOMContentLoaded', function () {
    /* ===== Header scroll effect ===== */
    const header = document.querySelector('.site-header');

    window.addEventListener('scroll', function () {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    /* ===== Mobile menu ===== */
    const toggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('.main-nav');

    if (toggle && nav) {
        toggle.addEventListener('click', function () {
            const isOpen = nav.classList.toggle('open');
            toggle.classList.toggle('active');
            toggle.setAttribute('aria-expanded', isOpen);
        });

        document.addEventListener('click', function (e) {
            if (!nav.contains(e.target) && !toggle.contains(e.target)) {
                nav.classList.remove('open');
                toggle.classList.remove('active');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });

        nav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                nav.classList.remove('open');
                toggle.classList.remove('active');
                toggle.setAttribute('aria-expanded', 'false');
            });
        });
    }

    /* ===== Dark/Light Theme ===== */
    const themeToggle = document.getElementById('themeToggle');
    const icon = themeToggle ? themeToggle.querySelector('i') : null;

    function setTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        if (icon) {
            icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }
    }

    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        setTheme(savedTheme);
    } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        setTheme('dark');
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            const current = document.documentElement.getAttribute('data-theme');
            setTheme(current === 'dark' ? 'light' : 'dark');
        });
    }

    /* ===== Hero Parallax ===== */
    const hero = document.querySelector('.hero');
    let heroSlides = [];

    if (hero) {
        heroSlides = hero.querySelectorAll('.carousel-slide');
        window.addEventListener('scroll', function () {
            const scrollY = window.scrollY;
            const heroTop = hero.offsetTop;
            const offset = scrollY - heroTop;
            if (offset > 0) {
                heroSlides.forEach(function (slide) {
                    slide.style.transform = 'translateY(' + (offset * 0.35) + 'px)';
                });
            } else {
                heroSlides.forEach(function (slide) {
                    slide.style.transform = 'translateY(0)';
                });
            }
        });
    }

    /* ===== Hero Carousel ===== */
    const slides = document.querySelectorAll('.carousel-slide');
    const dots = document.querySelectorAll('.carousel-dot');

    if (slides.length > 0) {
        let current = 0;
        let interval = setInterval(nextSlide, 5000);

        function goToSlide(index) {
            slides.forEach(function (s) { s.classList.remove('active'); });
            dots.forEach(function (d) { d.classList.remove('active'); });
            slides[index].classList.add('active');
            dots[index].classList.add('active');
            current = index;
        }

        function nextSlide() {
            goToSlide((current + 1) % slides.length);
        }

        function resetInterval() {
            clearInterval(interval);
            interval = setInterval(nextSlide, 5000);
        }

        dots.forEach(function (dot) {
            dot.addEventListener('click', function () {
                goToSlide(parseInt(dot.dataset.index));
                resetInterval();
            });
        });
    }

    /* ===== Back to top ===== */
    const backToTop = document.querySelector('.back-to-top');

    if (backToTop) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 400) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        });

        backToTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* ===== Gallery Lightbox ===== */
    const galleryItems = document.querySelectorAll('.gallery-item');
    const lightbox = document.querySelector('.lightbox');
    const lightboxImg = lightbox ? lightbox.querySelector('img') : null;
    const lightboxClose = lightbox ? lightbox.querySelector('.lightbox-close') : null;
    const lightboxPrev = lightbox ? lightbox.querySelector('.lightbox-prev') : null;
    const lightboxNext = lightbox ? lightbox.querySelector('.lightbox-next') : null;

    let currentIndex = 0;
    let allImages = [];

    if (galleryItems.length > 0 && lightbox) {
        allImages = Array.from(galleryItems).map(function (item) {
            return {
                src: item.dataset.src,
                alt: item.querySelector('img') ? item.querySelector('img').alt : ''
            };
        });

        galleryItems.forEach(function (item, index) {
            item.addEventListener('click', function () {
                openLightbox(index);
            });
        });

        if (lightboxClose) {
            lightboxClose.addEventListener('click', closeLightbox);
        }

        if (lightboxPrev) {
            lightboxPrev.addEventListener('click', function () {
                navigateLightbox(-1);
            });
        }

        if (lightboxNext) {
            lightboxNext.addEventListener('click', function () {
                navigateLightbox(1);
            });
        }

        document.addEventListener('keydown', function (e) {
            if (!lightbox.classList.contains('active')) return;
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') navigateLightbox(-1);
            if (e.key === 'ArrowRight') navigateLightbox(1);
        });

        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox) closeLightbox();
        });
    }

    function openLightbox(index) {
        currentIndex = index;
        updateLightbox();
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }

    function navigateLightbox(direction) {
        currentIndex += direction;
        if (currentIndex < 0) currentIndex = allImages.length - 1;
        if (currentIndex >= allImages.length) currentIndex = 0;
        updateLightbox();
    }

    function updateLightbox() {
        if (allImages.length === 0) return;
        const img = allImages[currentIndex];
        lightboxImg.src = img.src;
        lightboxImg.alt = img.alt;
    }

    /* ===== Smooth reveal on scroll ===== */
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.card, .team-card, .gallery-item, .news-item, .home-actu, .home-link-card, .home-section, .home-article').forEach(function (el) {
        el.classList.add('scroll-reveal');
        observer.observe(el);
    });

    /* ===== Modals (carte + météo) ===== */
    function openModal(id) {
        var m = document.getElementById(id);
        if (!m) return;
        m.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        var m = document.getElementById(id);
        if (!m) return;
        m.classList.remove('active');
        document.body.style.overflow = '';
    }

    function closeAllModals() {
        document.querySelectorAll('.modal.active').forEach(function (m) {
            m.classList.remove('active');
        });
        document.body.style.overflow = '';
    }

    /* Map modal */
    var mapBtn = document.getElementById('mapBtn');
    if (mapBtn) {
        mapBtn.addEventListener('click', function () {
            openModal('mapModal');
            if (typeof L === 'undefined') {
                var s = document.createElement('script');
                s.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                s.onload = initMap;
                document.head.appendChild(s);
            } else if (!window._mapDone) {
                setTimeout(initMap, 200);
            }
        });
    }

    function initMap() {
        var c = document.getElementById('map');
        if (!c || window._mapDone) return;
        setTimeout(function () {
            var map = L.map('map').setView([49.217688, 6.434656], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
            L.marker([49.217688, 6.434656]).addTo(map)
                .bindPopup('Mairie de Mégange<br>25 rue Principale')
                .openPopup();
            window._mapDone = true;
            setTimeout(function () { map.invalidateSize(); }, 300);
        }, 200);
    }

    /* Weather modal */
    var weatherBtn = document.getElementById('weatherBtn');
    if (weatherBtn) {
        weatherBtn.addEventListener('click', function () {
            openModal('weatherModal');
            fetchWeather();
        });
    }

    function fetchWeather() {
        var c = document.getElementById('weatherContent');
        if (!c) return;
        c.innerHTML = '<p style="color:var(--gray-400);"><i class="fas fa-spinner fa-spin"></i> Chargement…</p>';
        fetch('https://api.open-meteo.com/v1/forecast?latitude=49.2177&longitude=6.4347&current_weather=true&timezone=auto')
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (!d.current_weather) {
                    c.innerHTML = '<p class="weather-error">Données non disponibles</p>'; return;
                }
                var w = d.current_weather;
                c.innerHTML =
                    '<div class="weather-main">' +
                        '<div class="weather-icon"><i class="fas fa-' + wIcon(w.weathercode) + '"></i></div>' +
                        '<div class="weather-temp">' + Math.round(w.temperature) + '<sup>°C</sup></div>' +
                    '</div>' +
                    '<p style="margin-bottom:1rem;">' + wDesc(w.weathercode) + '</p>' +
                    '<div class="weather-details">' +
                        '<div class="weather-detail-item"><div class="label">Vent</div><div class="value">' + Math.round(w.windspeed) + ' km/h</div></div>' +
                        '<div class="weather-detail-item"><div class="label">Direction</div><div class="value">' + wDir(w.winddirection) + '</div></div>' +
                    '</div>';
            })
            .catch(function () {
                c.innerHTML = '<p class="weather-error">Erreur de chargement</p>';
            });
    }

    function wIcon(code) {
        if (code === 0) return 'sun';
        if (code <= 3) return 'cloud-sun';
        if (code <= 48) return 'smog';
        if (code <= 57) return 'cloud-rain';
        if (code <= 67) return 'cloud-showers-heavy';
        if (code <= 77) return 'snowflake';
        if (code <= 82) return 'cloud-rain';
        if (code <= 86) return 'cloud-snow';
        return 'cloud-bolt';
    }

    function wDesc(code) {
        if (code === 0) return 'Ciel dégagé';
        if (code <= 3) return 'Partiellement nuageux';
        if (code <= 48) return 'Brume / Brouillard';
        if (code <= 57) return 'Bruine';
        if (code <= 67) return 'Pluie';
        if (code <= 77) return 'Neige';
        if (code <= 82) return 'Averses';
        if (code <= 86) return 'Averses de neige';
        return 'Orage';
    }

    function wDir(deg) {
        if (deg < 22.5) return 'N';
        if (deg < 67.5) return 'NE';
        if (deg < 112.5) return 'E';
        if (deg < 157.5) return 'SE';
        if (deg < 202.5) return 'S';
        if (deg < 247.5) return 'SO';
        if (deg < 292.5) return 'O';
        if (deg < 337.5) return 'NO';
        return 'N';
    }

    /* Close modals on overlay/close click */
    document.querySelectorAll('.modal-overlay, .modal-close').forEach(function (el) {
        el.addEventListener('click', function () {
            var m = el.closest('.modal');
            if (m) closeModal(m.id);
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeAllModals();
    });
});
