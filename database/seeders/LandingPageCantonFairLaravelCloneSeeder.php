<?php

namespace Database\Seeders;

use App\Models\LandingPage;
use Illuminate\Database\Seeder;

class LandingPageCantonFairLaravelCloneSeeder extends Seeder
{
    public function run(): void
    {
        LandingPage::query()->where('slug', '!=', 'cantonfair-laravel-native')->update(['is_active' => false]);

        LandingPage::updateOrCreate(
            ['slug' => 'cantonfair-laravel-native'],
            [
                'name' => 'Canton Fair Laravel Native',
                'industry' => 'Events / Booths / Trips',
                'headline' => 'Canton Fair Adventure Trip',
                'html_content' => <<<'HTML'
<main class="cf-page">
  <section class="cf-hero">
    <div class="cf-hero-overlay"></div>
    <div class="cf-container cf-hero-content">
      <div class="cf-logo-wrap">
        <img src="/lovable-uploads/9b4b4998-9c58-40da-84d0-9f40ce422184.png" alt="KHB Events Logo" class="cf-logo">
      </div>
      <h1>Join Canton Fair 2025 With <span class="cf-gold">KHB Events</span> - Just $499!</h1>
      <h2>ចូលរួមដំណើរទស្សនកិច្ចពាណិជ្ជកម្មទៅកាន់ Canton Fair 2025 ជាមួយ KHB Events<br><span class="cf-gold-text">ចំនួនមានកំណត់ តែ 25 នាក់ប៉ុណ្ណោះ!</span></h2>

      <div class="cf-hero-stats">
        <article><strong>70,000+</strong><span>Booths & Exhibitors</span></article>
        <article><strong>200+</strong><span>Countries Participating</span></article>
        <article><strong>25</strong><span>Seats Available</span></article>
      </div>

      <p class="cf-hero-desc">
        The world's largest trade fair with 70,000+ booths and 200+ countries - don't miss this chance to connect directly with manufacturers!
      </p>

      <button class="cf-btn cf-btn-primary cf-btn-lg" onclick="cfScrollToBooking('hero-book');">ចុះឈ្មោះឥឡូវនេះ (Reserve Your Seat Now)</button>
      <p class="cf-urgency">🔥 Limited Seats Available - First Come, First Served! 🔥</p>
    </div>
  </section>

  <section class="cf-section cf-about">
    <div class="cf-container cf-grid-2">
      <div class="cf-image-card cf-image-business" aria-label="Business networking at Canton Fair"></div>
      <div>
        <h3>About Canton Fair</h3>
        <p>
          Canton Fair is the world's largest wholesale trade fair, held in Guangzhou, China.
          It features over <strong>70,000 booths</strong> with manufacturers from across the globe,
          attracting hundreds of thousands of buyers every year - including many Cambodian entrepreneurs.
        </p>
        <blockquote>
          Canton Fair ជាពិព័រណ៍បោះដុំធំបំផុតក្នុងពិភពលោក ដែលរៀបចំនៅទីក្រុង Guangzhou ប្រទេសចិន។
          មានស្តង់ផលិតផលលើស 70,000 ស្តង់ និងមានអ្នកចូលរួមរាប់សែននាក់ ពិសេសអ្នកទិញពីកម្ពុជាច្រើន។
        </blockquote>
        <div class="cf-mini-stats">
          <div><strong>70,000+</strong><span>Exhibition Booths</span></div>
          <div><strong>500,000+</strong><span>Annual Visitors</span></div>
        </div>
      </div>
    </div>
  </section>

  <section class="cf-section cf-package">
    <div class="cf-container">
      <div class="cf-center">
        <h3>Your $499 Package Includes</h3>
        <p>Everything you need for a successful business trip to Canton Fair 2025</p>
      </div>
      <div class="cf-grid-3">
        <article class="cf-card"><h4>Round-trip flight tickets</h4><small>សំបុត្រយន្តហោះទៅមក</small></article>
        <article class="cf-card"><h4>3 nights / 4 days hotel accommodation</h4><small>ស្នាក់នៅសណ្ឋាគារ 3 យប់ 4 ថ្ងៃ</small></article>
        <article class="cf-card"><h4>Free breakfast at the hotel</h4><small>អាហារពេលព្រឹកឥតគិតថ្លៃ</small></article>
        <article class="cf-card"><h4>Local transportation in Guangzhou</h4><small>ការដឹកជញ្ជូនក្នុងទីក្រុង Guangzhou</small></article>
        <article class="cf-card"><h4>Exclusive souvenirs from KHB Events</h4><small>អំណោយពិសេសពី KHB Events</small></article>
        <article class="cf-card"><h4>Fair registration assistance</h4><small>ជំនួយការចុះឈ្មោះចូលពិព័រណ៍</small></article>
        <article class="cf-card"><h4>Professional tour guide (Khmer + English support)</h4><small>មគ្គុទ្ទេសក៍អាជីព (ជំនួយភាសាខ្មែរ និងអង់គ្លេស)</small></article>
      </div>
      <div class="cf-price-panel">
        <div class="cf-price-main">$499 <span>per person</span></div>
        <div class="cf-price-sub">All-Inclusive Trip Package</div>
        <div class="cf-price-checks">No Hidden Fees • Full Support Included</div>
      </div>
    </div>
  </section>

  <section class="cf-section cf-dates">
    <div class="cf-container">
      <div class="cf-center">
        <h3>Choose Your Trip Date</h3>
        <p class="cf-gold-text">Only 25 Seats Each Trip!</p>
        <p>ចុះឈ្មោះឥឡូវនេះ មុនពេលកន្លែងអស់!</p>
      </div>
      <div class="cf-grid-3">
        <article class="cf-date-card"><h4>Trip 1</h4><p>17 - 20 October 2025</p><span class="cf-badge">Available</span><small>18 seats left</small></article>
        <article class="cf-date-card cf-date-hot"><h4>Trip 2</h4><p>25 - 28 October 2025</p><span class="cf-badge cf-badge-danger">Almost Full</span><small>7 seats left</small></article>
        <article class="cf-date-card"><h4>Trip 3</h4><p>2 - 5 November 2025</p><span class="cf-badge">Available</span><small>23 seats left</small></article>
      </div>
    </div>
  </section>

  <section class="cf-section cf-why">
    <div class="cf-container cf-grid-2">
      <div>
        <h3>Trusted Event Organizer for <span class="cf-primary">Cambodian Entrepreneurs</span></h3>
        <p>
          KHB Events has helped hundreds of Cambodian business owners connect directly with manufacturers in China and other global trade hubs.
          We understand the unique needs of Southeast Asian entrepreneurs and provide comprehensive support every step of the way.
        </p>
        <ul class="cf-check-list">
          <li>Licensed tour operator</li>
          <li>Insurance covered</li>
          <li>Established since 2018</li>
          <li>500+ successful trips</li>
        </ul>
      </div>
      <div class="cf-image-card cf-image-skyline" aria-label="Guangzhou skyline"></div>
    </div>
  </section>

  <section id="booking-form" class="cf-section cf-booking">
    <div class="cf-container">
      <div class="cf-center">
        <h3>Book Your Seat Now</h3>
        <p>ចុះឈ្មោះឥឡូវនេះ សម្រាប់ Canton Fair 2025</p>
      </div>
      <form id="cfBookingForm" class="cf-form">
        <label>Full Name / ឈ្មោះពេញ <input name="name" type="text" required placeholder="Enter your full name"></label>
        <label>Phone Number / លេខទូរស័ព្ទ <input name="phone" type="tel" required placeholder="060 815 515"></label>
        <label>Email Address / អ៊ីមែល <input name="email" type="email" required placeholder="your.email@example.com"></label>
        <label>Preferred Trip Date / កាលបរិច្ឆេទដំណើរ
          <select name="tripDate" required>
            <option value="">Select your preferred trip date</option>
            <option>17 - 20 October 2025</option>
            <option>25 - 28 October 2025</option>
            <option>2 - 5 November 2025</option>
          </select>
        </label>
        <button class="cf-btn cf-btn-primary cf-btn-lg" type="submit">ចុះឈ្មោះឥឡូវនេះ (Book My Seat Now)</button>
      </form>
      <div class="cf-contact-box">
        <p>Need help? Contact us directly:</p>
        <p><a href="tel:060815515">060 815 515</a> • <a href="tel:010947640">010 94 76 40</a></p>
      </div>
    </div>
  </section>

  <section class="cf-section cf-faq">
    <div class="cf-container">
      <div class="cf-center">
        <h3>Frequently Asked Questions</h3>
        <p>Get answers to common questions about your Canton Fair business trip</p>
      </div>

      <div class="cf-accordion" id="cfFaq">
        <article class="cf-faq-item">
          <button type="button" class="cf-faq-q">Do I need a visa for China?</button>
          <div class="cf-faq-a">Yes, Cambodian citizens need a visa to enter China. KHB Events will guide you through the visa process and documentation. <div class="cf-kh">ភាសាខ្មែរ: បាទ/ចាស ប្រជាពលរដ្ឋកម្ពុជាត្រូវការទិដ្ឋាការចូលប្រទេសចិន។ KHB Events នឹងដឹកនាំអ្នកពេញដំណើរការស្នើសុំទិដ្ឋាការ។</div></div>
        </article>
        <article class="cf-faq-item">
          <button type="button" class="cf-faq-q">Who can join this business trip?</button>
          <div class="cf-faq-a">This trip is for business owners, wholesalers, retailers, and entrepreneurs who want direct sourcing access. <div class="cf-kh">ភាសាខ្មែរ: ដំណើរនេះសមរម្យសម្រាប់ម្ចាស់អាជីវកម្ម អ្នកលក់ដុំ អ្នកលក់រាយ ឬអ្នកដែលចង់រកផលិតផលពីរោងចក្រផ្ទាល់។</div></div>
        </article>
        <article class="cf-faq-item">
          <button type="button" class="cf-faq-q">Is translation and language support provided?</button>
          <div class="cf-faq-a">Yes. Professional guides provide Khmer, English, and Mandarin support during meetings and negotiations. <div class="cf-kh">ភាសាខ្មែរ: បាទ/ចាស! មគ្គុទ្ទេសក៍របស់យើងអាចនិយាយភាសាខ្មែរ អង់គ្លេស និងចិន។</div></div>
        </article>
        <article class="cf-faq-item">
          <button type="button" class="cf-faq-q">What if I need to cancel my booking?</button>
          <div class="cf-faq-a">Cancellations made 30 days before departure are eligible for refund minus administration fees. <div class="cf-kh">ភាសាខ្មែរ: ការលុបចោលការកក់មុន 30 ថ្ងៃអាចទទួលបានការបង្វិលប្រាក់វិញពេញលេញ។</div></div>
        </article>
        <article class="cf-faq-item">
          <button type="button" class="cf-faq-q">Are meals included in the package?</button>
          <div class="cf-faq-a">The package includes daily hotel breakfast. Lunch and dinner are flexible for your business schedule. <div class="cf-kh">ភាសាខ្មែរ: កញ្ចប់រួមបញ្ចូលអាហារពេលព្រឹករៀងរាល់ថ្ងៃនៅសណ្ឋាគារ។</div></div>
        </article>
      </div>
    </div>
  </section>

  <button type="button" id="cfFloatingBook" class="cf-floating-book">Book Now</button>
</main>
HTML,
                'css_content' => <<<'CSS'
:root{--cf-primary:#e53935;--cf-secondary:#ffd700;--cf-accent:#ffb300;--cf-bg:#ffffff;--cf-muted:#f4f5f7;--cf-text:#111827;--cf-border:#e5e7eb}
*{box-sizing:border-box}
html,body{margin:0;padding:0}
body{font-family:Inter,Hanuman,"Noto Sans Khmer","Khmer OS Battambang",Arial,sans-serif;background:#fff;color:var(--cf-text);line-height:1.5}
.cf-container{width:min(1120px,92vw);margin:0 auto}
.cf-section{padding:60px 0}
.cf-center{text-align:center}
.cf-primary{color:var(--cf-primary)}
.cf-gold{background:linear-gradient(135deg,var(--cf-secondary),var(--cf-accent));-webkit-background-clip:text;background-clip:text;color:transparent}
.cf-gold-text{color:var(--cf-secondary)}
.cf-btn{border:none;cursor:pointer;border-radius:12px;min-height:44px;padding:11px 18px;font-weight:700;transition:.2s}
.cf-btn-lg{font-size:18px;padding:14px 24px}
.cf-btn-primary{background:linear-gradient(135deg,var(--cf-primary),#c62828);color:#fff;box-shadow:0 8px 24px rgba(229,57,53,.35)}
.cf-btn-primary:hover{transform:translateY(-2px)}
.cf-btn-outline{background:#fff;color:#111;border:1px solid #ddd}
.cf-hero{position:relative;min-height:100vh;display:flex;align-items:center;justify-content:center;overflow:hidden;background:url('https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=1600&auto=format&fit=crop') center/cover no-repeat}
.cf-hero-overlay{position:absolute;inset:0;background:rgba(0,0,0,.52)}
.cf-hero-content{position:relative;z-index:2;text-align:center;color:#fff;padding:84px 0}
.cf-logo{height:96px;width:auto;max-width:min(90vw,320px)}
.cf-logo-wrap{margin:0 0 18px}
.cf-hero h1{font-size:clamp(30px,5vw,62px);line-height:1.1;margin:0 0 14px;font-weight:800;text-shadow:2px 2px 6px rgba(0,0,0,.45)}
.cf-hero h2{font-size:clamp(16px,2.8vw,28px);font-weight:600;margin:0 0 22px;text-shadow:2px 2px 6px rgba(0,0,0,.45)}
.cf-hero-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;max-width:920px;margin:0 auto 22px}
.cf-hero-stats article{background:rgba(255,255,255,.12);backdrop-filter:blur(4px);border-radius:12px;padding:14px}
.cf-hero-stats strong{display:block;font-size:30px}
.cf-hero-stats span{font-size:13px}
.cf-hero-desc{font-size:clamp(14px,2.2vw,22px);max-width:860px;margin:0 auto 22px}
.cf-urgency{margin:15px 0 0;color:var(--cf-secondary);font-weight:800}
.cf-about{background:linear-gradient(135deg,#fff,#f7f8fa)}
.cf-grid-2{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:28px;align-items:center}
.cf-image-card{min-height:360px;border-radius:16px;box-shadow:0 20px 40px rgba(0,0,0,.15);background-size:cover;background-position:center}
.cf-image-business{background-image:url('https://images.unsplash.com/photo-1552664730-d307ca884978?q=80&w=1400&auto=format&fit=crop')}
.cf-image-skyline{background-image:url('https://images.unsplash.com/photo-1549692520-acc6669e2f0c?q=80&w=1400&auto=format&fit=crop')}
.cf-about h3,.cf-package h3,.cf-dates h3,.cf-why h3,.cf-booking h3,.cf-faq h3{font-size:clamp(28px,4vw,44px);margin:0 0 14px}
.cf-about p{font-size:18px;color:#4b5563}
.cf-about blockquote{margin:18px 0;background:#fff6cf;border-left:4px solid var(--cf-secondary);padding:14px;border-radius:10px}
.cf-mini-stats{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.cf-mini-stats div{background:#fff;border:1px solid var(--cf-border);border-radius:12px;padding:14px;text-align:center}
.cf-mini-stats strong{display:block;font-size:28px;color:var(--cf-primary)}
.cf-mini-stats span{font-size:13px;color:#6b7280}
.cf-package{background:linear-gradient(180deg,#fff,#fff7ed)}
.cf-grid-3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}
.cf-card{background:#fff;border:1px solid var(--cf-border);border-radius:14px;padding:16px;box-shadow:0 4px 14px rgba(0,0,0,.05)}
.cf-card h4{margin:0 0 8px;font-size:17px}
.cf-card small{color:#6b7280}
.cf-price-panel{margin-top:24px;background:linear-gradient(135deg,var(--cf-primary),#8e0f0b);color:#fff;padding:28px;border-radius:16px;text-align:center}
.cf-price-main{font-size:clamp(44px,8vw,90px);font-weight:900;line-height:1}
.cf-price-main span{font-size:24px;font-weight:600}
.cf-price-sub{font-size:24px;margin-top:8px}
.cf-price-checks{margin-top:10px;color:#fff6cf}
.cf-dates{background:#f9fafb}
.cf-date-card{position:relative;background:#fff;border:1px solid var(--cf-border);border-radius:14px;padding:18px;text-align:center}
.cf-date-card h4{margin:0 0 8px}
.cf-date-card p{margin:0 0 8px;font-weight:700}
.cf-date-card small{display:block;margin-top:8px;color:#6b7280}
.cf-badge{display:inline-block;background:#ffeeba;border-radius:999px;padding:5px 10px;font-weight:700;font-size:12px}
.cf-date-hot{border:2px solid var(--cf-primary)}
.cf-badge-danger{background:#ffd1d1;color:#a51d1d}
.cf-why{background:linear-gradient(135deg,#fff,#f8fafc)}
.cf-check-list{list-style:none;padding:0;margin:14px 0 0}
.cf-check-list li{position:relative;padding-left:24px;margin:8px 0}
.cf-check-list li::before{content:"✓";position:absolute;left:0;top:0;color:#16a34a;font-weight:800}
.cf-booking{background:linear-gradient(135deg,var(--cf-primary),#cc2e2e);color:#fff}
.cf-booking p{color:#ffecec}
.cf-form{max-width:760px;margin:0 auto;display:grid;gap:12px;background:#fff;border-radius:16px;padding:18px;color:#111}
.cf-form label{display:grid;gap:6px;font-weight:600}
.cf-form input,.cf-form select{min-height:46px;border-radius:10px;border:1px solid #d1d5db;padding:10px 12px;font-size:16px}
.cf-contact-box{text-align:center;margin-top:18px}
.cf-contact-box a{color:#fff;font-weight:800}
.cf-faq{background:#f9fafb}
.cf-accordion{max-width:900px;margin:0 auto;background:#fff;border:1px solid var(--cf-border);border-radius:16px;overflow:hidden}
.cf-faq-item{border-bottom:1px solid var(--cf-border)}
.cf-faq-item:last-child{border-bottom:0}
.cf-faq-q{width:100%;text-align:left;background:#fff;border:none;padding:16px 18px;font-size:19px;font-weight:700;cursor:pointer}
.cf-faq-a{display:none;padding:0 18px 16px;color:#4b5563}
.cf-faq-item.open .cf-faq-a{display:block}
.cf-kh{margin-top:10px;background:#fff6cf;border-left:3px solid var(--cf-secondary);padding:10px;border-radius:8px}
.cf-floating-book{position:fixed;right:14px;bottom:14px;z-index:1000;display:none;background:linear-gradient(135deg,var(--cf-primary),#c62828);color:#fff;border:none;border-radius:999px;padding:12px 18px;min-height:44px;font-weight:800;box-shadow:0 10px 24px rgba(229,57,53,.45)}
@media (max-width: 991.98px){
  .cf-grid-2,.cf-grid-3,.cf-hero-stats{grid-template-columns:1fr}
  .cf-image-card{min-height:240px}
}
CSS,
                'js_content' => <<<'JS'
function cfScrollToBooking(source) {
  if (typeof trackLandingEvent === 'function') {
    trackLandingEvent('cta_click', { cta_label: 'ScrollToBooking', source: source || 'unknown' });
  }
  var el = document.getElementById('booking-form');
  if (el) el.scrollIntoView({ behavior: 'smooth' });
}

document.addEventListener('DOMContentLoaded', function() {
  var floatingBtn = document.getElementById('cfFloatingBook');
  if (floatingBtn) {
    floatingBtn.addEventListener('click', function() { cfScrollToBooking('floating-cta'); });
    var onScroll = function() {
      if (window.innerWidth <= 768 && window.pageYOffset > 300) {
        floatingBtn.style.display = 'inline-flex';
      } else {
        floatingBtn.style.display = 'none';
      }
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  var form = document.getElementById('cfBookingForm');
  if (form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      var name = form.querySelector('[name="name"]').value;
      var phone = form.querySelector('[name="phone"]').value;
      var email = form.querySelector('[name="email"]').value;
      var tripDate = form.querySelector('[name="tripDate"]').value;

      if (typeof submitLandingLead === 'function') {
        submitLandingLead({
          lead_name: name,
          lead_phone: phone,
          lead_email: email,
          source: 'booking-form',
          meta: { tripDate: tripDate }
        });
      }
      if (typeof trackLandingEvent === 'function') {
        trackLandingEvent('lead_submit', { cta_label: 'BookMySeatNow', source: 'booking-form' });
      }
      alert('Booking Request Submitted! We will contact you within 24 hours to confirm your reservation.');
      form.reset();
    });
  }

  var faqButtons = document.querySelectorAll('.cf-faq-q');
  faqButtons.forEach(function(btn) {
    btn.addEventListener('click', function() {
      var item = btn.closest('.cf-faq-item');
      if (!item) return;
      item.classList.toggle('open');
    });
  });
});
JS,
                'redirect_url' => '/login',
                'show_once_mode' => 'cookie_once',
                'allow_inline_scripts' => true,
                'priority' => 1,
                'is_published' => true,
                'is_active' => true,
                'published_at' => now(),
            ]
        );
    }
}
