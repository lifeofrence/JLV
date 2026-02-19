
(function ($) {

  "use strict";

  // MENU
  $('.navbar-collapse a').on('click', function () {
    $(".navbar-collapse").collapse('hide');
  });

  // CUSTOM LINK
  $('.smoothscroll').click(function () {
    var el = $(this).attr('href');
    var elWrapped = $(el);
    var header_height = $('.navbar').height();

    scrollToDiv(elWrapped, header_height);
    return false;

    function scrollToDiv(element, navheight) {
      var offset = element.offset();
      var offsetTop = offset.top;
      var totalScroll = offsetTop - navheight;

      $('body,html').animate({
        scrollTop: totalScroll
      }, 300);
    }
  });

  // Force Instagram Embeds to re-process on load
  if (window.instgrm) {
    window.instgrm.Embeds.process();
  }

  // Scroll Animation Observer
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('active');
        // Once animated, no need to observe anymore
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  document.querySelectorAll('.reveal').forEach(el => {
    observer.observe(el);
  });

})(window.jQuery);

// Pricing Category Toggle
function togglePricing(category) {
  var pricingElement = document.getElementById(category + '-pricing');
  var categoryCards = document.querySelectorAll('.category-card');
  var clickedCard = event.currentTarget;

  // Check if this category is currently open
  var isOpen = pricingElement.style.display === 'block';

  // Close all pricing details
  document.querySelectorAll('.pricing-details').forEach(function (el) {
    el.style.display = 'none';
  });

  // Remove active class from all category cards
  categoryCards.forEach(function (card) {
    card.classList.remove('active');
  });

  // If it wasn't open, open it and add active class
  if (!isOpen) {
    pricingElement.style.display = 'block';
    clickedCard.classList.add('active');

    // Smooth scroll to pricing details
    setTimeout(function () {
      pricingElement.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }, 100);
  }
}
