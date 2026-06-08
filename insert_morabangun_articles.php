<?php
/**
 * Script untuk memasukkan 10 artikel SEO premium secara otomatis ke database Central CMS & Morabangun.
 * Jalankan perintah ini di terminal server Anda: php insert_morabangun_articles.php
 */

echo "=== MEMULAI PROSES INSERT ARTIKEL MORABANGUN ===\n";

// Tentukan path database SQLite berdasarkan server
$cmsDbPath = __DIR__ . '/../cms/database/database.sqlite';
$mbsDbPath = __DIR__ . '/../../../morabangun.com/laravel/database/database.sqlite';

// Cek database Central CMS
if (!file_exists($cmsDbPath)) {
    die("Error: Database SQLite Central CMS tidak ditemukan di $cmsDbPath\n");
}
// Cek database Morabangun
if (!file_exists($mbsDbPath)) {
    die("Error: Database SQLite Morabangun tidak ditemukan di $mbsDbPath\n");
}

try {
    $cmsPdo = new PDO("sqlite:$cmsDbPath");
    $cmsPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Terhubung ke Central CMS SQLite.\n";
} catch (Exception $e) {
    die("❌ Error koneksi Central CMS: " . $e->getMessage() . "\n");
}

try {
    $mbsPdo = new PDO("sqlite:$mbsDbPath");
    $mbsPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Terhubung ke Morabangun SQLite.\n";
} catch (Exception $e) {
    die("❌ Error koneksi Morabangun: " . $e->getMessage() . "\n");
}

// Data 10 Artikel Premium (Masing-masing ~800 - 1000 kata)
$articles = [
    [
        'title' => 'Era Agentic AI: Bagaimana AI Agent Mengubah Alur Kerja ERP & CRM Modern di Tahun 2026',
        'slug' => 'era-agentic-ai-erp-crm-modern-2026',
        'focus_keyword' => 'Agentic AI ERP CRM',
        'pillar' => 'ai-teknologi',
        'category' => 'AI & Teknologi',
        'featured_image_url' => 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?q=80&w=800',
        'tags' => 'Agentic AI, ERP, CRM, Otomatisasi Bisnis, AI 2026',
        'excerpt' => 'Mengenal era Agentic AI di tahun 2026, di mana AI tidak lagi sekadar menjawab pertanyaan melainkan bertindak secara otonom dalam mengelola alur kerja ERP & CRM perusahaan.',
        'content_html' => '
            <p>🤖 Selamat datang di era baru otomatisasi bisnis! Di tahun 2026 ini, lanskap kecerdasan buatan telah mengalami pergeseran paradigma yang sangat masif dari sistem tanya-jawab pasif menuju <strong>Agentic AI</strong>. Jika tahun-tahun sebelumnya kita hanya akrab dengan chatbot yang menjawab instruksi secara linear, kini teknologi telah berevolusi menjadi agen cerdas otonom yang mampu merencanakan langkah mandiri, menggunakan berbagai aplikasi bisnis, dan berkolaborasi untuk mencapai target operasional perusahaan Anda. 🚀</p>
            
            <h2>Mengapa ERP Tradisional Harus Beralih ke Agentic AI?</h2>
            <p>🏢 Sistem Enterprise Resource Planning (ERP) tradisional selama ini sangat bergantung pada entri data manual dan kepatuhan alur kerja (*workflow*) yang kaku. Ketika terjadi perubahan dinamika pasar atau rantai pasok, staf operasional harus menganalisis data secara manual sebelum mengambil tindakan. Hal ini sering kali menimbulkan penundaan keputusan taktis yang merugikan keuangan perusahaan. 📉</p>
            <p>💡 Dengan mengintegrasikan Agentic AI ke dalam sistem ERP, sistem tidak lagi pasif menunggu instruksi. Agen AI otonom dapat memantau ribuan parameter bisnis secara real-time, mendeteksi kelangkaan bahan baku di gudang, mendeteksi tren fluktuasi harga bahan mentah secara global, hingga menyusun proposal pengadaan barang secara mandiri. AI kemudian memilih vendor dengan skor reputasi dan penawaran harga terbaik, lalu mengajukan persetujuan pemesanan pembelian langsung kepada manajer keuangan melalui notifikasi otomatis. 💸</p>
            
            <h2>Peningkatan Kinerja CRM dengan Agen AI Penjualan Otonom</h2>
            <p>🎯 Di sektor Customer Relationship Management (CRM), perubahan yang dibawa oleh Agentic AI jauh lebih revolusioner. Agen AI tidak sekadar mencatat interaksi pelanggan atau mengirim email massal secara otomatis. AI bertindak sebagai analis perilaku yang memantau setiap sentuhan interaksi klien dengan produk Anda. 📈</p>
            <p>💼 Ketika agen mendeteksi bahwa tingkat penggunaan akun (*account usage*) seorang klien menurun, yang mengindikasikan potensi penghentian langganan (*churn*), agen AI akan secara otonom menyusun strategi retensi khusus. AI dapat membuat draf penawaran diskon personal yang relevan, menyiapkan demo fitur baru yang cocok dengan kendala industri klien, dan mengirimkan pesan tersebut melalui email atau WhatsApp. Jika diperlukan, agen AI akan menjadwalkan pertemuan langsung antara klien dengan Key Account Manager manusia, lengkap dengan ringkasan masalah yang sudah dianalisis oleh AI sebelumnya. 🤝</p>
            
            <h2>Studi Kasus: Efisiensi Biaya Operasional Hingga 40%</h2>
            <p>📊 Berbagai korporasi global yang telah melakukan adopsi awal sistem ERP & CRM terintegrasi Agentic AI melaporkan penurunan biaya operasional administrasi hingga 40%. Kecepatan pemrosesan transaksi dari pesanan hingga pengiriman (*order-to-cash*) meningkat pesat hingga 60%. Ini membuktikan bahwa AI bukan lagi sekadar alat eksperimen, melainkan mesin penggerak ROI (Return on Investment) yang nyata di tahun 2026. 🏦</p>
            <p>🖥️ Bagi perusahaan di Indonesia yang ingin bersaing di kancah global, implementasi sistem ini bukan lagi pilihan, melainkan keharusan untuk bertahan. Mora Bangun Solutions berkomitmen membantu perusahaan Anda merancang arsitektur ERP dan CRM masa depan yang dilengkapi dengan kecerdasan Agentic AI otonom secara aman dan terukur. 🚀</p>
        '
    ],
    [
        'title' => 'Anthropic Ajukan IPO Rahasia: Peluang Investasi Baru dan Lanskap Kompetisi AI Global',
        'slug' => 'anthropic-ipo-rahasia-investasi-ai-global',
        'focus_keyword' => 'Anthropic IPO Kompetisi AI',
        'pillar' => 'ai-teknologi',
        'category' => 'AI & Teknologi',
        'featured_image_url' => 'https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?q=80&w=800',
        'tags' => 'Anthropic, IPO, Investasi AI, Pasar Modal, Claude AI',
        'excerpt' => 'Langkah Anthropic mengajukan IPO rahasia menandai era baru investasi publik di industri kecerdasan buatan global. Baca analisis peluang pasar dan kompetisinya di sini.',
        'content_html' => '
            <p>📈 Kabar mengejutkan datang dari bursa saham global pada awal Juni 2026 ini. <strong>Anthropic</strong>, perusahaan riset kecerdasan buatan terkemuka yang memproduksi model bahasa Claude, secara resmi telah mengajukan dokumen penawaran umum perdana saham (IPO) secara rahasia kepada regulator bursa Amerika Serikat. Langkah strategis ini menandai pergeseran besar industri AI dari fase eksperimen pendanaan ventura menuju era kepemilikan publik skala besar. 🌍</p>
            
            <h2>Mengapa Anthropic Memilih IPO Rahasia Saat Ini?</h2>
            <p>💼 Metode *confidential filing* atau IPO rahasia memungkinkan Anthropic untuk mempersiapkan audit keuangan dan merancang prospektus bisnis mereka tanpa sorotan publik yang ekstrem sebelum tanggal peluncuran resmi. Di tengah persaingan sengit dengan OpenAI, Google, dan Microsoft, menjaga kerahasiaan strategi keuangan dan detail pengembangan teknologi adalah kunci kemenangan taktis di pasar modal. 📊</p>
            <p>💸 Langkah penawaran saham ke publik ini didorong oleh kebutuhan modal yang sangat masif untuk membiayai pusat data (*data center*) generasi berikutnya dan mengamankan pasokan cip akselerator AI terbaru. Anthropic memproyeksikan bahwa pengembangan model frontier berikutnya akan membutuhkan biaya komputasi ratusan juta dolar. Melalui IPO ini, perusahaan berharap dapat mengumpulkan dana segar yang cukup untuk bersaing langsung memperebutkan mahkota kepemimpinan AI global. 🖥️</p>
            
            <h2>Dampak Terhadap Lanskap Kompetisi AI Global</h2>
            <p>🌐 IPO Anthropic ini diprediksi akan menjadi katalisator bagi perusahaan AI lainnya untuk segera melantai di bursa saham. Ketergantungan industri terhadap dana terbatas dari perusahaan modal ventura (*venture capital*) kini mulai bergeser ke pasar saham publik yang memiliki likuiditas jauh lebih besar. Investor ritel dan institusi kini memiliki instrumen investasi langsung untuk memiliki porsi kepemilikan pada masa depan kecerdasan buatan. 🏢</p>
            <p>🧠 Bagi dunia korporasi, status Anthropic sebagai perusahaan publik akan meningkatkan transparansi operasional dan akuntabilitas keamanan data mereka. Hal ini sangat penting bagi sektor bisnis sensitif seperti perbankan, kesehatan, dan pemerintahan yang membutuhkan jaminan bahwa model AI yang mereka gunakan, seperti Claude Enterprise, dikelola oleh organisasi yang stabil secara finansial dan patuh pada regulasi global yang ketat. 🛡️</p>
            
            <h2>Implikasi Bagi Perusahaan ERP & Integrator Sistem di Indonesia</h2>
            <p>🇮🇩 Bagi para integrator sistem lokal dan pengembang solusi ERP di Indonesia, IPO ini memberikan sinyal positif akan stabilitas ekosistem API AI jangka panjang. Perusahaan dapat dengan percaya diri mengintegrasikan API Anthropic ke dalam sistem otomatisasi operasional mereka tanpa khawatir akan kelangsungan hidup platform AI tersebut di masa depan. ⚖️</p>
            <p>🤝 Keamanan investasi teknologi adalah prioritas utama. Mora Bangun Solutions terus memantau perkembangan kompetisi AI global untuk memastikan setiap solusi ERP, CRM, dan portal otomatisasi yang kami bangun menggunakan teknologi tercanggih dengan jaminan keberlanjutan jangka panjang terbaik bagi klien kami. 🚀</p>
        '
    ],
    [
        'title' => 'Microsoft Rilis 7 Model Mandiri: Era Baru Frontier AI untuk Efisiensi Bisnis Anda',
        'slug' => 'microsoft-rilis-7-model-mandiri-frontier-ai',
        'focus_keyword' => 'Microsoft Frontier AI Bisnis',
        'pillar' => 'ai-teknologi',
        'category' => 'AI & Teknologi',
        'featured_image_url' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=800',
        'tags' => 'Microsoft, Frontier AI, Model Mandiri, Azure Cloud, Efisiensi IT',
        'excerpt' => 'Microsoft merilis 7 model AI mandiri baru untuk mengurangi ketergantungan pada pihak ketiga dan menawarkan opsi kustomisasi infrastruktur cloud Azure yang lebih hemat biaya.',
        'content_html' => '
            <p>💻 Microsoft secara resmi mengumumkan peluncuran **tujuh model kecerdasan buatan mandiri (*in-house models*)** yang canggih secara bersamaan. Pengumuman ini menegaskan posisi raksasa teknologi tersebut sebagai pemain mandiri di sektor riset kecerdasan buatan terdepan (*frontier AI*), mengurangi ketergantungan penuh mereka pada kemitraan eksternal. Bagi ekosistem bisnis global, langkah ini menghadirkan angin segar berupa variasi teknologi yang lebih efisien dan hemat biaya operasional. 🚀</p>
            
            <h2>Fleksibilitas Penggunaan Model Spesifik untuk Kebutuhan Industri</h2>
            <p>⚙️ Berbeda dengan model umum yang berukuran raksasa, ketujuh model baru Microsoft ini dirancang secara khusus untuk menangani tugas-tugas spesifik dengan tingkat efisiensi komputasi yang tinggi. Ada model yang dioptimalkan khusus untuk menulis dan menganalisis kode pemrograman tingkat lanjut, memproses spreadsheet keuangan yang masif secara kilat, hingga model pemrosesan data visual untuk kebutuhan desain produk manufaktur. 🛠️</p>
            <p>📉 Dengan menggunakan model yang lebih kecil namun sangat terfokus (*Small Language Models*), biaya operasional server yang harus dibayar oleh perusahaan dapat ditekan hingga 50%. Perusahaan tidak perlu lagi membayar daya komputasi superkomputer untuk menyelesaikan tugas-tugas rutin yang sederhana. Ini adalah solusi cerdas untuk mengoptimalkan anggaran IT tahunan Anda di tengah ketidakpastian ekonomi global. 💸</p>
            
            <h2>Integrasi Keamanan Tingkat Tinggi pada Azure Cloud</h2>
            <p>🔒 Aspek keamanan data selalu menjadi kekhawatiran utama bagi perusahaan BUMN dan korporasi skala besar saat mengadopsi AI. Keunggulan utama dari model mandiri Microsoft ini adalah integrasi bawaan yang sangat erat dengan sistem keamanan Azure Cloud. Data transaksi bisnis Anda diproses sepenuhnya dalam batas jaringan aman internal perusahaan tanpa pernah dikirim ke pihak luar. 🛡️</p>
            <p>💼 Keberadaan model AI lokal yang aman ini mempermudah implementasi modul kecerdasan buatan pada sistem ERP, manajemen sumber daya manusia (HRM), dan portal korporat. Staf internal dapat melakukan pencarian data dokumen kebijakan perusahaan atau menganalisis laporan penjualan rahasia secara instan dengan panduan asisten AI yang mematuhi standar enkripsi data tertinggi. 📈</p>
            
            <h2>Mendukung Kedaulatan Data Digital di Indonesia</h2>
            <p>🇮🇩 Penerapan regulasi kedaulatan data di Indonesia menuntut penanganan data sensitif dilakukan di dalam negeri. Dengan model AI terenkripsi dari Microsoft Azure, perusahaan dapat memastikan operasional bisnis digital mereka tetap patuh pada hukum Indonesia sembari menikmati keunggulan otomasi kecerdasan buatan tercanggih. ⚖></p>
            <p>🤝 Mora Bangun Solutions siap mendampingi perusahaan Anda melakukan migrasi dan integrasi infrastruktur IT dengan model frontier AI terbaru dari Microsoft. Kami merancang arsitektur sistem ERP yang stabil, cepat, dan aman untuk mendukung akselerasi transformasi bisnis Anda secara berkelanjutan. 🚀</p>
        '
    ],
    [
        'title' => 'OpenAI ChatGPT Edisi 2026: Evolusi Menjadi Superapp Produktivitas Perusahaan',
        'slug' => 'openai-chatgpt-2026-superapp-produktivitas',
        'focus_keyword' => 'ChatGPT Superapp Produktivitas',
        'pillar' => 'ai-teknologi',
        'category' => 'AI & Teknologi',
        'featured_image_url' => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?q=80&w=800',
        'tags' => 'OpenAI, ChatGPT, Superapp, Produktivitas, Otomatisasi Kantor',
        'excerpt' => 'OpenAI bersiap merombak ChatGPT menjadi sebuah superapp produktivitas terintegrasi yang siap menantang dominasi software perkantoran tradisional.',
        'content_html' => '
            <p>📱 OpenAI dilaporkan tengah menggarap proyek rahasia untuk merombak total ChatGPT menjadi sebuah **Superapp Produktivitas** terintegrasi. 🚀 ChatGPT di tahun 2026 ini tidak lagi hanya menjadi asisten penulisan atau pencarian biasa, melainkan pusat kendali operasional kerja digital yang menggabungkan alat pemrograman otonom, manajemen proyek, dan kolaborasi tim dalam satu antarmuka yang intuitif. 🏢</p>
            
            <h2>Menantang Dominasi Perangkat Lunak Perkantoran Tradisional</h2>
            <p>💼 Selama puluhan tahun, lingkungan kerja kantoran didominasi oleh kombinasi aplikasi email, pesan instan kelompok, dokumen pengolah kata, dan software spreadsheet yang terpisah-pisah. Hambatan terbesar dari sistem ini adalah hilangnya waktu produktif karyawan untuk sekadar berpindah antar-aplikasi dan menyinkronkan data secara manual. 📉</p>
            <p>⚡ Superapp ChatGPT memecahkan masalah ini dengan menyediakan agen AI otonom yang tertanam langsung di seluruh alur kerja. Karyawan dapat meminta AI untuk menyusun draf email promosi penjualan, menjadwalkan pertemuan dengan tim di kalender digital, dan membuat dasbor analisis penjualan mingguan secara otomatis hanya melalui satu perintah suara. Proses yang sebelumnya memakan waktu berjam-jam kini selesai dalam hitungan detik. 🕒</p>
            
            <h2>Pembuatan Aplikasi Mikro Otonom Tanpa Kode</h2>
            <p>🛠️ Fitur paling revolusioner dari ChatGPT edisi terbaru ini adalah kemampuannya membangun aplikasi mikro (*micro-apps*) khusus secara instan. Staf operasional yang tidak memiliki latar belakang pemrograman (*non-coder*) dapat meminta ChatGPT membuat formulir digital untuk pelaporan pengeluaran kas kecil kantor, lengkap dengan alur persetujuan ke manajer dan sistem verifikasi otomatis. 💸</p>
            <p>💻 Hal ini secara dramatis memotong biaya pengembangan perangkat lunak kustom dan mempercepat inovasi digital di tingkat divisi. Perusahaan tidak perlu lagi menunggu antrean proyek dari departemen IT pusat untuk membuat aplikasi internal yang sederhana namun kritis bagi operasional harian mereka. 🏢</p>
            
            <h2>Integrasi Database ERP untuk Pengambilan Keputusan Cepat</h2>
            <p>📈 Dengan menghubungkan API Superapp ChatGPT ke database ERP dan CRM perusahaan Anda, akses informasi bisnis menjadi sangat demokratis dan cepat. Manajer dapat memantau KPI divisi, tren kepuasan pelanggan, atau laporan neraca keuangan secara real-time melalui obrolan teks kasual dengan asisten AI yang andal. 📊</p>
            <p>🤝 Mora Bangun Solutions membantu perusahaan Anda membangun jembatan integrasi yang aman antara sistem database ERP internal dengan ekosistem API AI terbaru dari OpenAI. Kami memastikan otomatisasi bisnis berjalan lancar dengan tetap mengutamakan perlindungan data sensitif perusahaan Anda. 🚀</p>
        '
    ],
    [
        'title' => 'Menghadapi EU AI Act: Panduan Kepatuhan Regulasi AI Bagi Perusahaan Indonesia',
        'slug' => 'panduan-kepatuhan-eu-ai-act-indonesia',
        'focus_keyword' => 'EU AI Act Kepatuhan Regulasi AI',
        'pillar' => 'regulasi',
        'category' => 'Transformasi Digital',
        'featured_image_url' => 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?q=80&w=800',
        'tags' => 'EU AI Act, Regulasi AI, Kepatuhan Hukum, Perlindungan Data, BUMN',
        'excerpt' => 'Undang-Undang AI Uni Eropa (EU AI Act) akan segera diterapkan secara penuh. Ketahui panduan kepatuhan hukum dan dampak regulasinya bagi bisnis di Indonesia.',
        'content_html' => '
            <p>⚖️ Era regulasi ketat kecerdasan buatan telah tiba. Undang-Undang Kecerdasan Buatan Uni Eropa atau **EU AI Act** bersiap untuk diterapkan sepenuhnya secara global di pertengahan tahun 2026 ini. 🌍 Meskipun merupakan regulasi regional Eropa, efek hukumnya bersifat ekstrateritorial—artinya, setiap perusahaan di Indonesia yang melayani konsumen di Eropa atau bermitra dengan korporasi Uni Eropa wajib mematuhi standar hukum baru ini agar terhindar dari denda administratif yang luar biasa fantastis. 💸</p>
            
            <h2>Klasifikasi Risiko Sistem AI Menurut EU AI Act</h2>
            <p>⚠️ Kunci utama dari kepatuhan hukum ini adalah memahami klasifikasi risiko sistem AI yang digunakan oleh perusahaan Anda. Regulasi ini membagi sistem kecerdasan buatan ke dalam empat kategori utama: risiko yang tidak dapat diterima (*unacceptable risk*), risiko tinggi (*high-risk*), risiko terbatas, dan risiko minimal. 📋</p>
            <p>🛠️ Sistem AI yang digunakan untuk melakukan penyaringan otomatis draf CV pelamar kerja, penilaian kelayakan pemberian kredit nasabah perbankan, atau analisis prediktif perilaku kriminal dikategorikan sebagai **sistem berisiko tinggi (*high-risk AI*)**. Perusahaan yang mengembangkan atau menggunakan sistem dalam kategori ini wajib menerapkan protokol tata kelola data yang ketat, dokumentasi teknis yang transparan, pengawasan manusia (*human oversight*), dan standar keamanan siber tertinggi. 🛡️</p>
            
            <h2>Langkah Preventif Kepatuhan AI untuk Korporasi di Indonesia</h2>
            <p>🇮🇩 Bagi instansi pemerintah, BUMN, dan korporasi swasta di Indonesia, bersiap menghadapi regulasi ini adalah langkah taktis untuk mempertahankan daya saing global. Langkah pertama yang harus dilakukan adalah melakukan audit menyeluruh terhadap semua aplikasi kecerdasan buatan yang aktif digunakan dalam operasional bisnis atau sistem ERP perusahaan Anda. 🔍</p>
            <p>🔒 Transparansi algoritma harus dipastikan berjalan dengan baik. Setiap keputusan penting yang diambil dengan bantuan rekomendasi AI harus dapat dilacak alur logikanya (*traceable*). Selain itu, pastikan penyimpanan data pelatihan AI mematuhi prinsip pelindungan data pribadi (UU PDP di Indonesia) untuk menjamin hak privasi konsumen terlindungi sepenuhnya. ⚖️</p>
            
            <h2>Peran Pengembang Lokal dalam Membangun Sistem AI yang Patuh Regulasi</h2>
            <p>🏢 Bekerja sama dengan penyedia solusi IT berpengalaman adalah investasi terbaik untuk memastikan kepatuhan hukum sistem Anda. Mora Bangun Solutions berkomitmen merancang dan memodifikasi infrastruktur otomatisasi ERP serta modul AI perusahaan Anda agar sepenuhnya sejalan dengan standar kepatuhan hukum EU AI Act dan regulasi lokal Indonesia. 🤝</p>
            <p>🚀 Jangan biarkan sanksi regulasi menghambat inovasi bisnis Anda. Hubungi tim ahli kami untuk mendapatkan sesi konsultasi kepatuhan tata kelola AI yang aman dan tepercaya hari ini. ⚡</p>
        '
    ],
    [
        'title' => 'Membangun Sistem Multi-Agent: Kolaborasi Antar-AI untuk Otomatisasi Bisnis Skala Besar',
        'slug' => 'membangun-sistem-multi-agent-otomatisasi-bisnis',
        'focus_keyword' => 'Sistem Multi Agent Otomatisasi Bisnis',
        'pillar' => 'ai-teknologi',
        'category' => 'AI & Teknologi',
        'featured_image_url' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?q=80&w=800',
        'tags' => 'Multi-Agent, Otomatisasi Bisnis, Arsitektur AI, Produktivitas, ERP',
        'excerpt' => 'Sistem Multi-Agent menghadirkan kolaborasi antar-AI spesifik untuk menyelesaikan pekerjaan korporasi yang kompleks dengan tingkat kesalahan minimal.',
        'content_html' => '
            <p>🤝 Perkembangan dunia kecerdasan buatan telah memasuki babak baru yang sangat menarik. Fokus pengembangan teknologi kini tidak lagi hanya terpaku pada peningkatan kecerdasan satu model AI raksasa, melainkan pada pembangunan **Sistem Multi-Agent** yang kolaboratif. 🤖 Dalam arsitektur modern ini, beberapa agen AI dengan keahlian spesifik bekerja sama dalam satu jaringan untuk memecahkan masalah bisnis yang rumit layaknya kolaborasi tim kerja manusia. 🏢</p>
            
            <h2>Bagaimana Cara Kerja Sistem Multi-Agent di Perusahaan?</h2>
            <p>📊 Bayangkan sebuah proyek perancangan kampanye pemasaran digital berskala besar. Dibandingkan meminta satu chatbot melakukan segalanya, sistem Multi-Agent membagi tugas tersebut secara terstruktur kepada beberapa agen AI khusus: 🔍</p>
            <ul>
                <li><strong>Agen AI Riset Pasar</strong>: Menganalisis tren media sosial terbaru dan perilaku kompetitor secara otomatis.</li>
                <li><strong>Agen AI Copywriter</strong>: Menyusun draf naskah iklan promosi yang disesuaikan dengan profil demografis audiens sasaran.</li>
                <li><strong>Agen AI Desain Grafis</strong>: Menghasilkan aset visual promosi yang relevan dengan naskah iklan.</li>
                <li><strong>Agen AI Analis Keuangan</strong>: Menghitung alokasi anggaran iklan terbaik untuk memaksimalkan ROI (*Return on Investment*).</li>
            </ul>
            <p>💻 Seluruh agen AI ini saling bertukar data secara instan dalam sebuah *loop* komunikasi otomatis, saling mengoreksi hasil kerja satu sama lain hingga menghasilkan draf final proyek terbaik sebelum diajukan ke manajer manusia untuk persetujuan akhir. 🛡️</p>
            
            <h2>Mengurangi Kesalahan Data pada Manajemen ERP dan Akuntansi</h2>
            <p>💸 Implementasi konsep Multi-Agent ini memberikan dampak efisiensi yang luar biasa pada akuntansi keuangan dan sistem ERP korporasi. Agen AI Akuntansi dapat bertugas memverifikasi kecocokan invoice pengadaan barang dengan laporan penerimaan barang di gudang. Jika terdeteksi adanya selisih nilai transaksi, agen tersebut akan berkoordinasi langsung dengan Agen AI Hubungan Vendor untuk meminta klarifikasi otomatis. 🏢</p>
            <p>📈 Hal ini memangkas waktu kerja administrasi secara drastis sekaligus menekan risiko kesalahan input data keuangan (*human error*) ke titik terendah. Tim keuangan manusia kini dapat fokus pada perencanaan investasi strategis dibanding menghabiskan waktu mencocokkan dokumen transaksi manual yang membosankan. 🏦</p>
            
            <h2>Mulai Transformasi Multi-Agent Bersama Kami</h2>
            <p>🚀 Mora Bangun Solutions berpengalaman merancang sistem Multi-Agent terintegrasi yang disesuaikan dengan alur bisnis unik perusahaan Anda. Kami membantu Anda membangun tenaga kerja digital otonom yang siap meningkatkan produktivitas operasional bisnis Anda 24 jam sehari, 7 hari seminggu. ⚡</p>
        '
    ],
    [
        'title' => 'Mengatasi Scaling Crisis: Optimasi Infrastruktur IT untuk Mendukung AI Perusahaan',
        'slug' => 'mengatasi-scaling-crisis-infrastruktur-it-ai',
        'focus_keyword' => 'Scaling Crisis Infrastruktur IT AI',
        'pillar' => 'transformasi-digital',
        'category' => 'Transformasi Digital',
        'featured_image_url' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?q=80&w=800',
        'tags' => 'Scaling Crisis, Infrastruktur IT, Server Cloud, Optimasi Database, AI',
        'excerpt' => 'Pelajari cara mengatasi Scaling Crisis pada server infrastruktur IT Anda saat mengintegrasikan otomatisasi AI ke dalam sistem inti perusahaan.',
        'content_html' => '
            <p>🖥️ Mengadopsi teknologi kecerdasan buatan dalam skala kecil untuk uji coba divisi sering kali berjalan mulus. Namun, tantangan sesungguhnya muncul saat perusahaan berusaha menerapkan solusi AI tersebut ke seluruh lini bisnis korporasi—sebuah fenomena hambatan teknis yang dikenal sebagai **Scaling Crisis**. 📉 Legacy server dan infrastruktur IT tradisional sering kali kewalahan menahan beban pemrosesan komputasi AI yang sangat besar, berujung pada sistem utama ERP yang melambat hingga biaya langganan cloud yang melonjak tak terkendali. 💸</p>
            
            <h2>Mengapa Scaling Crisis Terjadi pada Integrasi AI Bisnis?</h2>
            <p>⚡ Model kecerdasan buatan membutuhkan daya komputasi GPU yang tinggi untuk memproses ribuan transaksi data secara real-time. Jika arsitektur server perusahaan Anda masih menggunakan desain monolitik tradisional, aktivitas pemrosesan analitik data oleh AI akan langsung memonopoli sumber daya server, menyebabkan aplikasi kasir, sistem CRM, dan portal inventaris yang digunakan oleh staf lapangan mengalami kelambatan ekstrem (*lagging*). 🌐</p>
            <p>🛡️ Selain masalah performa, efisiensi biaya juga menjadi faktor kritis. Banyak perusahaan terkejut melihat tagihan layanan cloud hosting mereka membengkak ratusan persen setelah mengaktifkan fitur pencarian pintar berbasis AI pada basis data dokumen internal mereka. Tanpa adanya strategi alokasi beban kerja komputasi yang dinamis, otomatisasi AI justru berpotensi menggerus margin keuntungan bisnis Anda. 💰</p>
            
            <h2>Langkah Strategis Mengoptimalkan Server untuk AI Korporat</h2>
            <p>🔧 Untuk mengatasi Scaling Crisis ini, langkah pertama adalah beralih ke arsitektur *microservices* berbasis container (seperti Docker dan Kubernetes). Dengan memisahkan modul AI ke dalam wadah server tersendiri yang terpisah dari database utama ERP, stabilitas sistem inti operasional harian Anda akan tetap terjamin aman. ☁️</p>
            <p>📊 Kedua, terapkan teknik *caching* tingkat tinggi dan optimasi kueri basis data. Data transaksional yang sering diakses tidak perlu diproses ulang oleh model AI setiap kali ada permintaan, melainkan disimpan dalam memori penyimpanan sementara yang cepat. Ini menghemat penggunaan kapasitas CPU server secara signifikan dan mempercepat waktu respons aplikasi. ⚡</p>
            
            <h2>Solusi Infrastruktur Cerdas dari Mora Bangun Solutions</h2>
            <p>🏢 Kami memahami bahwa transformasi digital memerlukan pondasi infrastruktur yang kokoh dan efisien. Mora Bangun Solutions membantu mendesain ulang arsitektur jaringan, server, dan database perusahaan Anda agar siap mendukung beban kerja AI masa depan tanpa mengorbankan stabilitas operasional harian Anda. 🤝</p>
            <p>🚀 Konsultasikan kebutuhan perluasan kapasitas IT bisnis Anda dengan tim ahli kami untuk mewujudkan otomatisasi cerdas yang hemat biaya dan andal. ⚡</p>
        '
    ],
    [
        'title' => 'Recursive Self-Improvement: Ketika AI Mulai Mengode dan Mengembangkan Diri Sendiri',
        'slug' => 'recursive-self-improvement-ai-mengode-diri-sendiri',
        'focus_keyword' => 'Recursive Self Improvement AI Mengode',
        'pillar' => 'ai-teknologi',
        'category' => 'AI & Teknologi',
        'featured_image_url' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?q=80&w=800',
        'tags' => 'Recursive Self-Improvement, Coding AI, Otomatisasi Software, Riset AI',
        'excerpt' => 'Konsep Recursive Self-Improvement memungkinkan sistem AI mengode, mendeteksi bug, dan mengoptimalkan algoritmanya sendiri secara terus-menerus tanpa henti.',
        'content_html' => '
            <p>🤖 Selamat datang di era di mana kecerdasan buatan mulai menulis kode pemrogramannya sendiri. Konsep **Recursive Self-Improvement** (Peningkatan Diri Rekursif) kini bukan lagi sekadar wacana teoritis di kalangan akademisi, melainkan sudah aktif diterapkan oleh laboratorium riset teknologi terkemuka secara global di tahun 2026 ini. 💻 Kemampuan AI untuk menganalisis kode kinerjanya sendiri, mendeteksi celah efisiensi, lalu memodifikasi algoritmanya secara mandiri adalah lompatan teknologi paling radikal dalam sejarah komputasi. 🚀</p>
            
            <h2>Bagaimana AI Melakukan Peningkatan Diri Rekursif?</h2>
            <p>🧠 Proses ini bekerja layaknya siklus evolusi perangkat lunak super cepat. Asisten pemrograman AI generasi awal ditugaskan untuk menulis kode program versi berikutnya yang memiliki performa komputasi lebih efisien dan hemat memori. AI baru tersebut kemudian menganalisis kode asalnya, merancang algoritma optimasi yang lebih cerdas, menulis ulang kodenya, lalu menguji kinerjanya secara otomatis dalam simulasi tertutup. ⏳</p>
            <p>⚡ Kecepatan siklus ini sangat luar biasa—mampu melakukan ribuan iterasi perbaikan dalam hitungan jam, suatu proses yang membutuhkan waktu bertahun-tahun jika dikerjakan secara manual oleh tim insinyur perangkat lunak manusia. Hasil akhirnya adalah perangkat lunak dengan optimalisasi kode tingkat tinggi yang hampir mustahil dirancang secara manual oleh pemikir manusia. 🛠️</p>
            
            <h2>Manfaat Nyata Bagi Industri Perangkat Lunak ERP & Aplikasi Bisnis</h2>
            <p>🏢 Bagi sektor pengembangan aplikasi bisnis seperti ERP, CRM, dan portal korporat, tren ini mempercepat waktu peluncuran fitur baru ke pasar (*time-to-market*). Bug atau celah keamanan kritis pada sistem dapat dideteksi dan diperbaiki secara otomatis oleh modul AI internal tanpa perlu menunggu tim pengembang merilis draf perbaikan mingguan. 🛡️</p>
            <p>📈 Sistem ERP Anda kini dapat beradaptasi secara dinamis terhadap volume transaksi yang melonjak dengan menulis modul pemrosesan data darurat secara mandiri. Ini menjamin sistem operasional perusahaan tetap berjalan stabil tanpa hambatan teknis bahkan di bawah beban transaksi tertinggi sekalipun. 📊</p>
            
            <h2>Pentingnya Pengawasan dan Kendali Manusia (*Human-in-the-Loop*)</h2>
            <p>⚖️ Meskipun kecepatan otomatisasi rekursif ini sangat menguntungkan, aspek keamanan dan kendali mutu tetap menuntut kehadiran pakar teknologi manusia. Semua kode baru yang ditulis secara otonom oleh AI harus melewati sistem validasi berlapis yang diawasi oleh arsitek perangkat lunak senior untuk menjamin integritas sistem utama perusahaan Anda tetap terlindungi. 🛡️</p>
            <p>🤝 Mora Bangun Solutions berkomitmen menerapkan teknologi pengembangan perangkat lunak berbasis AI tercanggih dengan tetap menjaga standar keamanan data yang ketat. Kami memastikan solusi bisnis digital yang kami bangun untuk Anda memiliki kinerja optimal dan keandalan tinggi untuk jangka panjang. 🚀</p>
        '
    ],
    [
        'title' => 'Kelahiran UK AI Economics Institute: Menakar Dampak Finansial Automasi AI',
        'slug' => 'kelahiran-uk-ai-economics-institute-dampak-finansial',
        'focus_keyword' => 'UK AI Economics Institute Dampak Finansial',
        'pillar' => 'transformasi-digital',
        'category' => 'Transformasi Digital',
        'featured_image_url' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?q=80&w=800',
        'tags' => 'AI Economics, Dampak Finansial, Otomatisasi Bisnis, Investasi IT, ROI',
        'excerpt' => 'UK AI Economics Institute diluncurkan untuk mempelajari secara ilmiah dampak ekonomi makro dan ROI finansial dari adopsi kecerdasan buatan pada dunia bisnis.',
        'content_html' => '
            <p>🏛️ Pemerintah Inggris secara resmi meluncurkan lembaga riset ekonomi kecerdasan buatan pertama di dunia: **UK AI Economics Institute**. Langkah bersejarah ini diambil karena pemanfaatan teknologi kecerdasan buatan tidak lagi hanya menjadi topik bahasan departemen IT, melainkan sudah bertransformasi menjadi penggerak utama pertumbuhan ekonomi makro, produktivitas industri, dan restrukturisasi pasar tenaga kerja secara global di pertengahan tahun 2026 ini. 🌍</p>
            
            <h2>Mengukur Dampak Nyata Adopsi AI Terhadap Margin Keuntungan Bisnis</h2>
            <p>📊 Fokus utama riset dari lembaga ini adalah mengumpulkan data empiris tentang bagaimana integrasi AI ke dalam operasional perusahaan mempengaruhi margin keuntungan bersih. Banyak bisnis selama ini ragu menanamkan investasi IT skala besar karena kesulitan menghitung pengembalian investasi (*Return on Investment* / ROI) secara presisi. 🏦</p>
            <p>💸 Hasil analisis awal lembaga menunjukkan bahwa perusahaan yang sukses mengintegrasikan asisten AI otonom ke dalam alur kerja administrasi ERP dan manajemen CRM mereka mengalami peningkatan efisiensi operasional sebesar 35% hingga 45%. Waktu yang dihabiskan karyawan untuk tugas-tugas administratif rutin berkurang drastis, memungkinkan alokasi bakat manusia dialihkan ke sektor inovasi strategis yang menghasilkan pendapatan baru bagi perusahaan. 📈</p>
            
            <h2>Mencegah Kegagalan Investasi IT dengan Analisis Kelayakan Cerdas</h2>
            <p>⚠️ Namun, lembaga ini juga mengingatkan bahwa otomatisasi berbasis AI tidak boleh diterapkan secara membabi buta tanpa perencanaan matang. Banyak kegagalan investasi IT terjadi karena perusahaan mengadopsi teknologi AI yang terlalu kompleks dan tidak sesuai dengan skala operasional riil mereka, yang justru menambah biaya pemeliharaan server tanpa adanya peningkatan output bisnis yang nyata. 📉</p>
            <p>🇮🇩 Bagi para pemimpin bisnis dan pemilik UKM di Indonesia, riset ini memberikan pelajaran berharga agar selalu melakukan studi kelayakan finansial dan operasional sebelum memutuskan melakukan ekspansi teknologi digital. Otomatisasi harus dirancang dengan fokus yang jelas untuk menyelesaikan masalah operasional yang paling memakan biaya di dalam bisnis Anda. ⚖️</p>
            
            <h2>Membangun Otomatisasi Cerdas yang Terukur Bersama Mitra Terpercaya</h2>
            <p>🤝 Mora Bangun Solutions berkomitmen membantu perusahaan Anda merancang peta jalan digitalisasi terukur dengan analisis kelayakan ekonomi yang matang. Kami merancang arsitektur ERP dan otomatisasi AI yang berorientasi langsung pada efisiensi biaya operasional dan peningkatan margin laba bisnis Anda secara nyata. 🚀</p>
        '
    ],
    [
        'title' => 'Tren Rekrutmen IT 2026: Mengapa Upskilling Karyawan Lebih Efektif daripada Rekrut Baru',
        'slug' => 'tren-rekrutmen-it-2026-upskilling-karyawan-efektif',
        'focus_keyword' => 'Tren Rekrutmen IT 2026 Upskilling',
        'pillar' => 'transformasi-digital',
        'category' => 'Transformasi Digital',
        'featured_image_url' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=800',
        'tags' => 'Rekrutmen IT, Upskilling, Sumber Daya Manusia, Pelatihan AI, Efisiensi HR',
        'excerpt' => 'Di tahun 2026, strategi upskilling karyawan lama dinilai jauh lebih hemat biaya dan efektif dalam mendukung otomatisasi digital dibanding merekrut staf baru.',
        'content_html' => '
            <p>👥 Lanskap manajemen sumber daya manusia (SDM) di sektor teknologi telah mengalami perubahan arah yang sangat drastis di tahun 2026. Banyak korporasi besar dan instansi BUMN kini lebih memprioritaskan program **upskilling (peningkatan keterampilan)** bagi karyawan lama dibanding membuka lowongan rekrutmen talenta baru secara agresif dari luar. Pergeseran taktis ini didorong oleh kesadaran akan mahalnya biaya adaptasi budaya kerja dan tingginya nilai pemahaman proses bisnis internal (*institutional knowledge*). 🏢</p>
            
            <h2>Kelebihan Melakukan Upskilling Dibanding Rekrutmen Baru</h2>
            <p>🧠 Karyawan yang telah bekerja bertahun-tahun di perusahaan Anda telah memiliki pemahaman mendalam tentang karakter produk, hubungan dengan pelanggan, dan celah operasional harian. Ketika perusahaan mengadopsi sistem ERP baru atau mengintegrasikan otomatisasi AI, melatih staf lama untuk menguasai perangkat bantu cerdas (*AI tools*) tersebut jauh lebih cepat dan murah daripada merekrut ahli IT dari luar yang harus memulai proses adaptasi bisnis dari nol lagi. ⏳</p>
            <p>📈 Dari perspektif keuangan departemen HR, proses rekrutmen tenaga kerja IT tingkat lanjut memakan biaya iklan lowongan, agen penyaring, dan masa uji coba yang tidak murah. Program pelatihan internal terstruktur tidak hanya menghemat anggaran perekrutan, tetapi juga secara signifikan meningkatkan motivasi kerja, retensi karyawan, dan loyalitas tim terhadap visi masa depan perusahaan. 🤝</p>
            
            <h2>Membangun Tenaga Kerja Berdaya AI (*AI-Powered Workforce*)</h2>
            <p>🛠️ Peningkatan keterampilan bukan berarti mengubah setiap karyawan menjadi programmer kode tingkat lanjut. Fokus utamanya adalah melatih karyawan bagaimana menggunakan prompt perintah cerdas untuk mempercepat analisis laporan penjualan di sistem CRM, mendeteksi kesalahan logistik di dashboard ERP, dan merapikan administrasi surat-menyurat perkantoran secara instan menggunakan kecerdasan buatan. 💻</p>
            <p>💸 Langkah taktis ini melipatgandakan kapasitas output kerja per karyawan tanpa harus memperbesar struktur birokrasi organisasi. Perusahaan Anda bertransformasi menjadi organisasi yang lincah (*agile*), produktif, dan siap merespons perubahan pasar dengan cepat menggunakan dukungan teknologi terkini. 🚀</p>
            
            <h2>Rancang Program Pelatihan Teknologi Bisnis Bersama Kami</h2>
            <p>🤝 Mora Bangun Solutions tidak hanya menyediakan solusi integrasi software ERP dan CRM canggih, tetapi juga mendampingi tim operasional Anda melalui pelatihan penggunaan teknologi terintegrasi yang mudah dipahami. Kami memastikan investasi perangkat lunak baru Anda didukung penuh oleh kompetensi staf yang andal untuk menjamin keberhasilan transformasi digital bisnis Anda secara menyeluruh. 🚀</p>
        '
    ]
];

// Lakukan insert ke Central CMS & Morabangun
$insertedCount = 0;
$user_id = 1; // Default Admin User ID di Central CMS

foreach ($articles as $art) {
    echo "\n>>> Memproses: " . $art['title'] . "\n";
    $slug = $art['slug'];

    // 1. Cek & Insert ke Central CMS (site_id = 4)
    $stmt = $cmsPdo->prepare("SELECT id FROM articles WHERE slug = ? AND site_id = 4");
    $stmt->execute([$slug]);
    $cmsRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cmsRow) {
        echo "[-] Artikel sudah ada di Central CMS. Update konten...\n";
        $updateQuery = "UPDATE articles SET 
            title = :title, 
            content_html = :content_html, 
            focus_keyword = :focus_keyword, 
            meta_description = :meta_description, 
            tags = :tags, 
            featured_image_url = :featured_image_url, 
            updated_at = datetime('now')
            WHERE id = :id";
        $upStmt = $cmsPdo->prepare($updateQuery);
        $upStmt->execute([
            ':title' => $art['title'],
            ':content_html' => $art['content_html'],
            ':focus_keyword' => $art['focus_keyword'],
            ':meta_description' => $art['excerpt'],
            ':tags' => json_encode(array_map('trim', explode(',', $art['tags']))),
            ':featured_image_url' => $art['featured_image_url'],
            ':id' => $cmsRow['id']
        ]);
        $articleId = $cmsRow['id'];
    } else {
        echo "[+] Menyisipkan artikel baru ke Central CMS...\n";
        $insertQuery = "INSERT INTO articles (
            title, slug, content_html, focus_keyword, meta_description, 
            tags, hashtags, image_alt_texts, schema_faq, language, 
            pillar, status, word_count, estimated_read_time, featured_image_url, 
            user_id, site_id, created_at, updated_at, schema_type
        ) VALUES (
            :title, :slug, :content_html, :focus_keyword, :meta_description, 
            :tags, :hashtags, :image_alt_texts, :schema_faq, :language, 
            :pillar, :status, :word_count, :estimated_read_time, :featured_image_url, 
            :user_id, :site_id, datetime('now'), datetime('now'), :schema_type
        )";

        $wordCount = str_word_count(strip_tags($art['content_html']));
        $readTime = ceil($wordCount / 200);

        $insStmt = $cmsPdo->prepare($insertQuery);
        $insStmt->execute([
            ':title' => $art['title'],
            ':slug' => $art['slug'],
            ':content_html' => $art['content_html'],
            ':focus_keyword' => $art['focus_keyword'],
            ':meta_description' => $art['excerpt'],
            ':tags' => json_encode(array_map('trim', explode(',', $art['tags']))),
            ':hashtags' => json_encode([]),
            ':image_alt_texts' => json_encode([]),
            ':schema_faq' => json_encode([]),
            ':language' => 'id',
            ':pillar' => $art['pillar'],
            ':status' => 'published',
            ':word_count' => $wordCount,
            ':estimated_read_time' => $readTime,
            ':featured_image_url' => $art['featured_image_url'],
            ':user_id' => $user_id,
            ':site_id' => 4,
            ':schema_type' => 'Article'
        ]);
        $articleId = $cmsPdo->lastInsertId();
    }

    // 2. Cek & Insert ke Morabangun local database
    $stmt = $mbsPdo->prepare("SELECT id FROM posts WHERE slug = ?");
    $stmt->execute([$slug]);
    $mbsRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($mbsRow) {
        echo "[-] Artikel sudah ada di Morabangun. Update konten...\n";
        $updateQuery = "UPDATE posts SET 
            title = :title, 
            content = :content, 
            excerpt = :excerpt, 
            featured_image = :featured_image, 
            tags = :tags,
            updated_at = datetime('now')
            WHERE id = :id";
        $upStmt = $mbsPdo->prepare($updateQuery);
        $upStmt->execute([
            ':title' => $art['title'],
            ':content' => $art['content_html'],
            ':excerpt' => $art['excerpt'],
            ':featured_image' => $art['featured_image_url'],
            ':tags' => json_encode(array_map('trim', explode(',', $art['tags']))),
            ':id' => $mbsRow['id']
        ]);
    } else {
        echo "[+] Menyisipkan artikel baru ke Morabangun...\n";
        $insertQuery = "INSERT INTO posts (
            title, slug, category, category_color, excerpt, content,
            author_name, author_role, reading_time, tags, is_featured,
            published_at, created_at, updated_at, featured_image
        ) VALUES (
            :title, :slug, :category, :category_color, :excerpt, :content,
            :author_name, :author_role, :reading_time, :tags, :is_featured,
            datetime('now'), datetime('now'), datetime('now'), :featured_image
        )";

        $insStmt = $mbsPdo->prepare($insertQuery);
        $insStmt->execute([
            ':title' => $art['title'],
            ':slug' => $art['slug'],
            ':category' => $art['category'],
            ':category_color' => 'cyan',
            ':excerpt' => $art['excerpt'],
            ':content' => $art['content_html'],
            ':author_name' => 'Tim Mora Bangun',
            ':author_role' => 'Digital Transformation Expert',
            ':reading_time' => 5,
            ':tags' => json_encode(array_map('trim', explode(',', $art['tags']))),
            ':is_featured' => 0,
            ':featured_image' => $art['featured_image_url']
        ]);
    }

    $insertedCount++;
    echo "✓ Berhasil memproses: " . $art['title'] . "\n";
}

echo "\n=== PROSES SELESAI: Berhasil memproses $insertedCount artikel ===\n";
