<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBaseEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function message(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'persona' => ['required', 'string', 'in:nara'],
            'topic' => ['nullable', 'string', 'in:produk,checkout,tracking,pembayaran'],
            'is_first_message' => ['nullable', 'boolean'],
        ]);

        $message = trim($validated['message']);
        $persona = $validated['persona'];
        $topic = $validated['topic'] ?? 'produk';
        $isFirstMessage = (bool) ($validated['is_first_message'] ?? false);

        $knowledgeContext = $this->buildKnowledgeContext($message, $topic);
        $systemPrompt = $this->buildSystemPrompt($persona, $topic, $knowledgeContext, $isFirstMessage);

        $apiKey = trim((string) config('services.chatbot_ai.api_key'));
        if ($apiKey === '') {
            return response()->json([
                'answer' => $this->fallbackAnswer($persona, $topic, $message, $isFirstMessage),
                'source' => 'config',
            ]);
        }

        try {
            $response = Http::timeout((int) config('services.chatbot_ai.timeout', 25))
                ->withToken($apiKey)
                ->post(rtrim((string) config('services.chatbot_ai.base_url'), '/') . '/chat/completions', [
                    'model' => (string) config('services.chatbot_ai.model'),
                    'temperature' => 0.4,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $systemPrompt,
                        ],
                        [
                            'role' => 'user',
                            'content' => $message,
                        ],
                    ],
                ]);

            if (! $response->successful()) {
                return response()->json([
                    'answer' => $this->fallbackAnswer($persona, $topic, $message, $isFirstMessage),
                    'source' => 'fallback',
                ]);
            }

            $answer = data_get($response->json(), 'choices.0.message.content');
            if (! is_string($answer) || trim($answer) === '') {
                $answer = $this->fallbackAnswer($persona, $topic, $message, $isFirstMessage);
            }

            return response()->json([
                'answer' => trim($answer),
                'source' => 'ai',
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'answer' => $this->fallbackAnswer($persona, $topic, $message, $isFirstMessage),
                'source' => 'fallback',
            ]);
        }
    }

    private function buildSystemPrompt(string $persona, string $topic, string $knowledgeContext, bool $isFirstMessage = false): string
    {
        $personaStyles = [
            'nara' => 'Kamu Nara, CS yang hangat & empatik. Pakai bahasa santai, sesama manusia gitu. Kasih solusi yang praktis, jangan terlalu formal. Pede, senyum lewat chat.',
            'bayu' => 'Kamu Bayu, CS yang langsung to the point. Jawab singkat, padat, jelas. No nonsense, tapi tetap friendly. Kaya yang sudah biasa sama customer.',
            'ryan' => 'Kamu Ryan, CS yang chill & santai. Bahasa ring-gen, butuh disambung pake jokes kecil? Boleh aja. Tapi tetap helpful & ga asal jawab.',
            'vian' => 'Kamu Vian, CS yang rapi & sistematis. Penjelasan step-by-step, mudah diikuti. Detail tapi ga berbelit-belit, cukup paham aja yang perlu dipaham.',
        ];

        $style = $personaStyles[$persona] ?? $personaStyles['nara'];

        return implode("\n", [
            $style,
            'Membantu customer di toko Laryn milik user.',
            'Jangan pernah bilang toko ini milik kamu. Selalu anggap toko ini milik user/admin.',
            'Topik: ' . $topic . '.',
            'Pake info dari knowledge context di bawah, jangan asal ngomong.',
            'Kalo ga tau, bilang jujur & saranin step selanjutnya.',
            'Maks 6 kalimat, kecuali user minta penjelasan lebih detail.',
            'Bahasa natural, santai, bukan robot.',
            'Balasan pertama saja boleh memperkenalkan diri. Setelah itu, jangan perkenalkan diri lagi kecuali user minta.',
            'Kalau ini balasan pertama, buka dengan salam singkat dan panggil user dengan "sobat Laryn".',
            'Kalau knowledge context kosong atau tidak relevan, jawab tetap natural menggunakan pengetahuan umum yang aman dan tidak mengarang detail spesifik.',
            $isFirstMessage ? 'Ini balasan pertama percakapan ini.' : 'Ini bukan balasan pertama.',
            '',
            'Knowledge context:',
            $knowledgeContext,
        ]);
    }

    private function buildKnowledgeContext(string $message, string $topic): string
    {
        $defaultKnowledge = [
            'produk' => [
                'Customer bisa melihat produk di halaman Explore Produk.',
                'Produk dapat langsung dimasukkan ke keranjang dari halaman guest.',
                'Harga dan ketersediaan bisa berubah sesuai pembaruan admin.',
            ],
            'checkout' => [
                'Checkout dilakukan melalui popup keranjang dan form data customer.',
                'Customer perlu mengisi data nama, telepon, serta metode pengiriman.',
                'Alamat wajib diisi jika memilih pengiriman.',
            ],
            'tracking' => [
                'Tracking pesanan tersedia berdasarkan nomor order.',
                'Status pesanan diperbarui berkala dan bisa menampilkan catatan admin.',
                'Customer bisa cek daftar tracking dari halaman pesanan customer.',
            ],
            'pembayaran' => [
                'Pembayaran transfer memerlukan upload bukti pembayaran.',
                'Setelah upload bukti, customer melanjutkan pengisian data untuk kirim pesanan.',
                'Verifikasi pembayaran dilakukan oleh admin/kasir sebelum status lanjut.',
            ],
        ];

        $entries = KnowledgeBaseEntry::query()
            ->active()
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        $relevantEntries = $entries->filter(function (KnowledgeBaseEntry $entry) use ($message, $topic) {
            $entryTopic = $entry->topik ?: 'umum';
            if ($entryTopic !== 'umum' && $entryTopic !== $topic) {
                return false;
            }

            $messageText = mb_strtolower(trim(preg_replace('/[^\pL\pN\s]+/u', ' ', $message) ?? $message));
            $messageWords = array_filter(array_unique(preg_split('/\s+/u', $messageText) ?: []), function (string $word) {
                return mb_strlen($word) >= 3;
            });

            $entryText = mb_strtolower(implode(' ', array_filter([
                $message,
                $entry->judul,
                $entry->pertanyaan,
                $entry->kata_kunci,
                $entry->instruksi_ai,
            ])));

            foreach ($messageWords as $word) {
                if (str_contains($entryText, $word)) {
                    return true;
                }
            }

            return $entryTopic === 'umum' && $messageWords === [];
        })->take(5);

        if ($relevantEntries->isNotEmpty()) {
            $lines = [];

            foreach ($relevantEntries as $entry) {
                $lines[] = 'Judul: ' . $entry->judul;
                $lines[] = 'Topik: ' . $entry->topik;
                $lines[] = 'Pertanyaan: ' . $entry->pertanyaan;
                $lines[] = 'Jawaban: ' . $entry->jawaban;

                if ($entry->instruksi_ai) {
                    $lines[] = 'Instruksi AI: ' . $entry->instruksi_ai;
                }

                $lines[] = '';
            }

            return trim(implode("\n", $lines));
        }

        $lines = $defaultKnowledge[$topic] ?? $defaultKnowledge['produk'];

        $lower = mb_strtolower($message);
        if (str_contains($lower, 'ongkir') || str_contains($lower, 'kirim')) {
            $lines[] = 'Biaya kirim dapat bergantung pada metode pengiriman dan lokasi customer.';
        }

        if (str_contains($lower, 'bayar') || str_contains($lower, 'transfer')) {
            $lines[] = 'Jika customer belum upload bukti, arahkan untuk upload bukti pembayaran terlebih dahulu.';
        }

        return '- ' . implode("\n- ", $lines);
    }

    private function fallbackAnswer(string $persona, string $topic, string $message, bool $isFirstMessage = false): string
    {
        $prefixByPersona = [
            'nara' => 'Halo sobat Laryn, aku Nara. ',
            'bayu' => 'Halo sobat Laryn, aku Bayu. ',
            'ryan' => 'Halo sobat Laryn, aku Ryan. ',
            'vian' => 'Halo sobat Laryn, aku Vian. ',
        ];

        $topicAnswers = [
            'produk' => 'Cek dulu di menu Explore Produk, lihat-lihat apa aja yang enak, terus tambahin ke keranjang.',
            'checkout' => 'Buka keranjang, terus klik Lanjut Checkout, isi data & pilih cara pengiriman. Selesai!',
            'tracking' => 'Tinggal masukin nomor order di fitur track, terus bisa liat progress pesanan kamu.',
            'pembayaran' => 'Transfer aja dulu, terus upload buktinya. Admin bakal verifikasi & pesanan langsung diproses.',
        ];

        $prefix = $isFirstMessage ? ($prefixByPersona[$persona] ?? $prefixByPersona['nara']) : '';
        $core = $topicAnswers[$topic] ?? $topicAnswers['produk'];

        return trim($prefix . $core . ' Ada yang lain yang perlu dibantu?');
    }

    public function profiles(): JsonResponse
    {
        $profiles = [
            'nara' => [
                'id' => 'nara',
                'name' => 'Nara',
                'title' => 'Customer Service',
                'description' => 'Hangat, empatik, dan siap membantu Anda.',
                'avatar' => 'https://i.pravatar.cc/120?img=47',
            ],
        ];

        return response()->json(['profiles' => $profiles]);
    }
}
