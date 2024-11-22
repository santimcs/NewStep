<footer class="bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Stay In Touch Section -->
            <div class="space-y-4">
                <h3 class="text-xl font-bold mb-4">Stay In Touch</h3>
                <div x-data="{ images: [
                    'https://via.placeholder.com/300x200/1a1a1a/ffffff?text=Image+1',
                    'https://via.placeholder.com/300x200/1a1a1a/ffffff?text=Image+2',
                    'https://via.placeholder.com/300x200/1a1a1a/ffffff?text=Image+3'
                    ], 
                    currentIndex: 0 
                }" 
                class="relative">
                    <div class="relative overflow-hidden rounded-lg h-48">
                        <template x-for="(image, index) in images" :key="index">
                            <div x-show="currentIndex === index"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform translate-x-full"
                                x-transition:enter-end="opacity-100 transform translate-x-0"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100 transform translate-x-0"
                                x-transition:leave-end="opacity-0 transform -translate-x-full"
                                class="absolute inset-0">
                                <div class="h-full w-full bg-cover bg-center"
                                    :style="`background-image: url('${image}')`"></div>
                            </div>
                        </template>
                        <!-- Navigation Buttons -->
                        <button @click="currentIndex = currentIndex === 0 ? images.length - 1 : currentIndex - 1"
                            class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-r">
                            ←
                        </button>
                        <button @click="currentIndex = currentIndex === images.length - 1 ? 0 : currentIndex + 1"
                            class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-l">
                            →
                        </button>
                    </div>
                </div>
                <p class="text-gray-400">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris ut posuere mauris, eu malesuada dui...</p>
            </div>

            <!-- Our Company Section -->
            <div class="space-y-4">
                <h3 class="text-xl font-bold mb-4">Our Company</h3>
                <div x-data="{ 
                    news: [
                        'Latest company update 1',
                        'Important announcement 2',
                        'New feature release 3'
                    ],
                    currentNews: 0
                }"
                class="relative h-48 bg-gray-800 rounded-lg p-4">
                    <div class="space-y-4">
                        <template x-for="(item, index) in news" :key="index">
                            <div x-show="currentNews === index"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform translate-y-4"
                                x-transition:enter-end="opacity-100 transform translate-y-0"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100 transform translate-y-0"
                                x-transition:leave-end="opacity-0 transform -translate-y-4"
                                class="absolute inset-x-4">
                                <p class="text-white" x-text="item"></p>
                            </div>
                        </template>
                    </div>
                    <!-- News Navigation Dots -->
                    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                        <template x-for="(_, index) in news" :key="index">
                            <button @click="currentNews = index"
                                :class="{'bg-blue-500': currentNews === index, 'bg-gray-500': currentNews !== index}"
                                class="w-2 h-2 rounded-full transition-colors duration-200"></button>
                        </template>
                    </div>
                </div>
                <p class="text-gray-400">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris ut posuere mauris, eu malesuada dui...</p>
            </div>

            <!-- Client Login Section -->
            <div class="space-y-4">
                <h3 class="text-xl font-bold mb-4">Client Log In</h3>
                <form x-data="{ 
                    email: '', 
                    password: '', 
                    loading: false,
                    submit() {
                        this.loading = true;
                        // Simulate API call
                        setTimeout(() => {
                            this.loading = false;
                            this.email = '';
                            this.password = '';
                        }, 1000);
                    }
                }" 
                @submit.prevent="submit"
                class="space-y-4">
                    <div>
                        <input type="email" 
                            x-model="email"
                            class="w-full px-4 py-2 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:border-blue-500 text-white"
                            placeholder="Enter email">
                    </div>
                    <div>
                        <input type="password" 
                            x-model="password"
                            class="w-full px-4 py-2 rounded bg-gray-800 border border-gray-700 focus:outline-none focus:border-blue-500 text-white"
                            placeholder="Password">
                    </div>
                    <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200"
                        :disabled="loading">
                        <span x-show="!loading">Submit</span>
                        <span x-show="loading" class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</footer>

<!-- Initialize Alpine.js with auto-animation for carousels -->
<script>
    document.addEventListener('alpine:init', () => {
        // Auto-advance carousel for Stay In Touch section
        setInterval(() => {
            const stayInTouch = document.querySelector('[x-data]').__x.$data;
            if (stayInTouch.currentIndex !== undefined) {
                stayInTouch.currentIndex = 
                    stayInTouch.currentIndex === stayInTouch.images.length - 1 
                        ? 0 
                        : stayInTouch.currentIndex + 1;
            }
        }, 5000);

        // Auto-advance news items
        setInterval(() => {
            const newsSection = document.querySelectorAll('[x-data]')[1].__x.$data;
            if (newsSection.currentNews !== undefined) {
                newsSection.currentNews = 
                    newsSection.currentNews === newsSection.news.length - 1 
                        ? 0 
                        : newsSection.currentNews + 1;
            }
        }, 4000);
    });
</script>