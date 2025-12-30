<?php include "partials/header.php";?>
<?php include "partials/booking.php";?>
<main class="services-main">

    <!-- PAGE HEADER -->
    <section class="services-hero">
        <h1>Browse Beauty Services</h1>
        <p>Choose from top-rated professionals and services near you</p>

        <form class="services-search" autocomplete="off">
            <input id="searchInput" type="text" name="search" placeholder="Search service or category" autocomplete="off">
            <button id="clearBtn" type="submit"><i class="fa-solid fa-xmark"></i></button>
                <div id="searchSuggestions" class="search-suggestions hidden"></div>

        </form>
    </section>

    <section class="services-content">

        <!-- CATEGORY -->
        <aside class="category-sidebar">
            <h3>Categories</h3>
            <ul id="categoryList">
                       <li>Loading...</li>

            </ul>
        </aside>

        <div class="services-area">

            <!-- SUBCATEGORIES -->
            <div id="subcategoryTabs" class="subcategory-tabs">
              
            </div>
            <div class="services-scroll" id="servicesScroll">
            <div id="serviceGrid" class="service-grid">
               
            </div>
            
    </div>

        </div>
    </section>

</main>













<?php include "partials/footer.php"; ?>
