<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthForgot extends BaseController
{
    public function index()
    {
        return view('auth/forgot_choice');
    }

    // =========================
    // RESET VIA EMAIL
    // =========================
    public function email()
    {
        $email = $this->request->getPost('email');

        if (!$email) {
            return redirect()->back()->with('error', 'Email wajib diisi.');
        }

        $model = new UserModel();
        $user  = $model->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()
                ->with('error', 'Email tidak terdaftar.');
        }

        // ðŸ‘‰ LOGIC KIRIM EMAIL PUNYAMU TETAP DI SINI

        return redirect()->back()
            ->with('success', 'Link reset password sudah dikirim ke email Anda.');
    }

    // =========================
    // RESET VIA WHATSAPP (OTP)
    // =========================
    public function wa()
    {
        helper('wa');

        try {
            $input = $this->request->getPost('no_hp');
            if (!is_string($input) || trim($input) === '') {
                return redirect()->back()
                    ->with('error', 'Nomor WhatsApp wajib diisi.');
            }

            $no_hp = formatWA($input);

            if (!$no_hp) {
                return redirect()->back()
                    ->with('error', 'Format nomor WhatsApp tidak valid.');
            }

            $model = new \App\Models\UserModel();
            $user  = $model->where('no_hp', $no_hp)->first();

            if (!$user) {
                return redirect()->back()
                    ->with('error', 'Nomor WhatsApp tidak terdaftar.');
            }

            // OTP
            $otp = rand(100000, 999999);

            $updated = $model->update($user['id'], [
                'reset_token'   => $otp,
                'reset_expires' => date('Y-m-d H:i:s', strtotime('+5 minutes')),
            ]);

            if (!$updated) {
                log_message('error', 'Gagal update reset_token untuk user_id: ' . $user['id']);
                return redirect()->back()
                    ->with('error', 'Terjadi kesalahan saat memproses permintaan. Coba lagi.');
            }

            $pesan =
                "🔐 *Reset Password*\n\n"
              . "Kode OTP Anda:\n"
              . "*{$otp}*\n\n"
              . "Berlaku 5 menit.\n"
              . "Jangan bagikan kode ini.";

            $sent = kirimWA($no_hp, $pesan);

            if (!$sent) {
                log_message('error', 'Gagal kirim OTP WA ke: ' . $no_hp);
                return redirect()->back()
                    ->with('error', 'Gagal mengirim OTP WhatsApp. Pastikan WA terhubung dan coba lagi.');
            }

            session()->set('reset_user', $user['id']);

            return redirect()->to('/verify-otp')
                ->with('success', 'Kode OTP telah dikirim ke WhatsApp Anda.');
        } catch (\Throwable $e) {
            log_message('error', 'Lupa password WA error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan pada sistem. Silakan coba lagi.');
        }
    }


}

