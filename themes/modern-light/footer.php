                </div>
            </main>
        </div>
    </div>
    
    <!-- Theme-specific JavaScript -->
    <script>
        // Initialize Feather icons
        feather.replace();
        
        // Add smooth transitions and animations
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading animation to forms
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.classList.add('opacity-75');
                        submitBtn.disabled = true;
                        
                        // Re-enable after a delay if form doesn't submit
                        setTimeout(() => {
                            submitBtn.classList.remove('opacity-75');
                            submitBtn.disabled = false;
                        }, 3000);
                    }
                });
            });
            
            // Add hover effects to cards
            const cards = document.querySelectorAll('.bg-white');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.classList.add('shadow-lg');
                });
                card.addEventListener('mouseleave', function() {
                    this.classList.remove('shadow-lg');
                });
            });
            
            // Smooth scroll for in-page links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50, .bg-yellow-50');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.remove();
                }, 500);
            });
        }, 5000);
    </script>
    
    <!-- Include main app JavaScript -->
    <script src="/assets/js/app.js"></script>
</body>
</html>
