<?php

namespace Database\Seeders;

use App\Models\LandingPage;
use Illuminate\Database\Seeder;

class LandingPageDemoSeeder extends Seeder
{
    public function run(): void
    {
        LandingPage::query()->where('slug', '!=', 'demo-events-booths-trips')->update(['is_active' => false]);

        LandingPage::updateOrCreate(
            ['slug' => 'demo-events-booths-trips'],
            [
                'name' => 'Demo Funnel Landing (Events/Booths/Trips)',
                'industry' => 'Events',
                'headline' => 'Launch Faster. Fill Booths. Sell Trips.',
                'html_content' => <<<'HTML'
<main style="font-family:Arial,sans-serif;max-width:980px;margin:0 auto;padding:24px;line-height:1.5">
  <section style="padding:32px;border:1px solid #e5e7eb;border-radius:16px;background:#fff">
    <h1 style="font-size:36px;margin:0 0 10px">Scale Your Events, Booths, and Trips</h1>
    <p style="color:#4b5563;margin:0 0 18px">
      Use one landing workflow for any industry. Capture leads, track CTA clicks, and launch offers in minutes.
    </p>
    <div style="display:flex;gap:12px;flex-wrap:wrap">
      <button
        onclick="trackLandingEvent('cta_click',{cta_label:'Primary CTA',source:'hero'});landingContinue()"
        style="min-height:44px;padding:12px 20px;border:0;border-radius:10px;background:#2563eb;color:#fff;font-weight:700;cursor:pointer">
        Get Started
      </button>
      <button
        onclick="trackLandingEvent('cta_click',{cta_label:'Secondary CTA',source:'hero'})"
        style="min-height:44px;padding:12px 20px;border:1px solid #d1d5db;border-radius:10px;background:#fff;color:#111827;font-weight:700;cursor:pointer">
        View Demo
      </button>
    </div>
  </section>

  <section style="margin-top:18px;padding:24px;border:1px solid #e5e7eb;border-radius:16px;background:#f9fafb">
    <h2 style="font-size:24px;margin:0 0 10px">Quick Lead Form</h2>
    <form onsubmit="event.preventDefault();submitLandingLead({lead_name:this.name.value,lead_email:this.email.value,lead_phone:this.phone.value,source:'demo-form'}).then(()=>landingContinue())">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
        <input name="name" placeholder="Your name" required style="min-height:44px;padding:10px;border:1px solid #d1d5db;border-radius:8px">
        <input name="email" type="email" placeholder="Your email" required style="min-height:44px;padding:10px;border:1px solid #d1d5db;border-radius:8px">
        <input name="phone" placeholder="Phone" style="min-height:44px;padding:10px;border:1px solid #d1d5db;border-radius:8px">
      </div>
      <div style="margin-top:12px">
        <button type="submit" style="min-height:44px;padding:12px 18px;border:0;border-radius:10px;background:#059669;color:#fff;font-weight:700;cursor:pointer">
          Submit & Continue
        </button>
      </div>
    </form>
  </section>
</main>
HTML,
                'css_content' => 'body{background:#f3f4f6;margin:0;padding:20px;} @media (max-width:768px){main{padding:12px !important;} h1{font-size:28px !important;}}',
                'js_content' => 'console.log("Demo landing loaded");',
                'redirect_url' => '/login',
                'show_once_mode' => 'cookie_once',
                'allow_inline_scripts' => true,
                'priority' => 10,
                'is_published' => true,
                'is_active' => true,
                'published_at' => now(),
            ]
        );
    }
}
