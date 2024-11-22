<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NewStep</title>
    <style>
        nav {
            background-color: black; /* Set background to black */
            padding: 10px;
        }

        nav a {
            color: white; /* Set text color to white */
            text-decoration: none; /* Remove underline from links */
            padding: 10px;
            display: inline-block;
        }

        nav a:hover {
            color: lightgray; /* Optional: Change text color on hover */
        }
    </style>
    <!-- Load Tailwind and Alpine.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Alternative: Use Bootstrap if you prefer -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Bootstrap CSS -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- jQuery (if needed) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

</head>
<body>
    <header>
        <nav x-data="{ isOpen: false }" class="bg-white shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="index.php" class="text-primary text-xl font-bold">NewStep</a>
                    </div>
                    
                    <!-- Mobile menu button -->
                    <div class="flex items-center md:hidden">
                        <button 
                            @click="isOpen = !isOpen" 
                            class="inline-flex items-center justify-center p-2 rounded-md text-secondary hover:text-highlight hover:bg-gray-100"
                        >
                            <svg 
                                class="h-6 w-6" 
                                x-show="!isOpen" 
                                xmlns="http://www.w3.org/2000/svg" 
                                fill="none" 
                                viewBox="0 0 24 24" 
                                stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg 
                                class="h-6 w-6" 
                                x-show="isOpen" 
                                xmlns="http://www.w3.org/2000/svg" 
                                fill="none" 
                                viewBox="0 0 24 24" 
                                stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Desktop menu -->
                    <div class="hidden md:flex md:items-center md:space-x-4">
                        <a href="PlFrm3.php" class="text-secondary hover:text-highlight hover:bg-blue-50 px-3 py-2 rounded-md text-sm">Portfolio</a>
                        <a href="YieldFrm3.php" class="text-secondary hover:text-highlight hover:bg-blue-50 px-3 py-2 rounded-md text-sm">Dividend</a>
                        <a href="display_results.php" class="text-secondary hover:text-highlight hover:bg-blue-50 px-3 py-2 rounded-md text-sm">Div Compare</a>
                        <a href="SetIndexInq4.php" class="text-secondary hover:text-highlight hover:bg-blue-50 px-3 py-2 rounded-md text-sm">Set Index</a>
                        <a href="PriceFrm3.php" class="text-secondary hover:text-highlight hover:bg-blue-50 px-3 py-2 rounded-md text-sm">Price</a>
                        <a href="LoHiFrm2.php" class="text-secondary hover:text-highlight hover:bg-blue-50 px-3 py-2 rounded-md text-sm">Low/High</a>
                        <a href="FrqncyFrm3.php" class="text-secondary hover:text-highlight hover:bg-blue-50 px-3 py-2 rounded-md text-sm">Day Trade Frequency</a>
                        <a href="DayTradeInq3.php" class="text-secondary hover:text-highlight hover:bg-blue-50 px-3 py-2 rounded-md text-sm">Day Trade Price</a>
                        <a href="BHSInq3.php" class="text-secondary hover:text-highlight hover:bg-blue-50 px-3 py-2 rounded-md text-sm">IAA Consensus</a>
                        <a href="BHSInq10.php" class="text-secondary hover:text-highlight hover:bg-blue-50 px-3 py-2 rounded-md text-sm">IAA Consensus 10</a>
                        <a href="PerFrm3.php" class="text-secondary hover:text-highlight hover:bg-blue-50 px-3 py-2 rounded-md text-sm">P/E Ratio</a>
                    </div>
                </div>
                
                <!-- Mobile menu -->
                <div 
                    x-show="isOpen" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="md:hidden"
                >
                    <div class="px-2 pt-2 pb-3 space-y-1">
                        <a href="PlFrm3.php" class="block text-secondary hover:text-highlight hover:bg-blue-50 px-3 py-2 rounded-md text-sm">Portfolio</a>
                        <a href="YieldFrm3.php" class="block text-secondary hover:text-highlight hover:bg-blue-50 px-3 py-2 rounded-md text-sm">Dividend</a>
                        <a href="display_results.php" class="block text-secondary hover:text-highlight hover:bg-blue-50 px-3 py-2 rounded-md text-sm">Div Compare</a>
                        <a href="SetIndexInq4.php" class="block text-secondary hover:text-highlight hover:bg-blue-50 px-3 py-2 rounded-md text-sm">Set Index</a>
                        <a href="PriceFrm3.php" class="block text-secondary hover:text-highlight hover:bg-blue-50 px-3 py-2 rounded-md text-sm">Price</a>
                        <a href="LoHiFrm2.php" class="block text-secondary hover:text-highlight hover:bg-blue-50 px-3 py-2 rounded-md text-sm">Low/High</a>
                        <a href="FrqncyFrm3.php" class="block text-secondary hover:text-highlight hover:bg-blue-50 px-3 py-2 rounded-md text-sm">Day Trade Frequency</a>
                        <a href="DayTradeInq3.php" class="block text-secondary hover:text-highlight hover:bg-blue-50 px-3 py-2 rounded-md text-sm">Day Trade Price</a>
                        <a href="BHSInq3.php" class="block text-secondary hover:text-highlight hover:bg-blue-50 px-3 py-2 rounded-md text-sm">IAA Consensus</a>
                        <a href="BHSInq10.php" class="block text-secondary hover:text-highlight hover:bg-blue-50 px-3 py-2 rounded-md text-sm">IAA Consensus 10</a>
                        <a href="PerFrm3.php" class="block text-secondary hover:text-highlight hover:bg-blue-50 px-3 py-2 rounded-md text-sm">P/E Ratio</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>
</body>
</html>