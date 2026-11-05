<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class XenditController extends Controller
{
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = env(
            'XENDIT_SECRET_KEY',
            'ISI_DENGAN_SECRET_KEY_XENDIT_MU'
        );
    }

    public function prosesPembayaran(Request $request)
    {
        $transaksiId = $request->query('transaksi_id');

        $transaksi = DB::table('transaksis')
            ->where('id', $transaksiId)
            ->first();

        if (!$transaksi) {
            return redirect('/checkout')
                ->with('error', 'Transaksi tidak ditemukan.');
        }

        $payload = [
            'external_id' => 'DAILYSHOP-' . $transaksi->id . '-' . time(),
            'amount' => (int) $transaksi->total_harga,
            'description' => 'Pembayaran Pesanan #' . $transaksi->kode_transaksi,
            'success_redirect_url' => route('pesanan.index'),
            'failure_redirect_url' => url('/checkout'),
        ];

        $ch = curl_init('https://api.xendit.co/v2/invoices');

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($this->apiKey . ':')
            ]
        ]);

        $response = json_decode(curl_exec($ch), true);

        curl_close($ch);

        if (isset($response['invoice_url'])) {

            DB::table('transaksis')
                ->where('id', $transaksi->id)
                ->update([
                    'xendit_invoice_id' => $response['id'],
                    'xendit_invoice_url' => $response['invoice_url'],
                    'updated_at' => now()
                ]);

            return redirect()->away($response['invoice_url']);
        }

        return redirect('/checkout')
            ->with('error', 'Gagal membuat invoice Xendit.');
    }

    public function handleCallback(Request $request)
    {
        if (
            $request->header('x-callback-token')
            !== env('XENDIT_CALLBACK_TOKEN')
        ) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $data = $request->all();

        $externalId = $data['external_id'] ?? null;

        if (!$externalId) {
            return response()->json([
                'message' => 'External ID tidak ditemukan'
            ], 400);
        }

        $parts = explode('-', $externalId);

        $transaksiId = $parts[1] ?? null;

        if (
            $transaksiId &&
            in_array($data['status'], ['PAID', 'SETTLED'])
        ) {

            DB::transaction(function () use ($transaksiId) {

                DB::table('transaksis')
                    ->where('id', $transaksiId)
                    ->update([
                        'status' => 'proses',
                        'updated_at' => now()
                    ]);

            });

            return response()->json([
                'status' => 'success'
            ]);
        }

        return response()->json([
            'status' => 'ignored'
        ]);
    }
}