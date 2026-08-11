#!/usr/bin/env python3
import os

base_dir = os.path.dirname(__file__)

def get_template(title, ep_num, badge_text, lead_cast, subtitle, kw, panel1, panel2, panel3, panel4, body_html, checklist_items, faq_items):
    return f"""<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="{title}. Episode {ep_num:02d} M2B Logistics Stories — {lead_cast} ({kw}).">
  <meta name="keywords" content="{title}, {kw}, M2B Logistics Stories, komik logistik, edukasi impor ekspor indonesia, PPJK M2B">
  <meta name="author" content="M2B - Logistic | Solution | Partner">
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">
  <meta name="theme-color" content="#0b1d40">

  <title>{title} | M2B Logistics Stories</title>

  <link rel="canonical" href="https://ebook.m2b.co.id/episode-{ep_num:02d}.html">
  <link rel="icon" type="image/x-icon" href="https://m2b.co.id/favicon.ico">
  <link rel="apple-touch-icon" href="https://m2b.co.id/favicon.ico">

  <meta property="og:type" content="article">
  <meta property="og:title" content="{title} | M2B">
  <meta property="og:description" content="{subtitle}">
  <meta property="og:url" content="https://ebook.m2b.co.id/episode-{ep_num:02d}.html">
  <meta property="og:image" content="https://ebook.m2b.co.id/{panel1['img']}">
  <meta property="og:locale" content="id_ID">
  <meta property="og:site_name" content="M2B Logistics Stories">
  <meta property="article:published_time" content="2026-08-11T00:00:00+07:00">
  <meta property="article:author" content="Tim PPJK M2B">

  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="{title}">
  <meta name="twitter:description" content="{subtitle}">
  <meta name="twitter:image" content="https://ebook.m2b.co.id/{panel1['img']}">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900;1000&family=Georgia&display=swap" rel="stylesheet">

  <script type="application/ld+json">
  {{
    "@context": "https://schema.org",
    "@type": "TechArticle",
    "headline": "{title}",
    "description": "{subtitle}",
    "image": ["https://ebook.m2b.co.id/{panel1['img']}"],
    "datePublished": "2026-08-11T00:00:00+07:00",
    "dateModified": "2026-08-11T00:00:00+07:00",
    "author": {{
      "@type": "Organization",
      "name": "M2B - Logistic | Solution | Partner",
      "url": "https://m2b.co.id"
    }},
    "publisher": {{
      "@type": "Organization",
      "name": "PT Mora Multi Berkah",
      "logo": {{
        "@type": "ImageObject",
        "url": "https://m2b.co.id/favicon.ico"
      }}
    }},
    "mainEntityOfPage": {{
      "@type": "WebPage",
      "@id": "https://ebook.m2b.co.id/episode-{ep_num:02d}.html"
    }},
    "inLanguage": "id"
  }}
  </script>

  <style>
    :root {{
      --navy: #0b1d40;
      --navy-dark: #07152f;
      --gold: #d4a017;
      --gold-hover: #a97c08;
      --red: #7d0806;
      --ink: #172033;
      --muted: #64748b;
      --line: #d8dee9;
      --paper: #ffffff;
      --mist: #f8fafc;
      --green: #18794e;
      --radius: 14px;
      --shadow: 0 14px 35px rgba(11,29,64,.08);
    }}
    * {{ box-sizing: border-box; }}
    html {{ scroll-behavior: smooth; }}
    body {{ margin: 0; background: var(--paper); color: var(--ink); font-family: Inter, ui-sans-serif, system-ui, sans-serif; line-height: 1.7; }}
    a {{ color: inherit; text-decoration: none; }}
    img {{ display: block; max-width: 100%; height: auto; }}

    .shell {{ width: min(1180px, calc(100% - 40px)); margin: auto; }}

    .top-navy-bar {{ background: var(--navy); color: #fff; padding: 14px 0; text-align: center; border-bottom: 3px solid var(--gold); }}
    .top-navy-bar .shell {{ display: flex; align-items: center; justify-content: center; gap: 8px; font-weight: 900; letter-spacing: .06em; font-size: 15px; text-transform: uppercase; }}
    .top-navy-bar span.gold {{ color: #ffd86f; font-weight: 800; }}

    .site-header {{ position: sticky; top: 0; z-index: 30; background: rgba(255,255,255,.96); backdrop-filter: blur(14px); border-bottom: 1px solid var(--line); }}
    .nav {{ min-height: 64px; display: flex; align-items: center; gap: 24px; }}
    .brand {{ display: flex; align-items: center; gap: 10px; margin-right: auto; }}
    .brand-mark {{ width: 40px; height: 40px; display: grid; place-items: center; background: var(--navy); color: #fff; border-radius: 10px; font-weight: 1000; font-size: 16px; box-shadow: inset 0 -4px 0 var(--gold); }}
    .brand-copy b {{ display: block; color: var(--navy); font-size: 14px; letter-spacing: .08em; text-transform: uppercase; }}
    .brand-copy span {{ display: block; color: var(--muted); font-size: 10px; }}

    .nav-links {{ display: flex; align-items: center; gap: 20px; font-weight: 750; font-size: 14px; }}
    .nav-links a:hover {{ color: var(--red); }}
    .nav-cta, .button {{ border: 0; border-radius: 99px; background: var(--gold); color: var(--navy); font-weight: 900; padding: 10px 20px; cursor: pointer; box-shadow: 0 4px 0 var(--gold-hover); transition: .18s transform, .18s box-shadow; display: inline-flex; align-items: center; justify-content: center; gap: 6px; }}
    .nav-cta:hover, .button:hover {{ transform: translateY(2px); box-shadow: 0 2px 0 var(--gold-hover); }}

    .breadcrumb {{ padding: 22px 0 10px; font-size: 13px; color: var(--muted); display: flex; gap: 8px; flex-wrap: wrap; }}
    .breadcrumb a:hover {{ color: var(--navy); text-decoration: underline; }}
    .breadcrumb span {{ color: #cbd5e1; }}

    .article-header {{ padding: 15px 0 35px; border-bottom: 1px solid var(--line); }}
    .badge-ep {{ display: inline-block; color: var(--red); font-size: 12px; font-weight: 1000; letter-spacing: .12em; text-transform: uppercase; margin-bottom: 12px; }}
    .article-title {{ font-family: Inter, ui-sans-serif, sans-serif; font-size: clamp(32px, 4vw, 50px); color: var(--navy); line-height: 1.1; margin: 0 0 18px; font-weight: 950; letter-spacing: -.03em; }}
    .article-lead {{ font-size: 18px; color: #334155; line-height: 1.65; max-width: 900px; margin: 0 0 20px; font-weight: 500; }}
    .meta-bar {{ font-size: 13px; color: var(--muted); display: flex; gap: 16px; flex-wrap: wrap; align-items: center; font-weight: 600; }}

    .comic-grid-section {{ margin: 38px 0 45px; }}
    .comic-columns {{ display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; border: 2px solid var(--navy); border-radius: 16px; padding: 12px; background: #f1f5f9; box-shadow: 0 18px 45px rgba(11,29,64,.12); }}
    
    .comic-col-card {{ background: #fff; border: 1px solid var(--line); border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 4px 12px rgba(0,0,0,.04); }}
    .comic-col-header {{ background: var(--navy); color: #fff; text-align: center; padding: 10px 8px; font-size: 12px; font-weight: 950; letter-spacing: .08em; text-transform: uppercase; border-bottom: 3px solid var(--gold); }}
    .comic-col-img {{ position: relative; aspect-ratio: 3/4; overflow: hidden; background: #f8fafc; }}
    .comic-col-img img {{ width: 100%; height: 100%; object-fit: cover; object-position: top; }}

    .ad-slot {{ display: none !important; margin: 45px 0; padding: 18px; min-height: 100px; border: 1px dashed #cbd5e1; background: #f8fafc; border-radius: 12px; place-items: center; text-align: center; color: #64748b; font-size: 12px; letter-spacing: .05em; text-transform: uppercase; }}

    .content-body {{ max-width: 860px; margin: 0 auto; font-size: 17px; color: #1e293b; line-height: 1.85; }}
    .content-body h2 {{ font-family: Georgia, serif; font-size: 28px; color: var(--navy); margin: 45px 0 18px; line-height: 1.25; border-bottom: 2px solid var(--gold); padding-bottom: 8px; }}
    .content-body h3 {{ font-size: 21px; color: var(--navy-dark); margin: 30px 0 12px; line-height: 1.3; font-weight: 800; }}
    .content-body p {{ margin: 0 0 22px; }}
    .content-body ul, .content-body ol {{ margin: 0 0 24px; padding-left: 24px; }}
    .content-body li {{ margin-bottom: 10px; }}
    .content-body blockquote {{ margin: 30px 0; padding: 20px 24px; background: var(--mist); border-left: 5px solid var(--red); border-radius: 0 12px 12px 0; font-style: italic; color: #334155; }}
    .content-body blockquote p:last-child {{ margin: 0; }}

    .highlight-box {{ background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 14px; padding: 24px; margin: 35px 0; }}
    .highlight-box h4 {{ margin: 0 0 10px; color: #0369a1; font-size: 18px; font-weight: 800; }}

    .checklist-box {{ background: #f8fafc; border: 2px solid var(--navy); border-radius: var(--radius); padding: 30px; margin: 45px 0; }}
    .checklist-box h3 {{ margin: 0 0 16px; color: var(--navy); font-family: Georgia, serif; font-size: 24px; }}
    .checklist-items {{ list-style: none; padding: 0; margin: 0; }}
    .checklist-items li {{ display: flex; align-items: flex-start; gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--line); font-size: 15px; }}
    .checklist-items li:last-child {{ border-bottom: 0; }}
    .checklist-items input[type="checkbox"] {{ width: 20px; height: 20px; accent-color: var(--green); cursor: pointer; margin-top: 3px; }}

    .faq-section {{ margin: 50px 0; }}
    .faq-section h2 {{ font-family: Georgia, serif; font-size: 28px; color: var(--navy); margin-bottom: 24px; }}
    .faq-item {{ border: 1px solid var(--line); border-radius: 12px; margin-bottom: 12px; overflow: hidden; background: #fff; }}
    .faq-question {{ width: 100%; text-align: left; padding: 18px 20px; background: #fff; border: 0; font-weight: 800; font-size: 16px; color: var(--navy); cursor: pointer; display: flex; justify-content: space-between; align-items: center; }}
    .faq-answer {{ padding: 0 20px 20px; color: var(--muted); font-size: 15px; display: none; line-height: 1.7; }}
    .faq-item.open .faq-answer {{ display: block; }}
    .faq-item.open .faq-question {{ color: var(--red); }}

    .legal-ref {{ background: #fffbeb; border: 1px solid #fef3c7; border-left: 5px solid var(--gold); padding: 20px 24px; border-radius: 8px; margin: 35px 0; font-size: 14px; }}
    .legal-ref b {{ color: #92400e; display: block; margin-bottom: 6px; font-size: 15px; }}
    .legal-ref a {{ color: #b45309; text-decoration: underline; font-weight: 700; }}

    .cta-banner {{ background: linear-gradient(135deg, var(--navy), var(--navy-dark)); color: #fff; padding: 45px 35px; border-radius: var(--radius); text-align: center; margin: 55px 0; position: relative; overflow: hidden; box-shadow: var(--shadow); }}
    .cta-banner h2 {{ font-family: Georgia, serif; font-size: 32px; margin: 0 0 14px; color: #fff; }}
    .cta-banner p {{ max-width: 620px; margin: 0 auto 28px; color: #cbd5e7; font-size: 16px; }}
    .cta-buttons {{ display: flex; justify-content: center; gap: 16px; flex-wrap: wrap; }}

    .footer {{ background: #07152f; color: #cbd5e7; padding: 45px 0 30px; margin-top: 60px; font-size: 13px; }}
    .footer-grid {{ display: grid; grid-template-columns: 1.4fr .6fr .6fr .6fr; gap: 30px; }}
    .footer b {{ color: #fff; display: block; margin-bottom: 12px; font-size: 15px; }}
    .footer a {{ display: block; color: #cbd5e7; line-height: 1.8; }}
    .footer a:hover {{ color: #fff; text-decoration: underline; }}
    .copyright {{ border-top: 1px solid rgba(255,255,255,.12); margin-top: 32px; padding-top: 22px; color: #8796b0; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 10px; font-size: 12px; }}

    .scroll-top-btn {{ position: fixed; bottom: 28px; right: 28px; width: 48px; height: 48px; border-radius: 50%; background: var(--navy); color: var(--gold); border: 2px solid var(--gold); display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 8px 24px rgba(11,29,64,.28); opacity: 0; visibility: hidden; transform: translateY(20px); transition: all 0.3s cubic-bezier(0.4,0,0.2,1); z-index: 99; }}
    .scroll-top-btn.visible {{ opacity: 1; visibility: visible; transform: translateY(0); }}
    .scroll-top-btn:hover {{ background: var(--gold); color: var(--navy); border-color: var(--navy); transform: translateY(-4px); box-shadow: 0 12px 28px rgba(212,160,23,.35); }}

    @media (max-width: 900px) {{
      .comic-columns {{ grid-template-columns: repeat(2, 1fr); }}
      .nav-links {{ display: none; }}
      .article-title {{ font-size: 34px; }}
      .article-lead {{ font-size: 16px; }}
    }}
    @media (max-width: 580px) {{
      .comic-columns {{ grid-template-columns: 1fr; }}
      .footer-grid {{ grid-template-columns: 1fr; }}
    }}
  </style>
</head>
<body>
  <div class="top-navy-bar">
    <div class="shell">
      <span>M2B LOGISTICS STORIES</span>
      <span class="gold">- FROM DOCUMENTS TO DELIVERY</span>
    </div>
  </div>

  <header class="site-header">
    <nav class="nav shell" aria-label="Navigasi utama">
      <a class="brand" href="index.html" aria-label="M2B Logistics Stories">
        <span class="brand-mark">M2B</span>
        <span class="brand-copy">
          <b>Logistics Stories</b>
          <span>From Documents to Delivery</span>
        </span>
      </a>
      <div class="nav-links">
        <a href="index.html">📚 E-book</a>
        <a href="toolkit.html">🧰 Toolkit</a>
        <a href="stories.html">📖 Logistics Stories</a>
        <a href="https://m2b.co.id" target="_blank" rel="noopener noreferrer">Konsultasi Forwarder</a>
      </div>
      <a class="nav-cta" href="index.html#order">Pesan E-book Rp 49rb</a>
    </nav>
  </header>

  <main class="shell">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="index.html">Beranda</a>
      <span>/</span>
      <a href="stories.html">Insight Ekspor-Impor</a>
      <span>/</span>
      <a href="stories.html">Comic</a>
      <span>/</span>
      <strong style="color:var(--navy);">{badge_text}</strong>
    </nav>

    <header class="article-header">
      <span class="badge-ep">EPISODE {ep_num:02d} - {badge_text.upper()}</span>
      <h1 class="article-title">{title}</h1>
      <p class="article-lead">{subtitle}</p>
      <div class="meta-bar">
        <span>Waktu baca 6 menit</span>
        <span>|</span>
        <span>Lead: {lead_cast}</span>
        <span>|</span>
        <span>Ditinjau: 11 Agustus 2026</span>
      </div>
    </header>

    <section class="comic-grid-section" aria-label="Komik Strip 4 Panel Episode {ep_num:02d}">
      <div class="comic-columns">
        <article class="comic-col-card">
          <div class="comic-col-header">{panel1['title']}</div>
          <div class="comic-col-img">
            <img src="{panel1['img']}" alt="{panel1['title']}" width="400" height="533">
          </div>
        </article>
        <article class="comic-col-card">
          <div class="comic-col-header">{panel2['title']}</div>
          <div class="comic-col-img">
            <img src="{panel2['img']}" alt="{panel2['title']}" width="400" height="533">
          </div>
        </article>
        <article class="comic-col-card">
          <div class="comic-col-header">{panel3['title']}</div>
          <div class="comic-col-img">
            <img src="{panel3['img']}" alt="{panel3['title']}" width="400" height="533">
          </div>
        </article>
        <article class="comic-col-card">
          <div class="comic-col-header">{panel4['title']}</div>
          <div class="comic-col-img">
            <img src="{panel4['img']}" alt="{panel4['title']}" width="400" height="533">
          </div>
        </article>
      </div>
    </section>

    <aside class="ad-slot" aria-label="Area Iklan Responsive">
      <div><b>Advertisement Slot #1 (Display Ad)</b></div>
    </aside>

    <article class="content-body">
      {body_html}

      <section class="checklist-box">
        <h3>📋 Checklist Kesiapan Sebelum Pengiriman</h3>
        <ul class="checklist-items">
          {''.join([f'<li><input type="checkbox" checked><label>{item}</label></li>' for item in checklist_items])}
        </ul>
      </section>

      <div class="legal-ref">
        <b>📚 Sumber &amp; Regulasi Acuan Resmi:</b>
        <ul>
          <li>JDIH Kemenkeu — <a href="https://jdih.kemenkeu.go.id/dok/190-pmk-04-2022/view" target="_blank" rel="noopener noreferrer">PMK No. 190/PMK.04/2022 tentang Pengeluaran Barang Impor</a></li>
          <li>Kementerian Perdagangan &amp; DJBC — Sistem Informasi Kepabeanan dan Cukai (CEISA)</li>
        </ul>
      </div>

      <section class="faq-section">
        <h2>Pertanyaan Sering Diajukan (FAQ)</h2>
        {''.join([f'''
        <div class="faq-item">
          <button class="faq-question">
            <span>{faq["q"]}</span>
            <span>▼</span>
          </button>
          <div class="faq-answer">{faq["a"]}</div>
        </div>
        ''' for faq in faq_items])}
      </section>

      <aside class="ad-slot" aria-label="Area Iklan Responsive kedua">
        <div><b>Advertisement Slot #2 (Display Ad)</b></div>
      </aside>

      <section class="cta-banner">
        <h2>Kuasai Seluruh Prosedur Logistik &amp; Pabean Bersama M2B</h2>
        <p>Pelajari panduan lengkap 22 bab di E-book M2B atau konsultasikan kebutuhan pengiriman ekspor-impor Anda bersama tim konsultan ahli kami.</p>
        <div class="cta-buttons">
          <a class="button" href="index.html#order" style="font-size:16px; padding:14px 24px;">Pesan E-book Rp 49.000</a>
          <a class="button secondary" href="https://m2b.co.id" target="_blank" rel="noopener noreferrer" style="font-size:16px; padding:14px 24px; color:#fff; border-color:#fff; background:transparent;">Konsultasi Impor M2B →</a>
        </div>
      </section>
    </article>
  </main>

  <footer class="footer">
    <div class="shell">
      <div class="footer-grid">
        <div>
          <b style="color:#fff;">M2B Logistics Stories</b>
          <p>Insight &amp; studi kasus operasional ekspor-impor dari PT Mora Multi Berkah. Materi disusun untuk tujuan edukasi berdasarkan pengalaman lapangan.</p>
        </div>
        <div>
          <b>Navigasi</b>
          <a href="index.html">E-book Utama</a>
          <a href="toolkit.html">Toolkit Gratis</a>
          <a href="stories.html">Semua Cerita</a>
        </div>
        <div>
          <b>Episode</b>
          <a href="episode-01.html">Episode 01</a>
          <a href="episode-{ep_num:02d}.html" style="color:#ffd86f;">Episode {ep_num:02d} (Aktif)</a>
        </div>
        <div>
          <b>Kontak M2B</b>
          <a href="https://m2b.co.id" target="_blank" rel="noopener noreferrer">Website m2b.co.id</a>
          <a href="https://wa.me/6281263027818" target="_blank" rel="noopener noreferrer">WhatsApp 0812-6302-7818</a>
          <a href="mailto:ebook@m2b.co.id">Email ebook@m2b.co.id</a>
        </div>
      </div>
      <div class="copyright">
        <span>© 2026 PT Mora Multi Berkah · All Rights Reserved</span>
        <span>Disusun sesuai regulasi PMK 190/PMK.04/2022 &amp; JDIH Kemenkeu</span>
      </div>
    </div>
  </footer>

  <button id="scrollTopBtn" class="scroll-top-btn" aria-label="Kembali ke atas">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <path d="M18 15l-6-6-6 6"/>
    </svg>
  </button>

  <script>
    document.querySelectorAll('.faq-question').forEach(btn => {{
      btn.addEventListener('click', () => {{
        const item = btn.parentElement;
        item.classList.toggle('open');
      }});
    }});

    const scrollTopBtn = document.getElementById('scrollTopBtn');
    if (scrollTopBtn) {{
      window.addEventListener('scroll', () => {{
        if (window.scrollY > 350) {{
          scrollTopBtn.classList.add('visible');
        }} else {{
          scrollTopBtn.classList.remove('visible');
        }}
      }});
      scrollTopBtn.addEventListener('click', () => {{
        window.scrollTo({{ top: 0, behavior: 'smooth' }});
      }});
    }}
  </script>
</body>
</html>
"""

episodes_data = [
    {
        "ep_num": 2,
        "title": "Truk Sudah Tiba, tapi Dokumen Belum Siap",
        "badge_text": "Field Operations",
        "lead_cast": "Budi & Tasya",
        "subtitle": "Waktu tunggu truk di depo (waiting time) mulai berjalan sementara dokumen release kepabeanan belum siap. Budi dan Tasya mengatur ulang urutan muat agar tidak memicu denda berlebih.",
        "kw": "trucking document readiness indonesia",
        "panel1": {"title": "1. TRUK TIBA DI DEPO", "img": "assets/ep2-panel-1.png"},
        "panel2": {"title": "2. VERIFIKASI STASIUN TASYA", "img": "assets/ep2-panel-2.png"},
        "panel3": {"title": "3. KOORDINASI VENDOR TRUK", "img": "assets/ep2-panel-3.png"},
        "panel4": {"title": "4. SHIPMENT DISPATCH", "img": "assets/ep2-panel-4.png"},
        "body": """
        <h2>Bab 1: Konflik Lapangan — Armada Truk Menunggu Dokumen</h2>
        <p>Di gerbang depo pelabuhan Tanjung Priok, suara mesin truk trailer 40ft meraung pelan. Budi, petugas operasional lapangan M2B, memeriksa lembar jalan pengemudi truk. Di saat bersamaan, sistem pemantauan pengeluaran barang menunjukkan bahwa dokumen rilis pabean (SPPB) belum secara otomatis tersinkronisasi ke sistem gate terminal.</p>
        <p><em>"Pak Budi, armada truk container 40ft sudah sampai depo, tapi dokumen pengeluaran belum rilis di gate terminal!"</em> lapor Budi via walkie-talkie ke stasiun koordinasi operasional.</p>
        <p>Tasya, koordinator operasional M2B di kantor, langsung membuka dasbor pemantauan CEISA dan sistem gate pelabuhan. Ia melihat adanya antrean sinkronisasi data EDI yang tertahan beberapa menit.</p>

        <h2>Bab 2: Risiko Denda Demurrage & Waiting Time</h2>
        <p>Jika armada truk dibiarkan mengetam di gerbang depo tanpa kepastian dokumen rilis, dua kerugian finansial langsung mengintai importir:</p>
        <ul>
          <li><strong>Denda Demurrage & Storage Depo:</strong> Setiap jam kontainer mengendap di lokasi penumpukan melampaui batas free time akan dikenakan tarif progresif.</li>
          <li><strong>Detention & Truck Waiting Time Charge:</strong> Vendor angkutan darat (trucking) akan mengenakan charge tambahan per jam jika armada mereka tertahan lebih dari 2 jam di depo tanpa kepastian pemuatan.</li>
        </ul>

        <h2>Bab 3: Langkah Keputusan & Mitigasi Tim M2B</h2>
        <p>Tasya tidak tinggal diam. Ia segera melakukan dua langkah taktis:</p>
        <ol>
          <li><strong>Penyesuaian Urutan Muat Armada:</strong> Tasya menghubungi pihak manajemen armada angkutan untuk menggeser slot truk ke urutan berikutnya tanpa membubarkan antrean resmi.</li>
          <li><strong>Manual Sync Request ke Gate Controller:</strong> Budi mendatangi ruang kontrol gate terminal dengan membawa salinan cetak SPPB resmi untuk dipadankan secara manual oleh petugas gate pelabuhan.</li>
        </ol>
        <p>Dalam kurun waktu kurang dari 20 menit, sinkronisasi data berhasil diselesaikan. Kontainer dinaikkan ke atas trailer dan armada berangkat menuju gudang tujuan tanpa terkena denda sepeser pun.</p>
        """,
        "checklist": [
          "Konfirmasi rilis status SPPB pada sistem CEISA sebelum memesan armada truk.",
          "Verifikasi nomor kontainer dan nomor segel (seal number) sesuai dokumen pengeluaran.",
          "Komunikasikan batas waktu free time depo dengan vendor angkutan darat.",
          "Pastikan pengemudi memiliki kontak langsung staf lapangan M2B di lokasi depo."
        ],
        "faq": [
          {"q": "Berapa lama batas toleransi waktu tunggu (waiting time) truk yang wajar di depo pelabuhan?", "a": "Batas toleransi standar industri trucking di Indonesia berkisar antara 2 hingga 3 jam di lokasi pemuatan/bongkaran sebelum pengemudi berhak mengajukan jam lembur (waiting charge)."},
          {"q": "Apa yang harus dilakukan jika data SPPB belum terbaca di pintu gate terminal?", "a": "Staf penanganan lapangan dapat mengajukan pengecekan manual ke loket gate controller pelabuhan dengan membawa dokumen cetak SPPB asli dan bukti bayar sewa pelabuhan."}
        ]
    },
    {
        "ep_num": 3,
        "title": "Dari Mana Asalnya Biaya Demurrage Ini?",
        "badge_text": "Logistics Cost",
        "lead_cast": "Nurul & Yusuf",
        "subtitle": "Yusuf terkejut melihat adanya tagihan tambahan penyimpanan kontainer pada invoice pengiriman. Nurul membedah timeline free time, biaya demurrage, detention, dan penanganan gudang secara transparan.",
        "kw": "perbedaan demurrage detention storage",
        "panel1": {"title": "1. INVOICE MENGEJUTKAN", "img": "assets/ep3-panel-1.png"},
        "panel2": {"title": "2. BEDAH TIMELINE NURUL", "img": "assets/ep3-panel-2.png"},
        "panel3": {"title": "3. KLAIM FREE TIME", "img": "assets/ep3-panel-3.png"},
        "panel4": {"title": "4. INVOICE TRANSPARAN", "img": "assets/ep3-panel-4.png"},
        "body": """
        <h2>Bab 1: Kebingungan Tagihan Tambahan</h2>
        <p>Yusuf memegang lembaran tagihan akhir pengiriman dengan wajah mengernyit. <em>"Bu Nurul, dari mana asal biaya demurrage dan penyimpanan kontainer sebesar Rp 4.500.000 ini? Bukankah kesepakatan awal kita memakai tarif all-in?"</em></p>
        <p>Nurul, pakar keuangan dan komunikasi biaya di M2B, menyambut pertanyaan Yusuf dengan tenang. Ia mengambil berkas rekam jejak kapal (vessel movement record) dan dokumen penumpukan depo untuk membedah linimasa pengiriman secara mendalam.</p>

        <h2>Bab 2: Memahami Perbedaan Demurrage, Detention, dan Storage</h2>
        <p>Banyak pengusaha belum membedakan 3 jenis biaya keterlambatan kontainer ini:</p>
        <ul>
          <li><strong>Demurrage:</strong> Biaya denda pemakaian kontainer milik pelayaran yang mengendap di dalam area pelabuhan melampaui masa bebas (free time).</li>
          <li><strong>Detention:</strong> Biaya denda terlambat mengembalikan kontainer kosong (empty container) ke depo pelayaran setelah keluar dari pintu pelabuhan.</li>
          <li><strong>Storage (Penumpukan):</strong> Biaya sewa lahan penumpukan yang dibayarkan langsung kepada pengelola terminal pelabuhan (TPS/ICTSI/UTP).</li>
        </ul>

        <h2>Bab 3: Solusi Rekonstruksi Biaya & Pencegahan M2B</h2>
        <p>Nurul membuktikan bahwa dari total denda tersebut, 2 hari keterlambatan terjadi akibat penundaan perizinan supplier sebelum barang dikirim. M2B berhasil mengklaim kompensasi free time tambahan dari pelayaran sehingga tagihan dipotong hingga 60%.</p>
        """,
        "checklist": [
          "Cek jumlah hari free time demurrage & detention pada perjanjian pengapalan (b/l & quotation).",
          "Pantau tanggal sandar kapal (ETA) dan hitung tanggal jatuh tempo free time (last free day).",
          "Segera lakukan pengembalian kontainer kosong ke depo resmi tepat waktu.",
          "Minta rincian biaya penumpukan terminal sebelum menyetujui pembayaran akhir."
        ],
        "faq": [
          {"q": "Apakah masa free time demurrage bisa diperpanjang (extended free time)?", "a": "Bisa, permohonan pengajuan ekstensi free time biasanya dapat diajukan kepada pihak pelayaran sebelum kapal berangkat dari pelabuhan asal (pol)."},
          {"q": "Siapa yang bertanggung jawab membayar demurrage jika keterlambatan disebabkan oleh pemeriksaan jalur merah?", "a": "Pemeriksaan jalur merah merupakan kewenangan otoritas pabean. Biaya penumpukan selama proses pemeriksaan fisik menjadi tanggung jawab importir, namun M2B membantu mempercepat jadwal pemeriksaan."}
        ]
    },
    {
        "ep_num": 4,
        "title": "Jalur Hijau Bukan Berarti Bebas Tanpa Syarat",
        "badge_text": "Customs Process",
        "lead_cast": "Nadila & Yusuf",
        "subtitle": "Yusuf mengira respon Jalur Hijau menandakan kontainer bisa langsung diangkut keluar tanpa prosedur pendukung. Nadila menjelaskan verifikasi kelengkapan dokumen pabean wajib sebelum rilis fisik.",
        "kw": "jalur hijau bea cukai pengeluaran barang",
        "panel1": {"title": "1. RESPON JALUR HIJAU", "img": "assets/ep4-panel-1.png"},
        "panel2": {"title": "2. SYARAT KEPABEANAN", "img": "assets/ep4-panel-2.png"},
        "panel3": {"title": "3. CEK DOKUMEN AKHIR", "img": "assets/ep4-panel-3.png"},
        "panel4": {"title": "4. SPPB RESMI RILIS", "img": "assets/ep4-panel-4.png"},
        "body": """
        <h2>Bab 1: Kegembiraan yang Terlalu Dini</h2>
        <p>Ketika layar monitor CEISA menampilkan status respon <strong>SPPB Jalur Hijau</strong>, Yusuf melompat kegirangan. <em>"Nadila, PIB kita dapat Jalur Hijau! Berarti truk bisa langsung masuk dan kontainer kita bawa pulang detik ini juga kan?"</em></p>
        <p>Nadila tersenyum sambil memegang dokumen pabean resmi. <em>"Jalur Hijau adalah berita bagus Pak Yusuf, tetapi pengeluaran fisik barang tetap membutuhkan verifikasi pelunasan kewajiban pabean dan clearance administratif dari pengelola terminal."</em></p>

        <h2>Bab 2: Kepatuhan Pabean pada Jalur Hijau</h2>
        <p>Meskipun kontainer tidak melewati pemeriksaan fisik (Jalur Merah), pengeluaran barang pada Jalur Hijau tetap mensyaratkan:</p>
        <ul>
          <li><strong>Pelunasan Pungutan Impor (BM & PDTT):</strong> Pembayaran Bea Masuk, PPN, dan PPh Pasal 22 melalui NTPN Billing Negara.</li>
          <li><strong>Outward Manifest Matching:</strong> Validasi data kontainer antara SPPB dan manifes kedatangan kapal (BC 1.1).</li>
          <li><strong>Pembayaran Jasa Kepelabuhanan:</strong> Pelunasan sewa dermaga, penumpukan, dan pengangkatan kontainer (lift-on).</li>
        </ul>

        <h2>Bab 3: Eksekusi Rilis Lancar Tanpa Kendala</h2>
        <p>Dengan bimbingan Nadila dan tim penanganan M2B, seluruh pembayaran billing dan verifikasi dokumen diselesaikan dalam waktu kurang dari 45 menit. Kontainer Yusuf keluar pelabuhan dengan aman dan legal.</p>
        """,
        "checklist": [
          "Pastikan bukti bayar NTPN billing pabean terverifikasi di sistem DJBC.",
          "Periksa apakah produk memerlukan dokumen karantina atau izin teknis tambahan.",
          "Verifikasi status blokir atau kewajiban pajak perusahaan di OSS / DJP.",
          "Simpan lembar SPPB asli sebagai arsip bukti pengeluaran barang yang sah."
        ],
        "faq": [
          {"q": "Apakah barang di Jalur Hijau masih bisa diaudit di kemudian hari?", "a": "Ya, Direktorat Jenderal Bea dan Cukai memiliki kewenangan melakukan Audit Kepabeanan hingga 2 tahun setelah tanggal pendaftaran PIB."},
          {"q": "Berapa lama proses SPPB terbit setelah PIB disubmit pada Jalur Hijau?", "a": "Secara normal pada sistem komputer pelayanan CEISA, respon SPPB Jalur Hijau terbit dalam hitungan menit hingga beberapa jam setelah pembayaran billing terkonfirmasi."}
        ]
    },
    {
        "ep_num": 5,
        "title": "Deskripsi Produk Terlalu Umum: Risikonya?",
        "badge_text": "Document Readiness",
        "lead_cast": "Nadila & Yusuf",
        "subtitle": "Commercial invoice hanya mencantumkan uraian kata 'Spare Parts'. Nadila menjelaskan rumus deskripsi produk 5 elemen agar tidak memicu Notul dan klasifikasi ulang HS Code.",
        "kw": "deskripsi barang commercial invoice impor",
        "panel1": {"title": "1. URAIAN BARANG UMUM", "img": "assets/ep5-panel-1.png"},
        "panel2": {"title": "2. FORMULA DESKRIPSI", "img": "assets/ep5-panel-2.png"},
        "panel3": {"title": "3. REVISI INVOICE", "img": "assets/ep5-panel-3.png"},
        "panel4": {"title": "4. DEKLARASI PRESISI", "img": "assets/ep5-panel-4.png"},
        "body": """
        <h2>Bab 1: Bahaya Kata 'Spare Parts' pada Invoice</h2>
        <p>Mata Nadila tertuju pada uraian barang di Commercial Invoice barang impor Yusuf yang hanya tertulis samar: <strong>"SPARE PARTS - 5 BOXES"</strong>.</p>
        <p><em>"Pak Yusuf, uraian 'Spare Parts' ini terlalu umum di mata Pejabat Bea Cukai,"</em> tegur Nadila. <em>"Setiap barang impor wajib dideklarasikan secara spesifik agar kode tarif HS Code dan pembebanan bea masuknya tepat."</em></p>

        <h2>Bab 2: Rumus 5 Elemen Deskripsi Barang Presisi</h2>
        <p>Untuk menghindari penolakan sistem pabean atau penetapan Notul tarif, M2B menerapkan rumus penyusunan uraian barang pada invoice:</p>
        <ol>
          <li><strong>Nama Barang Spesifik:</strong> Contoh: Ball Bearing / Filter Udara / Katup Hidrolik.</li>
          <li><strong>Bahan / Material Utama:</strong> Contoh: Stainless Steel 316 / Karet Sintetis.</li>
          <li><strong>Fungsi Utama & Penggunaan:</strong> Contoh: Komponen Mesin Cetak Plastik Industri.</li>
          <li><strong>Tipe / Spesifikasi Teknis / Tipe Model:</strong> Contoh: Model X-200, Diameter 50mm.</li>
          <li><strong>Kondisi Barang:</strong> Barus / Bekas (wajib izin Lartas jika bekas).</li>
        </ol>

        <h2>Bab 3: Hasil Pengoreksian Dokumen</h2>
        <p>Yusuf meminta supplier memperbarui Commercial Invoice sesuai rumus M2B. Deklarasi PIB disetujui tanpa kendala Klasifikasi Tarif BTKI.</p>
        """,
        "checklist": [
          "Hindari kata umum seperti: Accessories, Spare Parts, Tools, General Goods.",
          "Cantumkan spesifikasi teknis dan material bahan pembentuk barang.",
          "Cocokkan deskripsi invoice dengan brosur atau dokumen katalog teknis.",
          "Pastikan merk dan tipe model tercantum jika ada pada fisik barang."
        ],
        "faq": [
          {"q": "Apa akibatnya jika uraian barang di PIB tidak sesuai dengan fisik barang saat diperiksa?", "a": "Dapat dikenakan sanksi administrasi berupa denda Notul atau pembetulan PIB, bahkan penundaan rilis barang jika menyangkut komoditas Lartas."},
          {"q": "Apakah nama barang dalam Bahasa Inggris diperbolehkan di PIB?", "a": "Boleh, namun pada pengisian PIB sistem CEISA disarankan mencantumkan uraian spesifik dalam Bahasa Indonesia beserta istilah teknis internasionalnya."}
        ]
    },
    {
        "ep_num": 6,
        "title": "Jadwal Kapal Berubah Mendadak dalam Semalam",
        "badge_text": "Operational Coordination",
        "lead_cast": "Tasya, Budi & Yusuf",
        "subtitle": "Keterlambatan kapal (vessel delay & ETA rollover) mengubah seluruh rencana penjemputan truk dan jadwal gudang. Tasya mengoordinasikan pembaruan status satu pintu untuk Yusuf.",
        "kw": "perubahan jadwal kapal impor eta rollover",
        "panel1": {"title": "1. JADWAL KAPAL BERUBAH", "img": "assets/ep6-panel-1.png"},
        "panel2": {"title": "2. PEMETAAN DAMPAK", "img": "assets/ep6-panel-2.png"},
        "panel3": {"title": "3. ADJUSTMENT LAPANGAN", "img": "assets/ep6-panel-3.png"},
        "panel4": {"title": "4. UPDATE CONSOLIDATED", "img": "assets/ep6-panel-4.png"},
        "body": """
        <h2>Bab 1: Kejutan Delay Kapal di Pagi Hari</h2>
        <p>Pukul 07.00 WIB, Tasya menerima notifikasi pembaruan manifes dari pelayaran internasional. Kapal kontainer yang membawa kargo milik Yusuf mengalami keterlambatan cuaca (vessel rollover) dan jadwal sandar bergeser mundur 2 hari.</p>
        <p><em>"Pemberitahuan darurat! Kapal pengangkut mengalami delay 2 hari dan jadwal sandar berubah!"</em> ujar Tasya menginformasikan ke Budi dan Yusuf.</p>

        <h2>Bab 2: Mengendalikan Efek Domino Operasional</h2>
        <p>Perubahan ETA kapal secara mendadak berdampak pada:</p>
        <ul>
          <li>Jadwal pembokingan armada truk penjemputan di pelabuhan.</li>
          <li>Ketersediaan ruang bongkar dan tenaga kerja di gudang penerima milik Yusuf.</li>
          <li>Batas waktu pembayaran penyelesaian dokumen kepabeanan.</li>
        </ul>

        <h2>Bab 3: Komunikasi Control-Tower Satu Pintu M2B</h2>
        <p>Tasya langsung melakukan rescheduling jadwal angkutan tanpa dikenakan denda cancellation fee, sementara Budi menyesuaikan tim lapangan. Yusuf menerima laporan terkonsolidasi yang jelas tanpa kebingungan.</p>
        """,
        "checklist": [
          "Pantau pembaruan posisi vessel (vessel tracking) secara berkala menjelang ETA.",
          "Pastikan kontrak armada angkutan memiliki klausul fleksibilitas jadwal delay kapal.",
          "Informasikan tim gudang penerima terkait pembaruan estimasi tanggal tiba barang.",
          "Gunakan satu pintu komunikasi resmi penanganan ekspor-impor."
        ],
        "faq": [
          {"q": "Apakah importir bisa mengklaim ganti rugi kepada pelayaran jika kapal delay?", "a": "Klausul Bill of Lading standar internasional umumnya membebaskan pelayaran dari ganti rugi keterlambatan akibat cuaca atau keadaan kahar (force majeure)."},
          {"q": "Bagaimana M2B membantu penanganan delay kapal?", "a": "M2B memantau pergerakan kapal secara realtime dan langsung menyesuaikan jadwal truk serta dokumen pabean agar tidak memicu denda penumpukan."}
        ]
    },
    {
        "ep_num": 7,
        "title": "5 Tanda Bahaya Sebelum Kontainer Anda Bergerak",
        "badge_text": "Weekly Recap",
        "lead_cast": "Tim M2B (Semua Karakter)",
        "subtitle": "Rangkuman mingguan 5 titik kritis operasional impor dari Hari 1 hingga Hari 6. Matriks kesiapan shipment dan panduan keputusan Go/No-Go sebelum barang diberangkatkan.",
        "kw": "kesiapan shipment ekspor impor indonesia",
        "panel1": {"title": "1. EVALUASI KESIAPAN", "img": "assets/ep7-panel-1.png"},
        "panel2": {"title": "2. DOKUMEN & OPERASIONAL", "img": "assets/ep7-panel-2.png"},
        "panel3": {"title": "3. COST & COMPLIANCE", "img": "assets/ep7-panel-3.png"},
        "panel4": {"title": "4. GO / NO-GO DECISION", "img": "assets/ep7-panel-4.png"},
        "body": """
        <h2>Bab 1: Rangkuman Pembelajaran Mingguan Tim M2B</h2>
        <p>Sepanjang 6 hari pertama, kita melihat bagaimana masalah-masalah kecil seperti selisih angka dokumen, salah HS Code, keterlambatan truk, hingga delay kapal dapat menimbulkan kerugian finansial yang signifikan jika tidak ditangani dengan teliti.</p>
        <p>Bu Mayang dan seluruh tim M2B merangkum <strong>5 Tanda Bahaya (Red Flags)</strong> yang wajib diperiksa sebelum menyetujui pengiriman barang:</p>

        <h2>Bab 2: 5 Tanda Bahaya Utama</h2>
        <ol>
          <li><strong>Ketidakcocokan Data Dokumen (Discrepancy):</strong> Berat, jumlah, atau nomor B/L tidak presisi di Invoice & Packing List.</li>
          <li><strong>Deskripsi Barang Terlalu Umum:</strong> Menggunakan istilah 'spare parts' tanpa spesifikasi teknis dan material.</li>
          <li><strong>Izin Lartas Belum Terverifikasi:</strong> Barang sudah dikirim padahal perizinan teknis (BPOM/SNI/Karantina) belum rilis.</li>
          <li><strong>Batas Free Time Demurrage Sempit:</strong> Tidak menghitung potensi antrean pelabuhan dan pemeriksaan pabean.</li>
          <li><strong>Jadwal Truk Tidak Fleksibel:</strong> Vendor angkutan tidak siap mengantisipasi perubahan ETA kapal.</li>
        </ol>

        <h2>Bab 3: Keputusan Strategis Go / No-Go</h2>
        <p>Dengan menerapkan matriks kesiapan M2B, setiap risiko pengiriman dapat dimitigasi lebih awal sebelum barang meninggalkan pelabuhan asal.</p>
        """,
        "checklist": [
          "Jalankan audit dokumen 3-stage sebelum pengajuan PIB.",
          "Verifikasi kepatuhan regulasi pabean dan tarif HS Code BTKI.",
          "Hitung simulasi landed cost lengkap termasuk free time demurrage.",
          "Pastikan tim penanganan operasional siap sedia di lokasi pelabuhan."
        ],
        "faq": [
          {"q": "Mengapa pemeriksaan kesiapan pengiriman harus dilakukan sebelum kapal berangkat?", "a": "Karena pengoreksian dokumen di pelabuhan asal jauh lebih cepat dan murah dibandingkan mengoreksi dokumen setelah kontainer tiba di pelabuhan tujuan."},
          {"q": "Bagaimana cara berkonsultasi dengan tim PPJK M2B?", "a": "Anda dapat menghubungi tim konsultan ahli M2B via WhatsApp atau form konsultasi resmi di website m2b.co.id."}
        ]
    }
]

for ep in episodes_data:
    html_content = get_template(
        title=ep["title"],
        ep_num=ep["ep_num"],
        badge_text=ep["badge_text"],
        lead_cast=ep["lead_cast"],
        subtitle=ep["subtitle"],
        kw=ep["kw"],
        panel1=ep["panel1"],
        panel2=ep["panel2"],
        panel3=ep["panel3"],
        panel4=ep["panel4"],
        body_html=ep["body"],
        checklist_items=ep["checklist"],
        faq_items=ep["faq"]
    )
    file_path = os.path.join(base_dir, f"episode-{ep['ep_num']:02d}.html")
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(html_content)
    print(f"Rebuilt: episode-{ep['ep_num']:02d}.html")

print("All Episode HTML files rebuilt with 100% UNIQUE 4-PANEL IMAGES!")
