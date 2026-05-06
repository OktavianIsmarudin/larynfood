<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Penjualan;
use App\Models\ProdukSiapJual;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Midtrans\Config;

class OrderController extends Controller
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Show explore products page
     */
    public function explore(Request $request)
    {
        $query = ProdukSiapJual::with(['stockGudang', 'produkPaket', 'user'])
            ->where('is_published', true)
            ->where('harga_jual', '>', 0);

        // Search by name
        if ($request->has('search') && $request->search) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        // Filter by price range
        if ($request->has('min_price') && $request->min_price) {
            $query->where('harga_jual', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->where('harga_jual', '<=', $request->max_price);
        }

        // Sort
        $sortBy = $request->get('sort', 'terbaru');
        switch ($sortBy) {
            case 'termurah':
                $query->orderBy('harga_jual', 'asc');
                break;
            case 'termahal':
                $query->orderBy('harga_jual', 'desc');
                break;
            case 'nama':
                $query->orderBy('nama_produk', 'asc');
                break;
            default: // terbaru
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        return view('customer.products.explore', compact('products'));
    }

    /**
     * Show cart page
     */
    public function cart()
    {
        $summary = $this->buildCartSummary();
        $cartItems = $summary['cart_items'];
        $total = $summary['subtotal'];

        return view('customer.cart', compact('cartItems', 'total'));
    }

    /**
     * Cart summary data for popup flow
     */
    public function cartData()
    {
        $summary = $this->buildCartSummary();

        return response()->json([
            'items' => $summary['items'],
            'subtotal' => $summary['subtotal'],
            'shipping_cost' => 15000,
            'total_with_delivery' => $summary['subtotal'] + 15000,
            'count' => $summary['count'],
        ]);
    }

    /**
     * Add product to cart
     */
    public function addToCart(Request $request, $id)
    {
        $product = ProdukSiapJual::findOrFail($id);
        $availablePaket = (int) ($product->stok_siap_jual ?? 0);

        if ($availablePaket <= 0) {
            $message = 'Stok produk habis.';

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return redirect()->back()->with('error', $message);
        }

        $cart = session()->get('cart', []);
        $currentQty = isset($cart[$id]) ? (int) $cart[$id]['quantity'] : 0;

        if (($currentQty + 1) > $availablePaket) {
            $message = 'Jumlah di keranjang melebihi stok siap jual (' . $availablePaket . ' paket).';

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return redirect()->back()->with('error', $message);
        }

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'name' => $product->nama_produk,
                'quantity' => 1,
                'price' => $product->harga_jual,
            ];
        }

        session()->put('cart', $cart);

        if ($request->expectsJson()) {
            $summary = $this->buildCartSummary();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang!',
                'count' => $summary['count'],
            ]);
        }

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    /**
     * Update cart item quantity
     */
    public function updateCart(Request $request)
    {
        if ($request->id && $request->quantity) {
            $cart = session()->get('cart', []);

            if (!isset($cart[$request->id])) {
                return response()->json(['success' => false, 'message' => 'Item tidak ditemukan'], 404);
            }

            $product = ProdukSiapJual::find($request->id);
            if (!$product) {
                return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
            }

            $requestedQty = max(1, (int) $request->quantity);
            $availablePaket = (int) ($product->stok_siap_jual ?? 0);
            if ($requestedQty > $availablePaket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah melebihi stok siap jual (' . $availablePaket . ' paket).',
                ], 422);
            }

            $cart[$request->id]['quantity'] = $requestedQty;
            session()->put('cart', $cart);

            return response()->json(['success' => true, 'message' => 'Keranjang berhasil diupdate']);
        }

        return response()->json(['success' => false, 'message' => 'Data tidak valid'], 422);
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        if ($request->expectsJson()) {
            $summary = $this->buildCartSummary();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus dari keranjang!',
                'count' => $summary['count'],
            ]);
        }

        return redirect()->back()->with('success', 'Produk berhasil dihapus dari keranjang!');
    }

    /**
     * Show checkout page
     */
    public function checkout()
    {
        return redirect()->route('landing');
    }

    /**
     * Process checkout for manual transfer payment
     */
    public function processCheckout(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'required|string',
            'delivery_method' => 'required|in:pickup,delivery',
            'shipping_address' => 'required_if:delivery_method,delivery|nullable|string',
            'notes' => 'nullable|string|max:1000',
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('landing')->with('error', 'Keranjang Anda kosong!');
        }

        try {
            DB::beginTransaction();

            $paymentProofPath = $request->file('payment_proof')->store('payment-proofs', 'public');

            // Calculate totals
            $subtotal = 0;
            $orderItems = [];

            foreach ($cart as $id => $item) {
                $product = ProdukSiapJual::with(['produkPaket.details.stockGudang', 'stockGudang'])
                    ->lockForUpdate()
                    ->findOrFail($id);

                $qtyPaket = max(1, (int) ($item['quantity'] ?? 1));
                $availablePaket = (int) ($product->stok_siap_jual ?? 0);
                if ($qtyPaket > $availablePaket) {
                    throw new \Exception(
                        'Stok produk "' . $product->nama_produk . '" tidak mencukupi. Tersedia: ' .
                        $availablePaket . ' paket, diminta: ' . $qtyPaket . ' paket.'
                    );
                }

                // Reserve stok saat checkout agar sinkron dengan explore produk
                $product->kurangiStokPenjualan($qtyPaket);

                $hargaPaket = (float) ($product->harga_jual ?? 0);
                $itemSubtotal = $hargaPaket * $qtyPaket;
                $subtotal += $itemSubtotal;

                $orderItems[] = [
                    'produk_siap_jual_id' => $id,
                    'product_name' => $product->nama_produk,
                    'quantity' => $qtyPaket,
                    'price' => $hargaPaket,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $shippingCost = $request->delivery_method === 'delivery' ? 15000 : 0;
            $total = $subtotal + $shippingCost;

            // Create order
            $notes = $request->notes;
            $orderPayload = [
                'order_number' => Order::generateOrderNumber(),
                'user_id' => Auth::id(),
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'shipping_address' => $request->delivery_method === 'delivery'
                    ? $request->shipping_address
                    : 'Ambil di tempat',
                'notes' => $notes,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total_amount' => $total,
                'payment_type' => 'manual_transfer',
                'payment_status' => 'pending',
                'status' => 'pending',
            ];

            if (Schema::hasColumn('orders', 'payment_proof_path')) {
                $orderPayload['payment_proof_path'] = $paymentProofPath;
            } else {
                $proofNote = '[BUKTI_BAYAR] ' . $paymentProofPath;
                $orderPayload['notes'] = $notes ? ($notes . PHP_EOL . $proofNote) : $proofNote;
            }

            $order = Order::create($orderPayload);

            // Create order items
            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            DB::commit();

            // Clear cart
            session()->forget('cart');

            $trackedOrders = $this->getTrackedOrderNumbers($request);
            $trackedOrders[] = $order->order_number;
            $trackedOrders = array_values(array_unique($trackedOrders));

            session()->put('tracked_orders', $trackedOrders);
            cookie()->queue(cookie('tracked_orders', implode(',', $trackedOrders), 60 * 24 * 30));

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pesanan berhasil dibuat.',
                    'order_number' => $order->order_number,
                    'track_url' => route('customer.track', ['orderNumber' => $order->order_number]),
                ]);
            }

            return redirect()
                ->route('customer.track', ['orderNumber' => $order->order_number])
                ->with('success', 'Pesanan berhasil dibuat. Admin akan memverifikasi pembayaran Anda.');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Midtrans callback handler
     */
    public function callback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            $order = Order::where('order_number', $request->order_id)->first();

            if ($order) {
                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                    $order->update([
                        'payment_status' => 'success',
                        'midtrans_transaction_id' => $request->transaction_id,
                        'midtrans_payment_type' => $request->payment_type,
                        'paid_at' => now(),
                        'status' => 'confirmed',
                        'confirmed_at' => now(),
                    ]);
                } elseif ($request->transaction_status == 'pending') {
                    $order->update([
                        'payment_status' => 'pending',
                        'midtrans_transaction_id' => $request->transaction_id,
                    ]);
                } elseif ($request->transaction_status == 'deny' || $request->transaction_status == 'expire' || $request->transaction_status == 'cancel') {
                    $previousStatus = $order->status;

                    DB::transaction(function () use ($order, $request, $previousStatus) {
                        $order->update([
                            'payment_status' => 'failed',
                            'midtrans_transaction_id' => $request->transaction_id,
                            'status' => 'cancelled',
                            'cancelled_at' => $order->cancelled_at ?? now(),
                        ]);

                        if ($previousStatus !== 'cancelled' && $previousStatus !== 'delivered') {
                            $this->restoreOrderStock($order->fresh()->load(['items.produkSiapJual.produkPaket.details.stockGudang', 'items.produkSiapJual.stockGudang']));
                        }
                    });
                }
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Show customer orders dashboard from tracked orders
     */
    public function index(Request $request)
    {
        $trackedOrders = $this->getTrackedOrderNumbers($request);

        $orders = Order::with('items')
            ->whereIn('order_number', $trackedOrders)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Lookup tracking by order number from input
     */
    public function trackLookup(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string|max:255',
        ]);

        return redirect()->route('customer.track', ['orderNumber' => $request->order_number]);
    }

    /**
     * Show order tracking page
     */
    public function track(Request $request, string $orderNumber)
    {
        $order = Order::with(['items.produkSiapJual'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $trackedOrders = $this->getTrackedOrderNumbers($request);
        $trackedOrders[] = $order->order_number;
        $trackedOrders = array_values(array_unique($trackedOrders));

        session()->put('tracked_orders', $trackedOrders);
        cookie()->queue(cookie('tracked_orders', implode(',', $trackedOrders), 60 * 24 * 30));

        return view('customer.orders.track', compact('order'));
    }

    /**
     * Tracking status data for popup flow
     */
    public function trackStatus(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        $statusLabel = match ($order->status) {
            'pending' => 'Menunggu verifikasi kasir...',
            'confirmed' => 'Pembayaran terverifikasi',
            'processing' => 'Pesanan sedang diproses',
            'shipped' => $this->isPickupOrder($order) ? 'Pesanan siap diambil' : 'Pesanan dalam perjalanan',
            'delivered' => 'Pesanan selesai',
            'cancelled' => 'Pesanan dibatalkan',
            default => 'Status diperbarui',
        };

        $steps = [
            [
                'title' => 'Pesanan Dibuat',
                'note' => 'Pesanan berhasil diterima',
                'done' => true,
                'active' => $order->status === 'pending',
            ],
            [
                'title' => 'Verifikasi Bayar',
                'note' => 'Kasir sedang memverifikasi pembayaran',
                'done' => in_array($order->status, ['confirmed', 'processing', 'shipped', 'delivered'], true) || $order->payment_status === 'success',
                'active' => in_array($order->status, ['pending', 'confirmed'], true),
            ],
            [
                'title' => 'Sedang Diproses',
                'note' => 'Pesanan sedang disiapkan',
                'done' => in_array($order->status, ['shipped', 'delivered'], true),
                'active' => $order->status === 'processing',
            ],
            [
                'title' => $this->isPickupOrder($order) ? 'Siap Diambil' : 'Dalam Perjalanan',
                'note' => $this->isPickupOrder($order) ? 'Silakan ambil pesananmu' : 'Pesanan sedang dalam perjalanan ke alamatmu',
                'done' => $order->status === 'delivered',
                'active' => $order->status === 'shipped',
            ],
            [
                'title' => 'Selesai',
                'note' => 'Selamat menikmati!',
                'done' => $order->status === 'delivered',
                'active' => false,
            ],
        ];

        return response()->json([
            'order_number' => $order->order_number,
            'status' => $order->status,
            'status_label' => $statusLabel,
            'tracking_note' => $order->tracking_note,
            'steps' => $steps,
        ]);
    }

    /**
     * Show order detail (backward-compatible)
     */
    public function show(string $orderNumber)
    {
        return $this->track(request(), $orderNumber);
    }

    /**
     * Show invoice
     */
    public function invoice(string $orderNumber)
    {
        $order = Order::with(['items.produkSiapJual'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('customer.orders.invoice', compact('order'));
    }

    /**
     * Admin order list
     */
    public function adminIndex(Request $request)
    {
        $query = Order::with('items')->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('orders.admin-index', compact('orders'));
    }

    /**
     * Admin order detail
     */
    public function adminShow(Order $order)
    {
        // Sinkronisasi ringan untuk data lama yang tidak konsisten.
        if (in_array($order->status, ['confirmed', 'processing', 'shipped', 'delivered'], true)
            && $order->payment_status !== 'success') {
            $order->update([
                'payment_status' => 'success',
                'paid_at' => $order->paid_at ?? now(),
            ]);
            $order->refresh();
        }

        $order->load(['items.produkSiapJual']);

        return view('orders.admin-show', compact('order'));
    }

    /**
     * Admin preview for payment proof image/file.
     */
    public function adminPaymentProof(Order $order)
    {
        $proofPath = $order->payment_proof_path;

        if (!$proofPath) {
            abort(404, 'Bukti pembayaran tidak ditemukan.');
        }

        if (!Storage::disk('public')->exists($proofPath)) {
            abort(404, 'File bukti pembayaran tidak ditemukan di storage.');
        }

        $absolutePath = Storage::disk('public')->path($proofPath);

        return response()->file($absolutePath);
    }

    /**
     * Admin verify payment proof
     */
    public function adminVerifyPayment(Order $order)
    {
        $order->update([
            'payment_status' => 'success',
            'paid_at' => $order->paid_at ?? now(),
            'status' => $order->status === 'pending' ? 'confirmed' : $order->status,
            'confirmed_at' => $order->confirmed_at ?? now(),
        ]);

        return redirect()->back()->with('success', 'Pembayaran berhasil diverifikasi. Tracking diperbarui.');
    }

    /**
     * Admin workflow button: advance to next stage.
     * Step 1 selalu verifikasi pembayaran.
     */
    public function adminAdvanceStage(Order $order)
    {
        if ($order->status === 'cancelled') {
            return redirect()->back()->with('error', 'Pesanan sudah dibatalkan.');
        }

        $message = 'Tidak ada tahap lanjutan.';

        DB::transaction(function () use (&$message, $order) {
            $order = $order->fresh()->load(['items.produkSiapJual.produkPaket', 'items.produkSiapJual.stockGudang']);
            $payload = [];

            // Tahap pertama: verifikasi pembayaran otomatis.
            if ($order->payment_status !== 'success') {
                $payload['payment_status'] = 'success';
                $payload['paid_at'] = $order->paid_at ?? now();
            }

            if ($order->status === 'pending') {
                $payload['status'] = 'confirmed';
                $payload['confirmed_at'] = $order->confirmed_at ?? now();
                $message = 'Pembayaran terverifikasi. Tracking masuk tahap Verifikasi Bayar.';
            } elseif ($order->status === 'confirmed') {
                $payload['status'] = 'processing';
                $message = 'Tracking dilanjutkan ke tahap Sedang Diproses.';
            } elseif ($order->status === 'processing') {
                $payload['status'] = 'shipped';
                $payload['shipped_at'] = $order->shipped_at ?? now();
                $message = 'Tracking dilanjutkan ke tahap Siap Diambil / Dalam Perjalanan.';
            } elseif ($order->status === 'shipped') {
                $payload['status'] = 'delivered';
                $payload['delivered_at'] = $order->delivered_at ?? now();
                $message = 'Tracking selesai.';
            } elseif ($order->status === 'delivered' && $order->payment_status !== 'success') {
                $message = 'Pembayaran disinkronkan menjadi success.';
            }

            if (!empty($payload)) {
                $order->update($payload);

                if (($payload['status'] ?? null) === 'delivered') {
                    $this->syncOrderToSales($order->fresh()->load(['items.produkSiapJual.produkPaket']));
                }
            }
        });

        return redirect()->back()->with('success', $message);
    }

    /**
     * Admin workflow button: cancel order.
     */
    public function adminCancel(Order $order)
    {
        if ($order->status === 'cancelled') {
            return redirect()->back()->with('info', 'Pesanan sudah dibatalkan sebelumnya.');
        }

        DB::transaction(function () use ($order) {
            $order = $order->fresh()->load(['items.produkSiapJual.produkPaket.details.stockGudang', 'items.produkSiapJual.stockGudang']);
            $previousStatus = $order->status;

            $payload = [
                'status' => 'cancelled',
                'cancelled_at' => $order->cancelled_at ?? now(),
            ];

            if ($order->payment_status !== 'success') {
                $payload['payment_status'] = 'failed';
            }

            $order->update($payload);

            if ($previousStatus !== 'cancelled' && $previousStatus !== 'delivered') {
                $this->restoreOrderStock($order);
            }
        });

        return redirect()->back()->with('success', 'Pesanan berhasil dibatalkan.');
    }

    /**
     * Admin update tracking status
     */
    public function adminUpdateTracking(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'tracking_note' => 'nullable|string|max:1000',
        ]);

        $payload = [
            'status' => $validated['status'],
            'tracking_note' => $validated['tracking_note'] ?? $order->tracking_note,
        ];

        if ($validated['status'] === 'confirmed' && !$order->confirmed_at) {
            $payload['confirmed_at'] = now();
        }

        if ($validated['status'] === 'shipped' && !$order->shipped_at) {
            $payload['shipped_at'] = now();
        }

        if ($validated['status'] === 'delivered' && !$order->delivered_at) {
            $payload['delivered_at'] = now();
        }

        if ($validated['status'] === 'cancelled' && !$order->cancelled_at) {
            $payload['cancelled_at'] = now();
        }

        $previousStatus = $order->status;

        DB::transaction(function () use ($order, $payload, $validated, $previousStatus) {
            $order->update($payload);

            if ($validated['status'] === 'cancelled' && $previousStatus !== 'cancelled' && $previousStatus !== 'delivered') {
                $this->restoreOrderStock($order->fresh()->load(['items.produkSiapJual.produkPaket.details.stockGudang', 'items.produkSiapJual.stockGudang']));
            }

            if ($validated['status'] === 'delivered') {
                $this->syncOrderToSales($order->fresh()->load(['items.produkSiapJual.produkPaket']));
            }
        });

        return redirect()->back()->with('success', 'Status tracking pesanan berhasil diperbarui.');
    }

    /**
     * Get tracked order numbers from session and cookie
     */
    private function getTrackedOrderNumbers(Request $request): array
    {
        $sessionOrders = session('tracked_orders', []);
        $cookieValue = $request->cookie('tracked_orders', '');
        $cookieOrders = $cookieValue ? array_filter(explode(',', $cookieValue)) : [];

        return array_values(array_unique(array_merge($sessionOrders, $cookieOrders)));
    }

    /**
     * Normalize cart data for views and AJAX responses.
     */
    private function buildCartSummary(): array
    {
        $cart = session()->get('cart', []);
        $cartItems = [];
        $items = [];
        $subtotal = 0;

        foreach ($cart as $id => $item) {
            $product = ProdukSiapJual::with(['stockGudang', 'produkPaket'])->find($id);
            if (!$product) {
                continue;
            }

            $availablePaket = (int) ($product->stok_siap_jual ?? 0);
            if ($availablePaket <= 0) {
                continue;
            }

            $quantity = (int) ($item['quantity'] ?? 1);
            if ($quantity > $availablePaket) {
                $quantity = $availablePaket;
            }

            $hargaPaket = (float) ($product->harga_jual ?? 0);
            $itemSubtotal = $hargaPaket * $quantity;

            $cartItems[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $itemSubtotal,
            ];

            $items[] = [
                'id' => $product->id,
                'name' => $product->nama_produk,
                'price' => (int) $hargaPaket,
                'quantity' => $quantity,
                'subtotal' => (int) $itemSubtotal,
            ];

            $subtotal += $itemSubtotal;
        }

        return [
            'cart_items' => $cartItems,
            'items' => $items,
            'subtotal' => (int) $subtotal,
            'count' => count($items),
        ];
    }

    /**
     * Determine pickup orders without relying on an optional database column.
     */
    private function isPickupOrder(Order $order): bool
    {
        if (isset($order->delivery_method)) {
            return $order->delivery_method === 'pickup';
        }

        return (float) $order->shipping_cost <= 0;
    }

    /**
     * Convert delivered orders into penjualan records (idempotent).
     */
    private function syncOrderToSales(Order $order): void
    {
        $items = $order->items ?? collect();

        if ($items->isEmpty()) {
            return;
        }

        $hasShippingAssigned = false;

        foreach ($items as $item) {
            $marker = '[AUTO-ORDER:' . $order->order_number . '][ITEM:' . $item->id . ']';

            $alreadySynced = Penjualan::where('keterangan', 'like', '%' . $marker . '%')->exists();
            if ($alreadySynced) {
                continue;
            }

            $produk = $item->produkSiapJual;
            $ownerUserId = $order->user_id ?? ($produk?->user_id);

            // If owner can't be resolved, skip to avoid invalid foreign key.
            if (!$ownerUserId) {
                continue;
            }

            $customerQuery = Customer::where('user_id', $ownerUserId);
            if (!empty($order->customer_phone)) {
                $customerQuery->where('telepon', $order->customer_phone);
            } else {
                $customerQuery->where('nama_customer', $order->customer_name);
            }

            $customer = $customerQuery->first();
            if (!$customer) {
                $customer = Customer::create([
                    'user_id' => $ownerUserId,
                    'nama_customer' => $order->customer_name,
                    'telepon' => $order->customer_phone,
                    'email' => $order->customer_email,
                    'alamat' => $order->shipping_address,
                ]);
            }

            $qtyPaket = (int) $item->quantity;
            $pcsPerPaket = max(1, (int) ($produk->pcs_per_paket ?? 1));
            $jumlahPcs = $qtyPaket * $pcsPerPaket;

            $hargaSatuan = (float) $item->price;
            $subtotal = (float) $item->subtotal;

            $ongkir = 0;
            if (!$hasShippingAssigned) {
                $ongkir = (float) $order->shipping_cost;
                $hasShippingAssigned = true;
            }

            if ($produk && $produk->isPaket() && $produk->produkPaket) {
                $hppPerPaket = (float) ($produk->produkPaket->hpp_total ?? 0);
                $hppTotal = $qtyPaket * $hppPerPaket;
            } else {
                $hppPerPcs = (float) ($produk->hpp_per_pcs ?? 0);
                $hppTotal = $jumlahPcs * $hppPerPcs;
            }

            $statusPembayaran = $order->payment_status === 'success' ? 'lunas' : 'utang';
            $keterangan = trim($marker . ' Auto sinkron dari tracking order ' . $order->order_number . '. ' . ($order->notes ?? ''));

            Penjualan::create([
                'user_id' => $ownerUserId,
                'customer_id' => $customer->id,
                'nama_customer_snapshot' => $order->customer_name,
                'produk_siap_jual_id' => $item->produk_siap_jual_id,
                'metode_pembayaran_id' => null,
                'jumlah_pcs' => $jumlahPcs,
                'qty_pcs' => $jumlahPcs,
                'harga_satuan' => $hargaSatuan,
                'total_penjualan' => $subtotal,
                'hpp_total' => $hppTotal,
                'ongkir' => $ongkir,
                'diskon' => 0,
                'tipe_diskon' => 'nominal',
                'promo' => 0,
                'pajak' => 0,
                'alamat_pengiriman' => $order->shipping_address,
                'metode_pengiriman' => $this->isPickupOrder($order) ? 'pickup' : 'delivery',
                'keterangan' => $keterangan,
                'bukti_pembayaran' => $order->payment_proof_path,
                'total_bayar' => $subtotal + $ongkir,
                'laba' => $subtotal - $hppTotal,
                'modal_terpakai' => $hppTotal,
                'keterangan_modal' => 'Auto dari order ' . $order->order_number,
                'status_pembayaran' => $statusPembayaran,
                'tanggal_penjualan' => optional($order->delivered_at)->toDateString() ?? now()->toDateString(),
            ]);
        }
    }

    /**
     * Restore stok ketika order dibatalkan.
     */
    private function restoreOrderStock(Order $order): void
    {
        $items = $order->items ?? collect();

        foreach ($items as $item) {
            $produk = $item->produkSiapJual;
            if (!$produk) {
                continue;
            }

            $qtyPaket = max(1, (int) ($item->quantity ?? 1));
            $produk->restoreStokPenjualan($qtyPaket);
        }
    }
}
