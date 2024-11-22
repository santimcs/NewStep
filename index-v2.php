<?php include('inc/header.php'); ?>

<div class="container page-content">
            
    <div class="hero row">
                
        <div id="carousel-example-generic" class="carousel slide">
            <!-- Indicators -->
            <ol class="carousel-indicators">
                <li data-bs-target="#carousel-example-generic" data-bs-slide-to="0" class="active"></li>
                <li data-bs-target="#carousel-example-generic" data-bs-slide-to="1"></li>
                <li data-bs-target="#carousel-example-generic" data-bs-slide-to="2"></li>
            </ol>
    
            <!-- Wrapper for slides -->
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="img/nature-1.jpg" class="d-block w-100" alt="Nature 1">
                </div>
                <div class="carousel-item">
                    <img src="img/nature-2.jpg" class="d-block w-100" alt="Nature 2">
                </div>
                <div class="carousel-item">
                    <img src="img/nature-3.jpg" class="d-block w-100" alt="Nature 3">
                </div>
            </div>
    
            <!-- Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#carousel-example-generic" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carousel-example-generic" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div><!--end slider -->
            
    </div><!--end hero -->
            
    <div class="row">
        <div class="col-md-8 offset-md-2 welcome">
            <h1>Welcome To Our Website!</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean mollis risus quis turpis semper suscipit. Etiam vel mollis lorem, sed cursus justo. Praesent ultrices tempus volutpat.</p>
        </div><!--welcome -->
    </div><!--end row -->
            
    <div class="row">
        <div class="col-sm-4 col-md-4">
            <div class="thumbnail">
                <img src="img/feat-4.jpg" alt="Professional Appeal" />
                <div class="caption">
                    <h3>Professional Appeal</h3>
                    <p>Vivamus laoreet diam at malesuada elementum. Vivamus euismod lobortis eros et aliquam.</p>
                    <p><a href="#" class="btn btn-default">Get Started!</a></p>
                </div>
            </div><!--end thumbnail -->
        </div><!--end third -->

        <div class="col-sm-4 col-md-4">
            <div class="thumbnail">
                <img src="img/feat-5.jpg" alt="Friendly Service" />
                <div class="caption">
                    <h3>Friendly Service</h3>
                    <p>Vivamus laoreet diam at malesuada elementum. Vivamus euismod lobortis eros et aliquam.</p>
                    <p><a href="#" class="btn btn-primary">Get Started!</a></p>
                </div>
            </div><!--end thumbnail -->
        </div><!--end third -->

        <div class="col-sm-4 col-md-4">
            <div class="thumbnail">
                <img src="img/feat-6.jpg" alt="Simple Contact" />
                <div class="caption">
                    <h3>Simple Contact</h3>
                    <p>Vivamus laoreet diam at malesuada elementum. Vivamus euismod lobortis eros et aliquam.</p>
                    <p><a href="#" class="btn btn-default">Learn More...</a></p>
                </div>
            </div><!--end thumbnail -->
        </div><!--end third -->
    </div><!--end row -->
            
</div><!--end container -->
        
<?php include('inc/footer.php'); ?>