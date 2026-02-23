<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthRegister extends BaseController
{
    public function form()
    {
        return view('auth/register_guru');
    }

    public function store()
    {
        helper(['form', 'face', 'wa']);

        // ===== NORMALISASI WA =====
        $noHp = formatWA($this->request->getPost('no_hp'));
        $tglLahir = $this->normalizeTanggalLahir((string) $this->request->getPost('tanggal_lahir'));

        if (!$noHp) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Nomor WhatsApp tidak valid');
        }

        if (!$tglLahir) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['tanggal_lahir' => 'Format tanggal lahir tidak valid. Gunakan DD-MM-YYYY, YYYY-MM-DD, atau DDMMYYYY.']);
        }

        // inject ulang ke POST
        $this->request->setGlobal('post', array_merge(
            $this->request->getPost(),
            [
                'no_hp' => $noHp,
                'tanggal_lahir' => $tglLahir,
            ]
        ));

        // ===== VALIDASI =====
        $rules = [
            'nama_depan'    => 'required',
            'nama_belakang' => 'required',
            'username'      => 'required|is_unique[users.username]',
            'email'         => 'required|valid_email|is_unique[users.email]',
            'no_hp'         => 'required|min_length[10]|max_length[15]|is_unique[users.no_hp]',
            'password'      => 'required|min_length[6]',
            'alamat'        => 'required',
            'tanggal_lahir' => 'required|valid_date[Y-m-d]',
        ];

        $messages = [
            'username' => [
                'is_unique' => 'Username sudah terdaftar, silakan gunakan username lain.'
            ],
            'email' => [
                'is_unique'    => 'Email sudah terdaftar, silakan gunakan email lain.',
                'valid_email'  => 'Format email tidak valid.'
            ],
            'no_hp' => [
                'is_unique' => 'Nomor WhatsApp sudah terdaftar.',
                'min_length' => 'Nomor WhatsApp terlalu pendek.',
                'max_length' => 'Nomor WhatsApp terlalu panjang.'
            ],
            'password' => [
                'min_length' => 'Password minimal 6 karakter.'
            ],
            'nama_depan' => [
                'required' => 'Nama depan wajib diisi.'
            ],
            'nama_belakang' => [
                'required' => 'Nama belakang wajib diisi.'
            ],
            'alamat' => [
                'required' => 'Alamat wajib diisi.'
            ],
            'tanggal_lahir' => [
                'required' => 'Tanggal lahir wajib diisi.',
                'valid_date' => 'Format tanggal lahir tidak valid.'
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // ===== UPLOAD FOTO =====
        $foto = $this->request->getFile('foto');
        $namaFoto = 'default.png';

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $folder = FCPATH . 'uploads/guru/';
            if (!is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            $namaFoto = $foto->getRandomName();
            $foto->move($folder, $namaFoto);
            cropWajah($folder . $namaFoto);
        }

        // ===== INSERT DB =====
        $model = new UserModel();
        $model->insert([
            'role_id'       => 3,
            'nama_depan'    => $this->request->getPost('nama_depan'),
            'nama_belakang' => $this->request->getPost('nama_belakang'),
            'username'      => $this->request->getPost('username'),
            'email'         => $this->request->getPost('email'),
            'no_hp'         => $noHp,
            'password'      => password_hash(
                $this->request->getPost('password'),
                PASSWORD_DEFAULT
            ),
            'alamat'        => $this->request->getPost('alamat'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'foto'          => $namaFoto,
            'status'        => 'nonaktif'
        ]);

        // ================================
// WA KE ADMIN
// ================================
$waAdmin = kirimWA(
    '6281232944843',
    "📢 *PENDAFTARAN GURU BARU*\n\n"
    . "Nama: {$this->request->getPost('nama_depan')} {$this->request->getPost('nama_belakang')}\n"
    . "Username: {$this->request->getPost('username')}\n"
    . "No WA: {$noHp}\n\n"
    . "Status: MENUNGGU AKTIVASI"
);

if (!$waAdmin) {
    log_message('error', 'WA ADMIN GAGAL TERKIRIM');
}

// ================================
// WA KE PENDAFTAR
// ================================
$waUser = kirimWA(
    $noHp,
    "Shalom 🙏\n\n"
    . "Terima kasih telah mendaftar sebagai guru.\n\n"
    . "📌 Status akun Anda saat ini:\n"
    . "*MENUNGGU AKTIVASI ADMIN*\n\n"
    . "Kami akan menghubungi Anda kembali setelah akun diaktifkan.\n\n"
    . "Tuhan Yesus memberkati ✨"
);

if (!$waUser) {
    log_message('error', 'WA PENDAFTAR GAGAL TERKIRIM: '.$noHp);
}

// ================================
// REDIRECT AKHIR
// ================================
return redirect()->to('/register-pending');
}
    // ===== AJAX VALIDASI REALTIME =====
    public function checkUser()
    {
        $model = new UserModel();

        $field = $this->request->getPost('field');
        $value = trim($this->request->getPost('value'));

        if (!$field || !$value) {
            return $this->response->setJSON(['valid' => true]);
        }

        if (!in_array($field, ['email', 'username', 'no_hp'])) {
            return $this->response->setJSON(['valid' => true]);
        }

        if ($field === 'no_hp') {
            helper('wa');
            $value = formatWA($value);
        }

        $exists = $model->where($field, $value)->first();

        if ($exists) {
            return $this->response->setJSON([
                'valid'   => false,
                'message' => match ($field) {
                    'email'    => 'Email sudah terdaftar',
                    'username' => 'Username sudah digunakan',
                    'no_hp'    => 'Nomor WhatsApp sudah terdaftar',
                }
            ]);
        }

        return $this->response->setJSON(['valid' => true]);
    }

    public function pending()
    {
        return view('auth/register_pending');
    }

    private function normalizeTanggalLahir(string $input): ?string
    {
        $value = trim($input);
        if ($value === '') {
            return null;
        }

        if (preg_match('/^\d{8}$/', $value) === 1) {
            $value = substr($value, 0, 2) . '-' . substr($value, 2, 2) . '-' . substr($value, 4, 4);
        }

        $value = str_replace(['.', '/'], '-', $value);

        $formats = ['Y-m-d', 'd-m-Y'];
        foreach ($formats as $format) {
            $dt = \DateTime::createFromFormat($format, $value);
            if ($dt instanceof \DateTime && $dt->format($format) === $value) {
                $year = (int) $dt->format('Y');
                if ($year < 1940 || $year > (int) date('Y')) {
                    return null;
                }
                return $dt->format('Y-m-d');
            }
        }

        return null;
    }
}
