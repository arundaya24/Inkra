<?php
require_once __DIR__ . '/../config.php';
$gambarMedia = pengaturan()['media'];

$produk = [
    ['nama' => 'Kaos',    'foto' => $gambarMedia['Kaos'],    'ket' => 'Kaos sehari-hari, dengan desain khas Anda.'],
    ['nama' => 'Jaket',   'foto' => $gambarMedia['Jaket'],   'ket' => 'Tampil beda dengan sentuhan personal.'],
    ['nama' => 'Totebag', 'foto' => $gambarMedia['Totebag'], 'ket' => 'Bawa desain Anda ke mana saja.'],
];

$langkah = [
    ['Isi formulir',          'Ceritakan ide Anda di halaman Minta Penawaran: jenis produk, jumlah, desain, dan target waktu yang Anda inginkan.'],
    ['Kirim lewat WhatsApp',  'Setelah formulir dikirim, Anda otomatis diarahkan ke WhatsApp kami dengan pesan yang sudah terisi.'],
    ['Diskusikan detailnya',  'Tentukan bahan, posisi sablon, warna, dan penawaran harga bersama kami lewat chat.'],
    ['Sablon',                'Periksa dan setujui desain, lalu proses sablon dimulai sesuai ide, bahan, posisi sablon, dan warna.'],
    ['Pembayaran',            'Bayar sesuai penawaran harga yang sudah disepakati di chat.'],
    ['Pengiriman (opsional)', 'Sablon sudah jadi dan akan dikirim ke alamatmu jika kamu memilih untuk dikirim.'],
];

$faq = [
    ['Produk apa saja yang bisa disablon?',
     'Mulai dari kaos, jaket, hingga totebag. Untuk produk lainnya, sertakan detailnya saat meminta penawaran.'],
    ['Apa yang perlu saya sertakan saat meminta penawaran?',
     'Jenis produk, jumlah, desain atau ide, serta target waktu yang Anda inginkan.'],
    ['Bagaimana jika desain saya belum siap?',
     'Ceritakan ide Anda dan bantuan yang Anda butuhkan untuk menyiapkan desain.'],
    ['Apa perbedaan antara tinta sablon Plastisol, Rubber, dan DTF?',
     'Plastisol tahan lama dengan hasil padat, Rubber elastis dan lembut, sedangkan DTF sangat cocok untuk desain penuh warna yang detail.'],
    ['Apakah ada fasilitas revisi desain?',
     'Ya, kamu berhak mendapatkan pratinjau <em>mockup</em> dan kesempatan revisi desain sebelum proses cetak massal dimulai.'],
    ['Bagaimana metode pengiriman pesanan?',
     'Pesanan dapat dikirimkan melalui berbagai pilihan ekspedisi terpercaya atau diambil langsung ke workshop kami.'],
];
