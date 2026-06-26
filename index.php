<?php
$dbAvailable = false;
try {
    require __DIR__ . '/admin/config.php';
    $dbAvailable = true;

    // Fetch settings
    $settings = [];
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM cms_settings");
    foreach ($stmt as $row) $settings[$row['setting_key']] = $row['setting_value'];

    // Fetch sections
    $sections = [];
    $stmt = $pdo->query("SELECT section_key, title, subtitle, content, image_url FROM cms_sections");
    foreach ($stmt as $row) $sections[$row['section_key']] = $row;

    // Fetch nav items
    $navItems = $pdo->query("SELECT label, href FROM cms_navigation WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);

    // Fetch social links
    $socialLinks = $pdo->query("SELECT platform, url, icon FROM cms_social_links WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);

    // Fetch portfolio items
    $portfolioItems = $pdo->query("SELECT video_src, instagram_url FROM cms_portfolio WHERE is_active=1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);

    // Fetch pricing
    $pricing = $pdo->query("SELECT * FROM cms_pricing WHERE is_active=1 ORDER BY FIELD(category,'wedding','lifestyle','event'), sort_order")->fetchAll(PDO::FETCH_ASSOC);
    $pricingFeatures = [];
    $stmt = $pdo->query("SELECT pricing_id, feature_text FROM cms_pricing_features ORDER BY sort_order");
    foreach ($stmt as $row) $pricingFeatures[$row['pricing_id']][] = $row['feature_text'];

} catch (Exception $e) {
    $dbAvailable = false;
}

// Fallback defaults
$siteName = $settings['site_name'] ?? 'JenniferLamiVisuals';
$metaTitle = $settings['meta_title'] ?? 'JenniferLamiVisuals - Professional Videography Services';
$metaDesc = $settings['meta_description'] ?? 'Professional Videography Services. Cinematic videos for weddings, events, and commercials.';
$metaKeywords = $settings['meta_keywords'] ?? 'videography, wedding videographer, event videography';
$ogTitle = $settings['og_title'] ?? 'JenniferLamiVisuals - Professional Videography Services';
$ogDesc = $settings['og_description'] ?? 'Cinematic wedding films, event coverage, and brand promos.';
$ogImage = $settings['og_image'] ?? 'images/header.png';
$contactEmail = $settings['contact_email'] ?? 'info@jenniferlamivisuals.com';
$contactPhone = $settings['contact_phone'] ?? '08060425569';
$footerCopyright = $settings['footer_copyright'] ?? 'Copyright &copy; 2026 JenniferLamiVisuals';
$jsonPriceRange = $settings['json_ld_price_range'] ?? '$200-$2500';
$jsonServiceTypes = $settings['json_ld_service_types'] ?? 'Wedding Videography,Event Videography,Commercial Video Production,Lifestyle Videography,Social Media Content Creation';

$heroImg = $sections['hero_image']['image_url'] ?? 'images/header.png';
$aboutHeading = $sections['about_heading']['title'] ?? 'About JenniferLami Visuals';
$aboutImg = $sections['about_heading']['image_url'] ?? 'images/ceo.png';
$aboutText1 = $sections['about_text_1']['content'] ?? "A documentary-trained eye that finds beauty in unscripted moments, I capture weddings, events, lifestyle and brands with cinematic storytelling and professional polish. Full wedding films, event highlights, brand promos — emotional, timeless, high-end.";
$aboutText2 = $sections['about_text_2']['content'] ?? "But I also live in the fast world of social media. Using just my iPhone, I create scroll-stopping, trend-native Reels, TikToks, and Instagram Stories for events and brands in real time.";
$aboutText3 = $sections['about_text_3']['content'] ?? "Whether you're a couple wanting a timeless wedding film + instant social magic, I'm here to make your story look and feel incredible.";
$aboutTagline = $sections['about_tagline']['title'] ?? 'Your Vision, My Lens';
$portfolioTitle = $sections['portfolio_heading']['title'] ?? 'Featured on Instagram';
$portfolioSubtitle = $sections['portfolio_heading']['subtitle'] ?? 'Check out our latest reels and shots directly from our feed.';
$contactHeading = $sections['contact_heading']['title'] ?? 'Interested? Let\'s talk';
$footerSiteName = $sections['footer_site_name']['title'] ?? 'JenniferLami Visuals';

$serviceTypes = explode(',', $jsonServiceTypes);

$nav = $dbAvailable && !empty($navItems) ? $navItems : [
    ['label' => 'Home', 'href' => '#section_1'],
    ['label' => 'About', 'href' => '#section_2'],
    ['label' => 'Portfolio', 'href' => '#section_3'],
    ['label' => 'Pricing', 'href' => '#section_5'],
    ['label' => 'Contact', 'href' => '#section_6'],
];

$social = $dbAvailable && !empty($socialLinks) ? $socialLinks : [
    ['platform' => 'Facebook', 'url' => 'https://www.facebook.com/share/1Hf2ocPwf8/', 'icon' => 'bi-facebook'],
    ['platform' => 'TikTok', 'url' => 'https://www.tiktok.com/@jenniferlamivisuals', 'icon' => 'bi-tiktok'],
    ['platform' => 'Instagram', 'url' => 'https://www.instagram.com/jenniferlamivisuals/', 'icon' => 'bi-instagram'],
];

$portfolio = $dbAvailable && !empty($portfolioItems) ? $portfolioItems : [
    ['video_src' => 'video/DYa5IslykU-.mp4', 'instagram_url' => 'https://www.instagram.com/reel/DYa5IslykU-/'],
    ['video_src' => 'video/DYNKFW3ynKV.mp4', 'instagram_url' => 'https://www.instagram.com/reel/DYNKFW3ynKV/'],
    ['video_src' => 'video/DWwrGTFkvTW.mp4', 'instagram_url' => 'https://www.instagram.com/reel/DWwrGTFkvTW/'],
];

// Group pricing by category
$pricingByCategory = ['wedding' => [], 'lifestyle' => [], 'event' => []];
if ($dbAvailable && !empty($pricing)) {
    foreach ($pricing as $p) {
        $p['features'] = $pricingFeatures[$p['id']] ?? [];
        $pricingByCategory[$p['category']][] = $p;
    }
} else {
    // Hardcoded fallback pricing
    $pricingByCategory = [
        'wedding' => [
            ['package_name' => 'Basic Wedding', 'price' => '$1500', 'subtitle' => '7 Hours Coverage', 'is_featured' => 0, 'features' => ['5-10 mins full length', '2-3 mins Highlight', '3 Reels']],
            ['package_name' => 'Premium Wedding', 'price' => '$2500', 'subtitle' => '10- Hours Event Coverage', 'is_featured' => 1, 'features' => ['10-15 mins full length', '2 Highlights', '2 Reels', 'TikTok/IG Content']],
            ['package_name' => 'Standard Wedding', 'price' => '$600', 'subtitle' => '7 Hours Coverage', 'is_featured' => 0, 'features' => ['2-3 mins Highlight', '2 Reels', 'TikTok/IG Content']],
        ],
        'lifestyle' => [
            ['package_name' => 'Photoshoot', 'price' => '$250', 'subtitle' => '2 Hours Coverage', 'is_featured' => 0, 'features' => ['2 Reels', 'TikTok/IG Content']],
            ['package_name' => 'Ad Campaign', 'price' => '$500', 'subtitle' => '9 Hours Coverage', 'is_featured' => 1, 'features' => ['1 Add', '2 Reels/Teaser', 'TikTok/IG Content']],
            ['package_name' => 'Product Shoot', 'price' => '$200', 'subtitle' => '2 Hours Coverage', 'is_featured' => 0, 'features' => ['3 Reels']],
        ],
        'event' => [
            ['package_name' => 'Basic Event', 'price' => '$250', 'subtitle' => '2-3 Hours Coverage', 'is_featured' => 0, 'features' => ['1 Highlight (1-2 Mins)', '1 Reels/Teaser']],
            ['package_name' => 'Premium Event', 'price' => '$500', 'subtitle' => 'Full Event Coverage', 'is_featured' => 1, 'features' => ['1 Highlight (1-3 Mins)', '2 Reels/Teaser', 'TikTok/IG Content']],
            ['package_name' => 'Standard Event', 'price' => '$350', 'subtitle' => '2-4 Hours Coverage', 'is_featured' => 0, 'features' => ['1 Highlight (1-3 Mins)', '2 Reels/Teaser']],
        ],
    ];
}

// Category meta
$categories = [
    'wedding' => ['icon' => 'bi-heart-fill', 'title' => 'Wedding Package', 'desc' => 'Cinematic wedding films & social content', 'id' => 'wedding'],
    'lifestyle' => ['icon' => 'images/light.png', 'title' => 'Lifestyle & Brand Package', 'desc' => 'Professional shoots & ad campaigns', 'id' => 'lifestyle'],
    'event' => ['icon' => 'bi-calendar-event-fill', 'title' => 'Event Package', 'desc' => 'Full event coverage & highlights', 'id' => 'event'],
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= htmlspecialchars($metaDesc) ?>">
    <meta name="author" content="<?= htmlspecialchars($siteName) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($metaKeywords) ?>">
    <link rel="canonical" href="https://jenniferlamivisuals.com/">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= htmlspecialchars($ogTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($ogDesc) ?>">
    <meta property="og:url" content="https://jenniferlamivisuals.com/">
    <meta property="og:image" content="https://jenniferlamivisuals.com/<?= htmlspecialchars($ogImage) ?>">
    <meta property="og:site_name" content="<?= htmlspecialchars($siteName) ?>">
    <meta property="og:locale" content="en_US">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($ogTitle) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($ogDesc) ?>">
    <meta name="twitter:image" content="https://jenniferlamivisuals.com/<?= htmlspecialchars($ogImage) ?>">
    <title><?= htmlspecialchars($metaTitle) ?></title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/lost-in-south" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-icons.css" rel="stylesheet">
    <link href="css/templatemo-festava-live.css" rel="stylesheet">
    <link href="css/custom-instagram.css" rel="stylesheet">
    <link href="css/rate-card.css" rel="stylesheet">
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "LocalBusiness",
      "name": "<?= htmlspecialchars($settings['json_ld_business_name'] ?? $siteName) ?>",
      "description": "<?= htmlspecialchars($metaDesc) ?>",
      "url": "https://jenniferlamivisuals.com",
      "telephone": "<?= htmlspecialchars($contactPhone) ?>",
      "email": "<?= htmlspecialchars($contactEmail) ?>",
      "image": "https://jenniferlamivisuals.com/<?= htmlspecialchars($ogImage) ?>",
      "logo": "https://jenniferlamivisuals.com/images/logo.png",
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Jacksonville",
        "addressRegion": "FL",
        "addressCountry": "US"
      },
      "priceRange": "<?= htmlspecialchars($jsonPriceRange) ?>",
      "sameAs": [<?php foreach ($social as $i => $s): ?>"<?= htmlspecialchars($s['url']) ?>"<?= $i < count($social) - 1 ? ',' : '' ?><?php endforeach; ?>],
      "serviceType": [<?php foreach ($serviceTypes as $i => $st): ?>"<?= htmlspecialchars(trim($st)) ?>"<?= $i < count($serviceTypes) - 1 ? ',' : '' ?><?php endforeach; ?>]
    }
    </script>
</head>
<body>
<main>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.png" class="logo img-fluid" alt="<?= htmlspecialchars($siteName) ?> Logo">
            </a>
            <a href="booking.html" class="btn custom-btn d-lg-none ms-auto me-4">Book Now</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav align-items-lg-center ms-auto me-lg-5">
                    <?php foreach ($nav as $item): ?>
                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="<?= htmlspecialchars($item['href']) ?>"><?= htmlspecialchars($item['label']) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <a href="booking.html" class="btn custom-btn d-lg-block d-none">Book Now</a>
            </div>
        </div>
    </nav>

    <section class="hero-section" id="section_1">
        <div class="container d-flex justify-content-center align-items-center">
            <div class="row">
                <div class="col-12 mt-auto mb-5 text-center"></div>
                <div class="col-lg-12 col-12 mt-auto d-flex flex-column flex-lg-row text-center">
                    <div class="social-share">
                        <ul class="social-icon d-flex align-items-center justify-content-center">
                            <span class="text-white me-3">Follow:</span>
                            <?php foreach ($social as $s): ?>
                                <li class="social-icon-item">
                                    <a href="<?= htmlspecialchars($s['url']) ?>" target="_blank" rel="noopener noreferrer" class="social-icon-link">
                                        <span class="<?= htmlspecialchars($s['icon']) ?>"></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="video-wrap">
            <img src="<?= htmlspecialchars($heroImg) ?>" class="custom-video img-fluid" alt="<?= htmlspecialchars($siteName) ?> - Portfolio Showcase">
        </div>
    </section>

    <section class="about-section section-padding" id="section_2">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-12 mb-4 mb-lg-0 d-flex align-items-center reveal reveal-left">
                    <div class="services-info">
                        <h2 class="text-white mb-4"><?= htmlspecialchars($aboutHeading) ?></h2>
                        <p class="text-white"><?= htmlspecialchars($aboutText1) ?></p>
                        <p class="text-white"><?= htmlspecialchars($aboutText2) ?></p>
                        <p class="text-white"><?= htmlspecialchars($aboutText3) ?></p>
                    </div>
                </div>
                <div class="col-lg-6 col-12 reveal reveal-right">
                    <div class="about-text-wrap">
                        <img src="<?= htmlspecialchars($aboutImg) ?>" class="about-image img-fluid" alt="About <?= htmlspecialchars($siteName) ?>">
                        <div class="about-text-info d-flex">
                            <div class="d-flex">
                                <i class="about-text-icon bi-camera"></i>
                            </div>
                            <div class="ms-4">
                                <h3><?= htmlspecialchars($aboutTagline) ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="artists-section section-padding" id="section_3">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 text-center">
                    <h2 class="mb-4"><?= htmlspecialchars($portfolioTitle) ?></h2>
                    <p class="mb-5"><?= htmlspecialchars($portfolioSubtitle) ?></p>
                </div>
                <?php foreach ($portfolio as $i => $item): ?>
                    <div class="col-lg-4 col-md-6 col-12 mb-4 reveal reveal-up reveal-delay-<?= min($i + 1, 3) ?>">
                        <div class="instagram-embed-container">
                            <video src="<?= htmlspecialchars($item['video_src']) ?>#t=0.001" controls preload="metadata" style="width: 100%; display: block; aspect-ratio: 9/16; object-fit: cover;"></video>
                            <a href="<?= htmlspecialchars($item['instagram_url']) ?>" target="_blank" class="instagram-overlay-btn" title="Watch on Instagram">
                                <i class="bi-instagram"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="rate-card-section" id="section_5">
        <div class="container">
            <div class="rate-card-header">
                <div class="subtitle1"><?= htmlspecialchars($siteName) ?></div>
                <div class="subtitle">Pricing Card</div>
            </div>
            <div class="category-cards-container reveal reveal-up">
                <?php foreach ($categories as $ck => $cat): ?>
                    <div class="category-card" onclick="togglePricing('<?= $cat['id'] ?>')">
                        <div class="category-icon">
                            <?php if (str_starts_with($cat['icon'], 'bi-')): ?>
                                <i class="<?= $cat['icon'] ?>"></i>
                            <?php else: ?>
                                <img src="<?= $cat['icon'] ?>" alt="<?= htmlspecialchars($cat['title']) ?> Icon">
                            <?php endif; ?>
                        </div>
                        <h3><?= htmlspecialchars($cat['title']) ?></h3>
                        <p><?= htmlspecialchars($cat['desc']) ?></p>
                        <div class="category-arrow">
                            <i class="bi-chevron-down" id="<?= $cat['id'] ?>-arrow"></i>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php foreach ($pricingByCategory as $ck => $packages): ?>
                <div class="pricing-details" id="<?= $ck ?>-pricing" style="display:none;">
                    <div class="rate-card-row">
                        <?php foreach ($packages as $p): ?>
                            <div class="rate-card <?= $p['is_featured'] ? 'rate-card-black' : 'rate-card-white' ?>">
                                <h3><?= htmlspecialchars($p['package_name']) ?></h3>
                                <div class="price"><?= htmlspecialchars($p['price']) ?></div>
                                <div class="sub-price"><?= htmlspecialchars($p['subtitle'] ?? '') ?></div>
                                <?php foreach ($p['features'] as $f): ?>
                                    <div class="rate-feature">
                                        <i class="<?= $p['is_featured'] ? 'bi-play-btn-fill' : 'bi-play-btn' ?>"></i>
                                        <span><?= htmlspecialchars($f) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="contact-section section-padding" id="section_6">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-12 mx-auto">
                    <h3 class="text-center mb-4"><?= htmlspecialchars($contactHeading) ?></h3>
                    <nav class="d-flex justify-content-center">
                        <div class="nav nav-tabs align-items-baseline justify-content-center" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-ContactForm-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-ContactForm" type="button" role="tab"
                                aria-controls="nav-ContactForm" aria-selected="false">
                                <h5>Contact Form</h5>
                            </button>
                        </div>
                    </nav>
                    <div class="tab-content shadow-lg mt-5" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-ContactForm" role="tabpanel"
                            aria-labelledby="nav-ContactForm-tab">
                            <div id="contact-message-status" class="mb-4 text-center" style="display: none; padding: 15px; border-radius: 5px;"></div>
                            <form id="contact-form" class="custom-form contact-form mb-5 mb-lg-0" action="send_email.php" method="post" role="form">
                                <div class="contact-form-body">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-12">
                                            <input type="text" name="contact-name" id="contact-name" class="form-control" placeholder="Full name" required>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-12">
                                            <input type="email" name="contact-email" id="contact-email" pattern="[^ @]*@[^ @]*" class="form-control" placeholder="Email address" required>
                                        </div>
                                    </div>
                                    <input type="tel" name="contact-phone" id="contact-phone" class="form-control" placeholder="Phone Number" required>
                                    <textarea name="contact-message" rows="3" class="form-control" id="contact-message" placeholder="Message"></textarea>
                                    <div class="col-lg-4 col-md-10 col-8 mx-auto">
                                        <button type="submit" class="form-control">Send message</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="site-footer">
    <div class="site-footer-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-12">
                    <h2 class="text-white mb-lg-0"><?= htmlspecialchars($footerSiteName) ?></h2>
                </div>
                <div class="col-lg-6 col-12 d-flex justify-content-lg-end align-items-center">
                    <ul class="social-icon d-flex justify-content-lg-end">
                        <?php foreach ($social as $s): ?>
                            <li class="social-icon-item">
                                <a href="<?= htmlspecialchars($s['url']) ?>" target="_blank" class="social-icon-link">
                                    <span class="<?= htmlspecialchars($s['icon']) ?>"></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="site-footer-bottom">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-12 mt-5">
                    <p class="copyright-text"><?= $footerCopyright ?></p>
                </div>
                <div class="col-lg-8 col-12 mt-lg-5">
                    <ul class="site-footer-links">
                        <li class="site-footer-link-item">
                            <a href="#" class="site-footer-link">Terms &amp; Conditions</a>
                        </li>
                        <li class="site-footer-link-item">
                            <a href="#" class="site-footer-link">Privacy Policy</a>
                        </li>
                        <li class="site-footer-link-item">
                            <a href="#" class="site-footer-link">Your Feedback</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/click-scroll.js"></script>
<script src="js/custom.js"></script>
<script>
$(document).ready(function() {
    $('#contact-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var statusDiv = $('#contact-message-status');
        var submitBtn = form.find('button[type="submit"]');
        var originalBtnText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Sending...');
        statusDiv.hide().removeClass('alert-success alert-danger').css('display', 'none');
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            dataType: 'json',
            success: function(response) {
                statusDiv.text(response.message).css({
                    'display': 'block',
                    'background-color': response.status === 'success' ? '#d4edda' : '#f8d7da',
                    'color': response.status === 'success' ? '#155724' : '#721c24',
                    'border': response.status === 'success' ? '1px solid #c3e6cb' : '1px solid #f5c6cb'
                }).fadeIn();
                if (response.status === 'success') form[0].reset();
            },
            error: function() {
                statusDiv.text('An error occurred. Please try again later.').css({
                    'display': 'block',
                    'background-color': '#f8d7da', 'color': '#721c24',
                    'border': '1px solid #f5c6cb'
                }).fadeIn();
            },
            complete: function() { submitBtn.prop('disabled', false).text(originalBtnText); }
        });
    });
});
</script>
</body>
</html>
