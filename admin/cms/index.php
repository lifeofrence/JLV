<?php $pageTitle = 'Dashboard'; include 'header.php'; ?>
<div class="container-fluid py-4">
    <h4 class="mb-4"><i class="bi-grid-fill"></i> Content Management</h4>
    <div class="row g-4">
        <div class="col-md-4">
            <a href="sections.php" class="text-decoration-none">
                <div class="card bg-dark border-secondary h-100">
                    <div class="card-body text-center py-5">
                        <i class="bi-layers-fill" style="font-size: 3rem; color: #ee5007;"></i>
                        <h5 class="mt-3 text-light">Page Sections</h5>
                        <p class="text-secondary mb-0">Edit hero, about, portfolio header, contact, and footer text</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="pricing.php" class="text-decoration-none">
                <div class="card bg-dark border-secondary h-100">
                    <div class="card-body text-center py-5">
                        <i class="bi-currency-dollar" style="font-size: 3rem; color: #ee5007;"></i>
                        <h5 class="mt-3 text-light">Pricing Packages</h5>
                        <p class="text-secondary mb-0">Manage wedding, lifestyle & event packages with features</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="portfolio.php" class="text-decoration-none">
                <div class="card bg-dark border-secondary h-100">
                    <div class="card-body text-center py-5">
                        <i class="bi-play-btn-fill" style="font-size: 3rem; color: #ee5007;"></i>
                        <h5 class="mt-3 text-light">Portfolio Videos</h5>
                        <p class="text-secondary mb-0">Add/edit Instagram video embeds and links</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="navigation.php" class="text-decoration-none">
                <div class="card bg-dark border-secondary h-100">
                    <div class="card-body text-center py-5">
                        <i class="bi-list-ul" style="font-size: 3rem; color: #ee5007;"></i>
                        <h5 class="mt-3 text-light">Navigation Menu</h5>
                        <p class="text-secondary mb-0">Edit navbar links and order</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="social.php" class="text-decoration-none">
                <div class="card bg-dark border-secondary h-100">
                    <div class="card-body text-center py-5">
                        <i class="bi-share" style="font-size: 3rem; color: #ee5007;"></i>
                        <h5 class="mt-3 text-light">Social Links</h5>
                        <p class="text-secondary mb-0">Manage social media URLs displayed on site</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="settings.php" class="text-decoration-none">
                <div class="card bg-dark border-secondary h-100">
                    <div class="card-body text-center py-5">
                        <i class="bi-gear-wide-connected" style="font-size: 3rem; color: #ee5007;"></i>
                        <h5 class="mt-3 text-light">Site Settings & SEO</h5>
                        <p class="text-secondary mb-0">Site name, contact info, meta tags, and JSON-LD</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
