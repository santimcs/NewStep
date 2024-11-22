<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClearStep</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        .navbar {
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c3e50 !important;
        }
        
        .navbar-nav {
            margin-left: 1rem;
        }
        
        .nav-link {
            color: #34495e !important;
            padding: 0.5rem 1rem !important;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: #3498db !important;
            background-color: rgba(52, 152, 219, 0.1);
            border-radius: 4px;
        }
        
        @media (max-width: 991px) {
            .navbar-nav {
                margin-left: 0;
                padding: 1rem 0;
            }
            
            .nav-link {
                padding: 0.5rem 0 !important;
            }
        }
        
        /* Style for Portfolio link to match other links */
        .nav-item a {
            text-decoration: none;
            color: #34495e !important;
            padding: 0.5rem 1rem;
            display: block;
        }
        
        .nav-item a:hover {
            color: #3498db !important;
            background-color: rgba(52, 152, 219, 0.1);
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">ClearStep</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">About Us</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Daily</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Set Index</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Set Date</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Low/High</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Day Trade Frequency</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Day Trade Price</a></li>
                        <li class="nav-item"><a href="PlFrm3.php">Portfolio</a></li>    
                        <li class="nav-item"><a class="nav-link" href="#">Dividend</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Weekly</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">IAA Consensus</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">IAA Consensus 10</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">P/E Ratio</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>