<footer class="bg-gray-900 text-white py-12">
    <!-- Previous footer content remains the same -->
</footer>

<script>
    // Wait for Alpine.js to be ready
    document.addEventListener('alpine:init', () => {
        Alpine.data('carousel', () => ({
            currentIndex: 0,
            images: [
                'https://via.placeholder.com/300x200/1a1a1a/ffffff?text=Image+1',
                'https://via.placeholder.com/300x200/1a1a1a/ffffff?text=Image+2',
                'https://via.placeholder.com/300x200/1a1a1a/ffffff?text=Image+3'
            ],
            init() {
                setInterval(() => {
                    this.currentIndex = (this.currentIndex + 1) % this.images.length;
                }, 5000);
            }
        }));

        Alpine.data('news', () => ({
            currentNews: 0,
            news: [
                'Latest company update 1',
                'Important announcement 2',
                'New feature release 3'
            ],
            init() {
                setInterval(() => {
                    this.currentNews = (this.currentNews + 1) % this.news.length;
                }, 4000);
            }
        }));
    });
</script>

<!-- Bootstrap JS (if you're using Bootstrap) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>