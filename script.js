// Global variables
let currentPage = 1;
let currentCategory = 'all';
let currentSearch = '';
let totalPages = 1;

// Load services on page load
document.addEventListener('DOMContentLoaded', function() {
    loadServices();
    setupEventListeners();
});

function setupEventListeners() {
    // Search input
    const searchInput = document.getElementById('serviceSearch');
    if (searchInput) {
        let typingTimer;
        searchInput.addEventListener('keyup', function(e) {
            clearTimeout(typingTimer);
            if (e.key === 'Enter') {
                performSearch();
            } else {
                typingTimer = setTimeout(performSearch, 500);
            }
        });
    }
    
    // Search button
    const searchBtn = document.getElementById('searchBtn');
    if (searchBtn) {
        searchBtn.addEventListener('click', performSearch);
    }
    
    // Category buttons
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentCategory = this.dataset.category;
            currentSearch = '';
            document.getElementById('serviceSearch').value = '';
            currentPage = 1;
            loadServices();
        });
    });
    
    // Mobile menu toggle
    const menuToggle = document.querySelector('.menu-toggle');
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            document.querySelector('.nav-links').classList.toggle('active');
        });
    }
    
    // FAQ toggles
    document.querySelectorAll('.faq-question').forEach(question => {
        question.addEventListener('click', () => {
            const faqItem = question.parentElement;
            faqItem.classList.toggle('active');
        });
    });
    
    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
}

function performSearch() {
    const searchInput = document.getElementById('serviceSearch');
    currentSearch = searchInput.value.trim();
    if (currentSearch.length < 2) {
        if (currentSearch.length === 0) {
            currentCategory = 'all';
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
            document.querySelector('.category-btn[data-category="all"]').classList.add('active');
            loadServices();
        }
        return;
    }
    currentCategory = '';
    currentPage = 1;
    loadServices();
}

async function loadServices() {
    const servicesGrid = document.getElementById('servicesGrid');
    if (!servicesGrid) return;
    
    servicesGrid.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading services...</div>';
    
    let url = `api/get-services.php?page=${currentPage}&limit=30`;
    if (currentSearch) {
        url += `&search=${encodeURIComponent(currentSearch)}`;
    } else if (currentCategory && currentCategory !== 'all') {
        url += `&category=${currentCategory}`;
    }
    
    try {
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success && data.services.length > 0) {
            displayServices(data.services);
            updatePagination(data);
            updateServiceCount(data.total);
        } else {
            servicesGrid.innerHTML = `
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3>No services found</h3>
                    <p>Try searching with different keywords</p>
                    <button onclick="resetSearch()" class="btn-primary">Reset Search</button>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading services:', error);
        servicesGrid.innerHTML = '<div class="error-message"><i class="fas fa-exclamation-triangle"></i> Error loading services. Please refresh the page.</div>';
    }
}

function displayServices(services) {
    const servicesGrid = document.getElementById('servicesGrid');
    servicesGrid.innerHTML = services.map(service => `
        <div class="service-card" onclick="window.open('https://t.me/${botUsername}', '_blank')">
            <div class="service-icon">
                <i class="${service.icon}"></i>
            </div>
            <h3 title="${service.name}">${service.name.length > 25 ? service.name.substring(0, 25) + '...' : service.name}</h3>
            <div class="price">${service.price_formatted}</div>
            <div class="service-badge">Available</div>
        </div>
    `).join('');
    
    // Add animation
    document.querySelectorAll('.service-card').forEach((card, index) => {
        card.style.animation = `fadeInUp 0.5s ease forwards ${index * 0.05}s`;
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
    });
}

function updatePagination(data) {
    const pagination = document.getElementById('pagination');
    if (!pagination) return;
    
    totalPages = data.total_pages;
    
    if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }
    
    let html = '<div class="pagination-container">';
    
    if (currentPage > 1) {
        html += `<button class="page-btn" onclick="goToPage(${currentPage - 1})"><i class="fas fa-chevron-left"></i> Previous</button>`;
    }
    
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
            html += `<button class="page-btn ${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
        } else if (i === currentPage - 3 || i === currentPage + 3) {
            html += `<span class="page-dots">...</span>`;
        }
    }
    
    if (currentPage < totalPages) {
        html += `<button class="page-btn" onclick="goToPage(${currentPage + 1})">Next <i class="fas fa-chevron-right"></i></button>`;
    }
    
    html += '</div>';
    pagination.innerHTML = html;
}

function goToPage(page) {
    currentPage = page;
    loadServices();
    window.scrollTo({ top: document.getElementById('services').offsetTop - 100, behavior: 'smooth' });
}

function updateServiceCount(total) {
    const serviceCountSpan = document.getElementById('serviceCount');
    if (serviceCountSpan) {
        serviceCountSpan.textContent = total;
    }
}

function resetSearch() {
    currentSearch = '';
    currentCategory = 'all';
    currentPage = 1;
    document.getElementById('serviceSearch').value = '';
    document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
    document.querySelector('.category-btn[data-category="all"]').classList.add('active');
    loadServices();
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .search-container {
        margin: 30px 0;
        position: relative;
    }
    
    .search-box {
        display: flex;
        align-items: center;
        background: rgba(255,255,255,0.1);
        border-radius: 50px;
        padding: 5px;
        border: 1px solid rgba(255,255,255,0.2);
        transition: all 0.3s;
    }
    
    .search-box:focus-within {
        border-color: #6366f1;
        background: rgba(255,255,255,0.15);
    }
    
    .search-box i {
        padding: 0 15px;
        color: #a0a0b0;
    }
    
    .search-box input {
        flex: 1;
        background: transparent;
        border: none;
        padding: 15px 0;
        color: white;
        font-size: 16px;
        outline: none;
    }
    
    .search-box input::placeholder {
        color: #a0a0b0;
    }
    
    .search-box button {
        background: #6366f1;
        border: none;
        color: white;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .search-box button:hover {
        background: #4f46e5;
        transform: scale(1.05);
    }
    
    .category-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
        margin-bottom: 40px;
    }
    
    .category-btn {
        padding: 8px 20px;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 30px;
        color: #a0a0b0;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 14px;
    }
    
    .category-btn i {
        margin-right: 8px;
    }
    
    .category-btn:hover, .category-btn.active {
        background: #6366f1;
        color: white;
        border-color: #6366f1;
    }
    
    .pagination {
        margin-top: 40px;
        text-align: center;
    }
    
    .pagination-container {
        display: flex;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .page-btn {
        padding: 8px 16px;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 8px;
        color: white;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .page-btn:hover, .page-btn.active {
        background: #6366f1;
        border-color: #6366f1;
    }
    
    .page-dots {
        padding: 8px 4px;
        color: #a0a0b0;
    }
    
    .no-results, .error-message {
        text-align: center;
        padding: 60px 20px;
        background: rgba(255,255,255,0.05);
        border-radius: 20px;
    }
    
    .no-results i, .error-message i {
        font-size: 48px;
        margin-bottom: 20px;
        color: #6366f1;
    }
    
    .loading-spinner {
        text-align: center;
        padding: 60px;
        font-size: 24px;
        color: #6366f1;
    }
    
    .service-badge {
        display: inline-block;
        padding: 4px 12px;
        background: rgba(16, 185, 129, 0.2);
        border-radius: 20px;
        font-size: 12px;
        color: #10b981;
        margin-top: 10px;
    }
`;
document.head.appendChild(style);

// Bot username from PHP
const botUsername = document.querySelector('.btn-telegram')?.href?.split('/').pop() || 'MoneyMakerBot';