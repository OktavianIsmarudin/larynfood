<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laryn - Sistem Inventory & Keuangan')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8F9FA;
            color: #212529;
        }

        :root {
            --primary-color: #21489d;
            --primary-dark: #1a3a7d;
            --secondary-color: #F59E0B;
            --text-dark: #212529;
            --text-muted: #6C757D;
            --border-color: #DEE2E6;
        }

        /* Custom Button Styles */
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(33, 72, 157, 0.3);
            color: white;
        }

        /* Navbar */
        .navbar-custom {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color) !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand i {
            color: var(--secondary-color);
        }

        .nav-link {
            color: var(--text-dark) !important;
            font-weight: 500;
            margin: 0 15px;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            border: none;
            color: white;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary-custom:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(33, 72, 157, 0.3);
        }

        .btn-outline-custom {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            background: white;
        }

        .btn-outline-custom:hover {
            background-color: var(--primary-color);
            color: white;
        }

        /* Footer */
        .footer-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 60px 0 30px;
            margin-top: 80px;
        }

        .footer-custom .container {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        .footer-custom h5 {
            font-weight: 700;
            margin-bottom: 24px;
            font-size: 18px;
            letter-spacing: 0.3px;
        }

        .footer-custom .footer-brand {
            max-width: 100%;
            margin: 0;
            padding: 0;
        }

        .footer-custom .footer-brand p {
            line-height: 1.8;
            margin-bottom: 20px;
            font-size: 14px;
            letter-spacing: 0.2px;
        }

        .footer-custom .footer-links,
        .footer-custom .footer-contact {
            padding-left: 0;
            padding-right: 0;
            margin: 0;
        }

        .footer-custom .footer-links li,
        .footer-custom .footer-contact li {
            margin-bottom: 14px;
            font-size: 14px;
            line-height: 1.6;
        }

        .footer-custom .footer-links li:last-child,
        .footer-custom .footer-contact li:last-child {
            margin-bottom: 0;
        }

        .footer-custom .footer-links a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.8);
            transition: color 0.3s;
        }

        .footer-custom .footer-links a:hover {
            color: white;
        }

        .footer-custom .footer-contact {
            line-height: 1.8;
        }

        .footer-custom .footer-contact i {
            width: 16px;
            text-align: center;
            opacity: 0.9;
            margin-right: 10px;
        }

        .footer-custom a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-custom a:hover {
            color: white;
        }

        .social-icons a {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            margin-right: 10px;
            transition: all 0.3s;
        }

        .social-icons a:hover {
            background: white;
            color: var(--primary-color);
        }

        .floating-cart-btn {
            position: fixed;
            right: 24px;
            bottom: 96px;
            width: 64px;
            height: 64px;
            border-radius: 50%;
            border: none;
            background: linear-gradient(135deg, #d33a2c 0%, #b93025 100%);
            color: #fff;
            box-shadow: 0 8px 20px rgba(211, 58, 44, 0.35);
            z-index: 1055;
        }

        .floating-chat-btn {
            position: fixed;
            right: 24px;
            bottom: 24px;
            min-width: 64px;
            height: 64px;
            border-radius: 999px;
            border: none;
            background: linear-gradient(135deg, #6a5cf6 0%, #5a3fd5 100%);
            color: #fff;
            box-shadow: 0 10px 22px rgba(90, 63, 213, 0.38);
            z-index: 1056;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 0 20px;
            font-weight: 700;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .floating-chat-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 26px rgba(90, 63, 213, 0.42);
        }

        .floating-chat-btn i {
            font-size: 22px;
        }

        .chatbot-panel {
            position: fixed;
            right: 24px;
            bottom: 104px;
            width: min(420px, calc(100vw - 32px));
            max-height: min(700px, calc(100vh - 130px));
            background: #f5f5f8;
            border-radius: 22px;
            border: 1px solid #e3e4ea;
            box-shadow: 0 24px 48px rgba(18, 24, 40, 0.22);
            z-index: 1060;
            display: none;
            flex-direction: column;
            overflow: hidden;
        }

        .chatbot-panel.show {
            display: flex;
        }

        .chatbot-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 20px 14px;
            background: #f5f5f8;
        }

        .chatbot-agent-select {
            border: none;
            background: transparent;
            font-size: 20px;
            font-weight: 700;
            color: #1f2430;
            outline: none;
            padding-right: 24px;
        }

        .chatbot-close {
            border: none;
            background: transparent;
            color: #1f2430;
            font-size: 24px;
            line-height: 1;
        }

        .chatbot-body {
            padding: 4px 20px 12px;
            overflow-y: auto;
        }

        .chatbot-intro {
            text-align: center;
            padding: 12px 10px 18px;
            color: #1f2430;
        }

        .chatbot-intro i {
            font-size: 42px;
            color: #5a3fd5;
            margin-bottom: 10px;
        }

        .chatbot-intro h4 {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .chatbot-intro p {
            font-size: 17px;
            color: #3f4654;
            margin-bottom: 0;
        }

        .chatbot-personas-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            margin-bottom: 16px;
        }

        .chatbot-persona-card {
            border: 2px solid #dfe2e8;
            border-radius: 14px;
            padding: 12px;
            background: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .chatbot-persona-card:hover {
            border-color: #b0adf5;
            box-shadow: 0 4px 12px rgba(90, 63, 213, 0.1);
        }

        .chatbot-persona-card.selected {
            border-color: #6a5cf6;
            background: #f2f0ff;
            box-shadow: 0 6px 16px rgba(90, 63, 213, 0.2);
        }

        .chatbot-persona-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #eef0f5;
            margin-bottom: 8px;
        }

        .chatbot-persona-name {
            font-weight: 700;
            color: #1f2430;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .chatbot-persona-title {
            font-size: 12px;
            color: #888f9f;
            margin-bottom: 4px;
        }

        .chatbot-persona-desc {
            font-size: 11px;
            color: #636b78;
            line-height: 1.4;
        }

        .chatbot-selected-persona {
            display: none;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            background: #f2f0ff;
            border-radius: 12px;
            margin-bottom: 12px;
        }

        .chatbot-selected-persona.show {
            display: flex;
        }

        .chatbot-selected-persona-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e6e8f0;
            background: #fff;
            flex-shrink: 0;
        }

        .chatbot-selected-persona-info h5 {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            color: #1f2430;
        }

        .chatbot-selected-persona-info p {
            margin: 0;
            font-size: 12px;
            color: #636b78;
        }

        .chatbot-input:disabled {
            background: #f5f5f5;
            color: #999;
            cursor: not-allowed;
        }

        .chatbot-send:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .chatbot-quick {
            border-top: 1px solid #dfe2e8;
            border-bottom: 1px solid #dfe2e8;
            padding: 8px 0;
            margin-bottom: 14px;
        }

        .chatbot-quick button {
            width: 100%;
            border: none;
            text-align: left;
            background: transparent;
            padding: 13px 2px;
            color: #242a37;
            font-size: 18px;
            border-bottom: 1px solid #e6e8ee;
        }

        .chatbot-quick button:last-child {
            border-bottom: none;
        }

        .chatbot-chip-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 12px;
        }

        .chatbot-chip {
            border: 1px solid #d8dce7;
            border-radius: 999px;
            background: #fff;
            color: #2a3040;
            padding: 7px 14px;
            font-size: 14px;
        }

        .chatbot-chip.active {
            border-color: #6a5cf6;
            color: #5a3fd5;
            background: #f2f0ff;
        }

        .chatbot-chatlog {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 10px;
            max-height: 220px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .chatbot-msg {
            max-width: 90%;
            border-radius: 14px;
            padding: 10px 12px;
            font-size: 14px;
            line-height: 1.45;
        }

        .chatbot-msg.user {
            align-self: flex-end;
            background: #5a3fd5;
            color: #fff;
        }

        .chatbot-msg.bot {
            align-self: flex-start;
            background: #fff;
            border: 1px solid #e2e6ef;
            color: #212834;
        }

        .chatbot-form-wrap {
            margin: 0 20px 10px;
            border: 2px solid #141822;
            border-radius: 28px;
            padding: 14px;
            background: #fff;
        }

        .chatbot-input {
            width: 100%;
            border: none;
            outline: none;
            resize: none;
            min-height: 72px;
            font-size: 17px;
            color: #1f2430;
        }

        .chatbot-form-footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px;
        }

        .chatbot-send {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            border: none;
            background: #eceef4;
            color: #888f9f;
            font-size: 22px;
        }

        .chatbot-send.active {
            background: #5a3fd5;
            color: #fff;
        }

        .chatbot-disclaimer {
            text-align: center;
            color: #636b78;
            font-size: 12px;
            padding: 0 18px 14px;
        }

        .floating-cart-count {
            position: absolute;
            top: -6px;
            right: -2px;
            min-width: 24px;
            height: 24px;
            border-radius: 999px;
            background: #fff;
            color: #d33a2c;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #f0f0f0;
        }

        .popup-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 14px;
        }

        .popup-item-row {
            border-bottom: 1px solid #e9ecef;
            padding: 12px 0;
        }

        .popup-item-row:last-child {
            border-bottom: none;
        }

        .popup-qty-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid #d0d7e2;
            background: #fff;
            color: #21489d;
            font-weight: 700;
        }

        .track-timeline-item {
            display: flex;
            gap: 12px;
            margin-bottom: 18px;
        }

        .track-dot {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            margin-top: 4px;
            background: #d1d5db;
        }

        .track-timeline-item.active .track-dot {
            background: #f59e0b;
        }

        .track-timeline-item.done .track-dot {
            background: #10b981;
        }

        @media (max-width: 768px) {
            .floating-cart-btn {
                right: 16px;
                bottom: 84px;
                width: 58px;
                height: 58px;
            }

            .floating-chat-btn {
                right: 16px;
                bottom: 16px;
                min-width: 58px;
                height: 58px;
                padding: 0 16px;
                font-size: 13px;
            }

            .chatbot-panel {
                right: 8px;
                bottom: 84px;
                width: calc(100vw - 16px);
                max-height: calc(100vh - 104px);
                border-radius: 18px;
            }

            .chatbot-intro h4 {
                font-size: 34px;
            }

            .chatbot-intro p {
                font-size: 15px;
            }

            .chatbot-quick button {
                font-size: 16px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-utensils"></i>
                Laryn
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('landing') }}#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.explore') }}">Explore Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('landing') }}#about">Tentang</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <button type="button" class="floating-cart-btn" id="floatingCartButton" aria-label="Buka keranjang">
        <i class="fas fa-shopping-cart"></i>
        <span class="floating-cart-count" id="floatingCartCount">{{ count(session('cart', [])) }}</span>
    </button>

    <button type="button" class="floating-chat-btn" id="floatingChatButton" aria-label="Buka chatbot">
        <i class="fas fa-comments"></i>
        <span>Tanya CS</span>
    </button>

    <div class="chatbot-panel" id="chatbotPanel" aria-live="polite">
        <div class="chatbot-header">
            <div style="font-weight: 700; color: #1f2430;">Pilih CS</div>
            <button type="button" class="chatbot-close" id="chatbotCloseBtn" aria-label="Tutup chatbot">&times;</button>
        </div>
        <div class="chatbot-body">
            <div class="chatbot-personas-grid" id="chatbotPersonasGrid"></div>

            <div class="chatbot-selected-persona" id="chatbotSelectedPersona">
                <div id="selectedPersonaAvatar"></div>
                <div class="chatbot-selected-persona-info">
                    <h5 id="selectedPersonaName"></h5>
                    <p id="selectedPersonaDescription"></p>
                </div>
            </div>

            <div class="chatbot-intro">
                <i class="fas fa-paper-plane"></i>
                <h4>Halo</h4>
                <p>Ada yang bisa saya bantu?</p>
            </div>

            <div class="chatbot-quick">
                <button type="button" class="chatbot-quick-btn" data-question="Saya ingin lihat produk yang paling laris hari ini.">Saya ingin lihat produk yang paling laris hari ini.</button>
                <button type="button" class="chatbot-quick-btn" data-question="Saya butuh info biaya kirim dan cara checkout.">Saya butuh info biaya kirim dan cara checkout.</button>
                <button type="button" class="chatbot-quick-btn" data-question="Bagaimana cara tracking pesanan saya?">Bagaimana cara tracking pesanan saya?</button>
            </div>

            <div class="chatbot-chip-list" id="chatbotTopicList">
                <button type="button" class="chatbot-chip active" data-topic="produk">Produk</button>
                <button type="button" class="chatbot-chip" data-topic="checkout">Checkout</button>
                <button type="button" class="chatbot-chip" data-topic="tracking">Tracking</button>
                <button type="button" class="chatbot-chip" data-topic="pembayaran">Pembayaran</button>
            </div>

            <div class="chatbot-chatlog" id="chatbotChatlog"></div>
        </div>

        <form id="chatbotForm" class="chatbot-form-wrap">
            <textarea id="chatbotInput" class="chatbot-input" placeholder="Tulis pertanyaan Anda" disabled></textarea>
            <div class="chatbot-form-footer">
                <button type="submit" id="chatbotSendBtn" class="chatbot-send" aria-label="Kirim" disabled>
                    <i class="fas fa-arrow-up"></i>
                </button>
            </div>
        </form>

        <div class="chatbot-disclaimer">Informasi dari AI mungkin tidak akurat</div>
    </div>

    <div class="modal fade" id="popupCartModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Keranjang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="popupCartItems"></div>
                    <div class="d-flex justify-content-between mt-3">
                        <strong>Total</strong>
                        <strong id="popupCartTotal">Rp0</strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="w-100 d-grid gap-2">
                        <button type="button" class="btn btn-primary-custom" id="openCheckoutModalBtn">Lanjut Checkout</button>
                        <div class="input-group">
                            <input type="text" class="form-control" id="trackOrderInput" placeholder="Masukkan Order ID untuk tracking">
                            <button class="btn btn-outline-custom" type="button" id="openTrackByIdBtn">Track</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="popupPaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="popup-card mb-3">
                        <p class="mb-2"><strong>Pembayaran Transfer</strong></p>
                        @if($guestPaymentSetting?->qris_image_path)
                        <div class="mb-3">
                            <p class="mb-1"><strong>QRIS</strong></p>
                            <img src="{{ asset('storage/' . $guestPaymentSetting->qris_image_path) }}" alt="QRIS" class="img-fluid rounded border" style="max-height: 260px;">
                        </div>
                        @endif

                        @if($guestPaymentSetting?->bank_name || $guestPaymentSetting?->account_number)
                        <p class="mb-1">{{ $guestPaymentSetting->bank_name ?? 'Bank' }}: <strong>{{ $guestPaymentSetting->account_number ?? '-' }}</strong> ({{ $guestPaymentSetting->account_holder ?? '-' }})</p>
                        @else
                        <p class="mb-1">BCA: <strong>1234567890</strong> (Laryn)</p>
                        <p class="mb-0">Mandiri: <strong>9876543210</strong> (Laryn)</p>
                        @endif

                        @if($guestPaymentSetting?->instructions)
                        <p class="mb-0 mt-2 text-muted">{{ $guestPaymentSetting->instructions }}</p>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Bukti Pembayaran</label>
                        <input type="file" id="popupPaymentProofInput" name="payment_proof" class="form-control" accept="image/*" required form="popupCheckoutForm">
                        <small class="text-muted">Upload bukti transfer sebelum klik tombol Sudah Bayar.</small>
                        <div class="text-danger small mt-1" id="popupPaymentProofError"></div>
                    </div>
                    <p class="mb-3 text-muted">Setelah transfer selesai, klik tombol di bawah untuk mengisi identitas dan mengirim bukti pembayaran.</p>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary-custom" id="confirmPaidBtn">Sudah Bayar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="popupIdentityModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Data Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="popupCheckoutForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama</label>
                                <input type="text" name="customer_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No Telp</label>
                                <input type="text" name="customer_phone" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email (Opsional)</label>
                                <input type="email" name="customer_email" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pengiriman</label>
                                <select name="delivery_method" id="popupDeliveryMethod" class="form-select" required>
                                    <option value="pickup">Ambil di Tempat</option>
                                    <option value="delivery">Dikirim</option>
                                </select>
                            </div>
                            <div class="col-12" id="popupAddressGroup" style="display:none;">
                                <label class="form-label">Alamat Pengiriman</label>
                                <textarea name="shipping_address" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Keterangan</label>
                                <textarea name="notes" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="d-grid mt-3">
                            <button type="submit" class="btn btn-primary-custom" id="popupSubmitOrderBtn">Kirim Pesanan</button>
                        </div>
                    </form>
                    <div class="text-danger small mt-2" id="popupCheckoutError"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="popupTrackingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Track Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">Order: <strong id="popupTrackOrderNumber">-</strong></p>
                    <div class="popup-card mb-3" id="popupTrackStatus">Memuat status...</div>
                    <div id="popupTrackTimeline"></div>
                    <div class="alert alert-info d-none" id="popupTrackNote"></div>
                    <p class="text-muted text-center mb-0">Auto refresh setiap 5 detik</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary-custom" id="orderAgainBtn">Pesan Lagi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer-custom" id="contact">
        <div class="container">
            <div class="row gy-4 gx-5">
                <div class="col-lg-4 footer-brand">
                    <h5><i class="fas fa-utensils me-2"></i>Laryn</h5>
                    <p style="color: rgba(255, 255, 255, 0.8);">
                        Sistem manajemen inventory dan keuangan terbaik untuk bisnis makanan Anda.
                        Kelola stock, transaksi, dan laporan dengan mudah.
                    </p>
                    <div class="social-icons mt-3">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <h5>Menu</h5>
                    <ul class="list-unstyled footer-links">
                        <li class="mb-2"><a href="{{ route('landing') }}#home">Home</a></li>
                        <li class="mb-2"><a href="{{ route('landing') }}#products">Produk</a></li>
                        <li class="mb-2"><a href="{{ route('landing') }}#about">Tentang</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6">
                    <h5>Kontak</h5>
                    <ul class="list-unstyled footer-contact">
                        <li><i class="fas fa-envelope"></i> laryn0322@gmail.com</li>
                        <li><i class="fas fa-phone"></i> +62 821-3805-9664</li>
                        <li><i class="fas fa-map-marker-alt"></i> Griya Taman Asri FF 04, Tawangsari, Sidoarjo</li>
                        <li><i class="fas fa-clock"></i> Senin - Sabtu: 17.00 - 20.30</li>
                        <li><i class="fas fa-clock"></i> Minggu: 08.00 - 20.30</li>
                    </ul>
                </div>
            </div>

            <hr style="border-color: rgba(255, 255, 255, 0.2); margin: 30px 0 20px;">

            <div class="text-center" style="color: rgba(255, 255, 255, 0.7);">
                <p class="mb-0">&copy; {{ date('Y') }} Laryn. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.hash) {
                setTimeout(function() {
                    const target = document.querySelector(window.location.hash);
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }, 100);
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const cartDataUrl = '{{ route('customer.cart.data') }}';
            const cartUpdateUrl = '{{ route('customer.cart.update') }}';
            const checkoutUrl = '{{ route('customer.checkout.process') }}';
            const trackStatusTemplate = '{{ route('customer.track.status', ['orderNumber' => '__ORDER__']) }}';
            const cartRemoveTemplate = '{{ route('customer.cart.remove', ['id' => '__ID__']) }}';
            const chatbotMessageUrl = '{{ route('chatbot.message') }}';

            const floatingCartCount = document.getElementById('floatingCartCount');
            const popupCartItems = document.getElementById('popupCartItems');
            const popupCartTotal = document.getElementById('popupCartTotal');
            const openCheckoutModalBtn = document.getElementById('openCheckoutModalBtn');
            const popupCheckoutForm = document.getElementById('popupCheckoutForm');
            const popupCheckoutError = document.getElementById('popupCheckoutError');
            const popupSubmitOrderBtn = document.getElementById('popupSubmitOrderBtn');
            const popupDeliveryMethod = document.getElementById('popupDeliveryMethod');
            const popupAddressGroup = document.getElementById('popupAddressGroup');
            const confirmPaidBtn = document.getElementById('confirmPaidBtn');
            const popupPaymentProofInput = document.getElementById('popupPaymentProofInput');
            const popupPaymentProofError = document.getElementById('popupPaymentProofError');
            const popupTrackOrderNumber = document.getElementById('popupTrackOrderNumber');
            const popupTrackStatus = document.getElementById('popupTrackStatus');
            const popupTrackTimeline = document.getElementById('popupTrackTimeline');
            const popupTrackNote = document.getElementById('popupTrackNote');
            const openTrackByIdBtn = document.getElementById('openTrackByIdBtn');
            const trackOrderInput = document.getElementById('trackOrderInput');
            const floatingChatButton = document.getElementById('floatingChatButton');
            const chatbotPanel = document.getElementById('chatbotPanel');
            const chatbotCloseBtn = document.getElementById('chatbotCloseBtn');
            const chatbotInput = document.getElementById('chatbotInput');
            const chatbotSendBtn = document.getElementById('chatbotSendBtn');
            const chatbotForm = document.getElementById('chatbotForm');
            const chatbotChatlog = document.getElementById('chatbotChatlog');
            const chatbotTopicList = document.getElementById('chatbotTopicList');
            const chatbotPersonasGrid = document.getElementById('chatbotPersonasGrid');
            const chatbotSelectedPersona = document.getElementById('chatbotSelectedPersona');
            const selectedPersonaName = document.getElementById('selectedPersonaName');
            const selectedPersonaDescription = document.getElementById('selectedPersonaDescription');
            const selectedPersonaAvatar = document.getElementById('selectedPersonaAvatar');
            const chatbotProfilesUrl = '{{ route('chatbot.profiles') }}';
            const chatbotIntroKey = 'chatbot_intro_done';

            const cartModal = new bootstrap.Modal(document.getElementById('popupCartModal'));
            const paymentModal = new bootstrap.Modal(document.getElementById('popupPaymentModal'));
            const identityModal = new bootstrap.Modal(document.getElementById('popupIdentityModal'));
            const trackingModal = new bootstrap.Modal(document.getElementById('popupTrackingModal'));

            let trackInterval = null;

            function formatRupiah(number) {
                return `Rp${Number(number || 0).toLocaleString('id-ID')}`;
            }

            function updateAddressVisibility() {
                const isDelivery = popupDeliveryMethod.value === 'delivery';
                popupAddressGroup.style.display = isDelivery ? 'block' : 'none';
                const addressField = popupAddressGroup.querySelector('textarea[name="shipping_address"]');
                addressField.required = isDelivery;
            }

            popupDeliveryMethod.addEventListener('change', updateAddressVisibility);
            updateAddressVisibility();

            async function fetchCartData() {
                const response = await fetch(cartDataUrl, {
                    headers: { 'Accept': 'application/json' }
                });
                return response.json();
            }

            async function renderCartPopup() {
                const data = await fetchCartData();
                floatingCartCount.textContent = data.count;

                if (!data.items.length) {
                    popupCartItems.innerHTML = '<p class="text-muted mb-0">Keranjang masih kosong.</p>';
                    popupCartTotal.textContent = formatRupiah(0);
                    openCheckoutModalBtn.disabled = true;
                    return;
                }

                openCheckoutModalBtn.disabled = false;
                popupCartItems.innerHTML = data.items.map(item => `
                    <div class="popup-item-row">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>${item.name}</strong>
                                <div class="text-muted small">${formatRupiah(item.price)}</div>
                                <div class="mt-2 d-flex align-items-center gap-2">
                                    <button type="button" class="popup-qty-btn" data-action="minus" data-id="${item.id}">-</button>
                                    <span>${item.quantity}</span>
                                    <button type="button" class="popup-qty-btn" data-action="plus" data-id="${item.id}">+</button>
                                    <button type="button" class="btn btn-link text-danger p-0 ms-2" data-action="remove" data-id="${item.id}">Hapus</button>
                                </div>
                            </div>
                            <strong>${formatRupiah(item.subtotal)}</strong>
                        </div>
                    </div>
                `).join('');

                popupCartTotal.textContent = formatRupiah(data.subtotal);
            }

            async function updateCartQuantity(id, quantity) {
                await fetch(cartUpdateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ id, quantity })
                });
                await renderCartPopup();
            }

            async function removeCartItem(id) {
                const url = cartRemoveTemplate.replace('__ID__', id);
                await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                await renderCartPopup();
            }

            popupCartItems.addEventListener('click', async function(event) {
                const target = event.target.closest('[data-action]');
                if (!target) return;

                const action = target.getAttribute('data-action');
                const id = Number(target.getAttribute('data-id'));
                if (!id) return;

                const data = await fetchCartData();
                const item = data.items.find(i => Number(i.id) === id);
                if (!item) return;

                if (action === 'plus') {
                    await updateCartQuantity(id, item.quantity + 1);
                }

                if (action === 'minus') {
                    const nextQty = item.quantity - 1;
                    if (nextQty <= 0) {
                        await removeCartItem(id);
                    } else {
                        await updateCartQuantity(id, nextQty);
                    }
                }

                if (action === 'remove') {
                    await removeCartItem(id);
                }
            });

            document.getElementById('floatingCartButton').addEventListener('click', async function() {
                // Check if there's an active tracking session
                const trackedOrder = localStorage.getItem('last_tracked_order');
                const trackingActive = localStorage.getItem('tracking_modal_open') === 'true';

                if (trackedOrder && trackingActive) {
                    // Show tracking modal instead of cart
                    trackOrderInput.value = trackedOrder;
                    trackingModal.show();
                    startTrackingPolling(trackedOrder);
                } else {
                    // Show cart modal
                    await renderCartPopup();
                    cartModal.show();
                }
            });

            openCheckoutModalBtn.addEventListener('click', function() {
                cartModal.hide();
                popupCheckoutError.textContent = '';
                popupPaymentProofError.textContent = '';
                paymentModal.show();
            });

            confirmPaidBtn.addEventListener('click', function() {
                if (!popupPaymentProofInput.files || !popupPaymentProofInput.files.length) {
                    popupPaymentProofError.textContent = 'Silakan upload bukti pembayaran terlebih dahulu.';
                    return;
                }

                popupPaymentProofError.textContent = '';
                paymentModal.hide();
                identityModal.show();
            });

            async function loadTracking(orderNumber) {
                popupTrackOrderNumber.textContent = orderNumber;
                const response = await fetch(trackStatusTemplate.replace('__ORDER__', orderNumber), {
                    headers: { 'Accept': 'application/json' }
                });

                if (!response.ok) {
                    popupTrackStatus.textContent = 'Order tidak ditemukan.';
                    popupTrackTimeline.innerHTML = '';
                    // Clear tracking state if order not found
                    localStorage.removeItem('last_tracked_order');
                    localStorage.removeItem('tracking_modal_open');
                    return;
                }

                const data = await response.json();
                popupTrackStatus.textContent = data.status_label;
                popupTrackTimeline.innerHTML = data.steps.map(step => `
                    <div class="track-timeline-item ${step.done ? 'done' : (step.active ? 'active' : '')}">
                        <div class="track-dot"></div>
                        <div>
                            <div class="fw-bold">${step.title}</div>
                            <div class="text-muted small">${step.note}</div>
                            ${step.active ? '<div class="text-warning small fw-semibold">Sedang berlangsung...</div>' : ''}
                        </div>
                    </div>
                `).join('');

                // Check if order is completed (all steps done)
                if (data.status && data.status === 'delivered') {
                    localStorage.removeItem('last_tracked_order');
                    localStorage.removeItem('tracking_modal_open');
                }

                if (data.tracking_note) {
                    popupTrackNote.classList.remove('d-none');
                    popupTrackNote.innerHTML = `<strong>Keterangan Admin:</strong><br>${data.tracking_note}`;
                } else {
                    popupTrackNote.classList.add('d-none');
                }
            }

            function startTrackingPolling(orderNumber) {
                if (trackInterval) {
                    clearInterval(trackInterval);
                }

                loadTracking(orderNumber);
                trackInterval = setInterval(function() {
                    loadTracking(orderNumber);
                }, 5000);
            }

            document.getElementById('popupTrackingModal').addEventListener('hidden.bs.modal', function() {
                if (trackInterval) {
                    clearInterval(trackInterval);
                    trackInterval = null;
                }
                // Don't clear tracking state here - it will persist across page refresh
            });

            document.getElementById('popupTrackingModal').addEventListener('show.bs.modal', function() {
                // Mark tracking as active
                localStorage.setItem('tracking_modal_open', 'true');
            });

            document.getElementById('orderAgainBtn').addEventListener('click', function() {
                // Clear tracking state and navigate to products
                localStorage.removeItem('last_tracked_order');
                localStorage.removeItem('tracking_modal_open');
                window.location.href = '{{ route('products.explore') }}';
            });

            openTrackByIdBtn.addEventListener('click', function() {
                const orderNumber = trackOrderInput.value.trim();
                if (!orderNumber) return;
                localStorage.setItem('last_tracked_order', orderNumber);
                cartModal.hide();
                trackingModal.show();
                startTrackingPolling(orderNumber);
            });

            popupCheckoutForm.addEventListener('submit', async function(event) {
                event.preventDefault();
                popupCheckoutError.textContent = '';
                popupSubmitOrderBtn.disabled = true;
                popupSubmitOrderBtn.textContent = 'Memproses...';

                // Set tracking as active for this new order
                localStorage.setItem('tracking_modal_open', 'true');

                try {
                    const response = await fetch(checkoutUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: new FormData(popupCheckoutForm)
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        popupCheckoutError.textContent = data.message || 'Gagal memproses checkout.';
                        return;
                    }

                    popupCheckoutForm.reset();
                    updateAddressVisibility();
                    await renderCartPopup();

                    localStorage.setItem('last_tracked_order', data.order_number);
                    identityModal.hide();
                    trackingModal.show();
                    startTrackingPolling(data.order_number);
                } catch (error) {
                    popupCheckoutError.textContent = 'Terjadi kendala saat checkout.';
                } finally {
                    popupSubmitOrderBtn.disabled = false;
                    popupSubmitOrderBtn.textContent = 'Kirim Pesanan';
                }
            });

            document.querySelectorAll('form[action*="/customer/cart/add/"]').forEach(form => {
                form.addEventListener('submit', async function(event) {
                    event.preventDefault();

                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: new FormData(form)
                    });

                    if (response.ok) {
                        await renderCartPopup();
                    }
                });
            });

            function appendChatMessage(role, text) {
                const bubble = document.createElement('div');
                bubble.className = `chatbot-msg ${role}`;
                bubble.textContent = text;
                chatbotChatlog.appendChild(bubble);
                chatbotChatlog.scrollTop = chatbotChatlog.scrollHeight;
            }

            let selectedPersonaId = null;

            async function loadAndRenderPersonas() {
                try {
                    const response = await fetch(chatbotProfilesUrl);
                    const data = await response.json();
                    const profiles = data.profiles;

                    chatbotPersonasGrid.innerHTML = Object.values(profiles).map(profile => `
                        <button type="button" class="chatbot-persona-card" data-persona="${profile.id}">
                            <img class="chatbot-persona-avatar" src="${profile.avatar}" alt="${profile.name}" loading="lazy" />
                            <div class="chatbot-persona-name">${profile.name}</div>
                            <div class="chatbot-persona-title">${profile.title}</div>
                            <div class="chatbot-persona-desc">${profile.description}</div>
                        </button>
                    `).join('');

                    document.querySelectorAll('.chatbot-persona-card').forEach(card => {
                        card.addEventListener('click', function() {
                            const personaId = this.getAttribute('data-persona');
                            selectPersona(personaId, profiles[personaId]);
                        });
                    });

                    // Auto-select the CS bot since it's only one now
                    const firstPersonaId = Object.keys(profiles)[0];
                    if (firstPersonaId) {
                        selectPersona(firstPersonaId, profiles[firstPersonaId]);
                    }
                } catch (error) {
                    console.error('Error loading personas:', error);
                }
            }

            function selectPersona(personaId, profile) {
                selectedPersonaId = personaId;

                // Update card selection styling
                document.querySelectorAll('.chatbot-persona-card').forEach(card => {
                    card.classList.remove('selected');
                });
                document.querySelector(`[data-persona="${personaId}"]`).classList.add('selected');

                // Show selected persona info
                selectedPersonaAvatar.innerHTML = `<img class="chatbot-selected-persona-avatar" src="${profile.avatar}" alt="${profile.name}" loading="lazy" />`;
                selectedPersonaName.textContent = profile.name;
                selectedPersonaDescription.textContent = profile.description;
                chatbotSelectedPersona.classList.add('show');

                // Enable input & send button
                chatbotInput.disabled = false;
                updateSendButtonState();
                chatbotInput.focus();
            }

            function updateSendButtonState() {
                const hasText = chatbotInput.value.trim().length > 0;
                const isEnabled = selectedPersonaId !== null && hasText;
                chatbotSendBtn.disabled = !isEnabled;
                chatbotSendBtn.classList.toggle('active', isEnabled);
            }

            function hasShownChatbotIntro() {
                return sessionStorage.getItem(chatbotIntroKey) === '1';
            }

            function markChatbotIntroShown() {
                sessionStorage.setItem(chatbotIntroKey, '1');
            }

            function submitChatbotMessage() {
                chatbotForm.requestSubmit();
            }

            chatbotInput.addEventListener('input', updateSendButtonState);
            chatbotInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter' && !event.shiftKey) {
                    event.preventDefault();
                    if (!chatbotInput.disabled && chatbotInput.value.trim()) {
                        submitChatbotMessage();
                    }
                }
            });

            async function openChatbotPanel() {
                chatbotPanel.classList.add('show');
                chatbotPanel.style.display = 'flex';
                chatbotPanel.style.visibility = 'visible';
                chatbotPanel.style.pointerEvents = 'auto';

                if (chatbotPersonasGrid.children.length === 0) {
                    await loadAndRenderPersonas();
                }

                if (selectedPersonaId) {
                    chatbotInput.focus();
                }
            }

            function closeChatbotPanel() {
                chatbotPanel.classList.remove('show');
                chatbotPanel.style.display = 'none';
            }

            floatingChatButton.addEventListener('click', async function() {
                await openChatbotPanel();
            });

            chatbotCloseBtn.addEventListener('click', function() {
                closeChatbotPanel();
            });

            chatbotTopicList.addEventListener('click', function(event) {
                const chip = event.target.closest('[data-topic]');
                if (!chip) return;

                chatbotTopicList.querySelectorAll('.chatbot-chip').forEach(item => item.classList.remove('active'));
                chip.classList.add('active');
            });

            document.querySelectorAll('.chatbot-quick-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (!selectedPersonaId) {
                        chatbotInput.placeholder = 'Pilih CS dulu!';
                        return;
                    }
                    chatbotInput.value = btn.getAttribute('data-question') || '';
                    updateSendButtonState();
                    chatbotInput.focus();
                });
            });

            chatbotForm.addEventListener('submit', async function(event) {
                event.preventDefault();

                if (!selectedPersonaId) {
                    alert('Pilih CS terlebih dahulu!');
                    return;
                }

                const message = chatbotInput.value.trim();
                if (!message) return;

                const activeTopic = chatbotTopicList.querySelector('.chatbot-chip.active')?.getAttribute('data-topic') || 'produk';
                const isFirstMessage = !hasShownChatbotIntro();

                appendChatMessage('user', message);
                chatbotInput.value = '';
                updateSendButtonState();

                try {
                    const response = await fetch(chatbotMessageUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            message,
                            persona: selectedPersonaId,
                            topic: activeTopic,
                            is_first_message: isFirstMessage
                        })
                    });

                    const data = await response.json();
                    if (!response.ok) {
                        appendChatMessage('bot', data.message || 'Maaf, AI sedang sibuk. Coba beberapa saat lagi.');
                        return;
                    }

                    appendChatMessage('bot', data.answer || 'Maaf, saya belum bisa menjawab saat ini.');
                    if (isFirstMessage) {
                        markChatbotIntroShown();
                    }
                } catch (error) {
                    appendChatMessage('bot', 'Koneksi ke server bermasalah. Coba lagi sebentar ya.');
                }
            });

            // Note: Tracking state is preserved in localStorage
            // It will be checked & displayed when user clicks the floating cart button
            // No auto-open on page load

            renderCartPopup();
            updateSendButtonState();
        });
    </script>

    @stack('scripts')
</body>
</html>
