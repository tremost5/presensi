@extends('layouts/adminlte')
@section('content')

<style>
.guru-shell {
  max-width: 1100px;
  margin: 0 auto;
  display: grid;
  gap: 18px;
  padding: 4px 0 84px;
}
.guru-hero {
  border-radius: 18px;
  border: 1px solid #dbe4f0;
  background: linear-gradient(135deg, #0f766e 0%, #0ea5e9 100%);
  color: #fff;
  padding: 18px 20px;
  box-shadow: 0 16px 34px rgba(15, 118, 110, 0.28);
}
.guru-hero h3 {
  margin: 0;
  font-weight: 800;
  letter-spacing: -0.02em;
}
.guru-hero small {
  opacity: .92;
}
.kpi-grid {
  display: grid;
  gap: 12px;
  grid-template-columns: repeat(4, minmax(0, 1fr));
}
.kpi-card {
  border: 1px solid #dbe4f0;
  border-radius: 14px;
  padding: 14px;
  background: #fff;
}
.kpi-card .label {
  color: #64748b;
  font-size: .82rem;
}
.kpi-card .value {
  margin-top: 2px;
  font-size: 1.6rem;
  font-weight: 800;
  line-height: 1.1;
  color: #0f172a;
}
.panel-grid {
  display: grid;
  gap: 16px;
  grid-template-columns: 1.55fr 1fr;
}
.chart-card {
  border: 1px solid #dbe4f0;
  border-radius: 16px;
  background: #fff;
  padding: 14px;
}
.chart-card h6 {
  margin: 0;
  font-weight: 800;
}
.chart-sub {
  color: #64748b;
  font-size: .82rem;
}
.chart-wrap {
  height: 260px;
  margin-top: 8px;
}
.side-stack {
  display: grid;
  gap: 16px;
}
.list-item {
  padding: 10px 0;
  border-bottom: 1px dashed #e2e8f0;
}
.list-item:last-child {
  border-bottom: 0;
  padding-bottom: 0;
}
.materi-link {
  color: #0f172a;
  font-weight: 700;
  cursor: pointer;
}
.materi-link:hover {
  text-decoration: underline;
}
.materi-preview {
  width: 100%;
  border: 1px solid #dbe4f0;
  border-radius: 12px;
  overflow: hidden;
  background: #f8fafc;
  min-height: 180px;
  display: none;
  align-items: center;
  justify-content: center;
}
.materi-preview img {
  max-width: 100%;
  max-height: 52vh;
  object-fit: contain;
  display: block;
}
.materi-preview iframe {
  width: 100%;
  height: 52vh;
  border: 0;
  display: block;
  background: #fff;
}
.materi-preview-fallback {
  color: #64748b;
  font-size: 0.9rem;
  padding: 14px;
}
@media (max-width: 991.98px) {
  .kpi-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  .panel-grid {
    grid-template-columns: 1fr;
  }
}
</style>

<section class="content">
  <div class="guru-shell">

    <div class="guru-hero">
      <h3>Dashboard Guru</h3>
      <small>
        Halo <?= esc(trim(($guru['nama_depan'] ?? '') . ' ' . ($guru['nama_belakang'] ?? ''))) ?>.
        Login terakhir:
        <?= !empty($guru['last_login']) ? date('d M Y H:i', strtotime($guru['last_login'])) : '-' ?>
      </small>
    </div>

    <div class="kpi-grid">
      <div class="kpi-card">
        <div class="label">Hadir Hari Ini</div>
        <div class="value"><?= (int) ($todayCount ?? 0) ?></div>
      </div>
      <div class="kpi-card">
        <div class="label">Total Minggu Ini</div>
        <div class="value"><?= (int) ($weeklyTotal ?? 0) ?></div>
      </div>
      <div class="kpi-card">
        <div class="label">Total Bulan Ini</div>
        <div class="value"><?= (int) ($monthlyTotal ?? 0) ?></div>
      </div>
      <div class="kpi-card">
        <div class="label">Rata-rata / Hari</div>
        <div class="value"><?= esc((string) ($avgWeekly ?? 0)) ?></div>
      </div>
    </div>

    <div class="panel-grid">
      <div class="chart-card">
        <h6>Grafik Kehadiran Minggu Ini</h6>
        <div class="chart-sub">Total murid status hadir per hari (7 hari terakhir)</div>
        <div class="chart-wrap"><canvas id="chartWeekly"></canvas></div>
      </div>

      <div class="side-stack">
        <div class="chart-card">
          <h6>Ulang Tahun Murid (H-3 s/d H+3)</h6>
          <?php if (empty($ultah)): ?>
            <div class="chart-sub mt-2">Tidak ada ulang tahun terdekat.</div>
          <?php else: ?>
            <?php foreach ($ultah as $u): ?>
              <div class="list-item">
                <strong><?= esc($u['nama_depan'].' '.$u['nama_belakang']) ?></strong>
                <div class="chart-sub">Kelas <?= esc($u['nama_kelas'] ?? '-') ?></div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <div class="chart-card">
          <h6>Materi Terbaru</h6>
          <?php if (empty($materi)): ?>
            <div class="chart-sub mt-2">Belum ada materi.</div>
          <?php else: ?>
            <?php foreach ($materi as $m): ?>
              <div class="list-item">
                <span class="materi-link" data-id="<?= (int) $m['id'] ?>">
                  <?= esc($m['judul']) ?>
                </span>
                <div class="chart-sub">
                  Kelas <?= esc($m['nama_kelas'] ?? '-') ?> •
                  <?= date('d M Y', strtotime($m['created_at'])) ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="chart-card">
      <h6>Grafik Kehadiran Bulan Ini</h6>
      <div class="chart-sub">Distribusi jumlah hadir harian dari tanggal 1 sampai hari ini</div>
      <div class="chart-wrap"><canvas id="chartMonthly"></canvas></div>
    </div>

  </div>
</section>

<div class="modal fade" id="materiModal">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="mJudul"></h5>
        <button class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p id="mCatatan" class="text-muted mb-0"></p>
        <div id="materiPreview" class="materi-preview mt-3"></div>
      </div>
      <div class="modal-footer">
        <a id="btnDownload" class="btn btn-success btn-block" download>Download Materi</a>
        <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const weeklyLabels = @json($weeklyLabels ?? []);
  const weeklyData = @json($weeklyData ?? []);
  const monthlyLabels = @json($monthlyLabels ?? []);
  const monthlyData = @json($monthlyData ?? []);

  const commonGrid = {
    color: 'rgba(148,163,184,.25)',
    drawTicks: false
  };

  new Chart(document.getElementById('chartWeekly'), {
    type: 'line',
    data: {
      labels: weeklyLabels,
      datasets: [{
        label: 'Hadir',
        data: weeklyData,
        tension: 0.35,
        borderWidth: 3,
        borderColor: '#0f766e',
        pointRadius: 4,
        pointHoverRadius: 6,
        pointBackgroundColor: '#0f766e',
        fill: true,
        backgroundColor: 'rgba(15,118,110,.14)'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { display: false }, ticks: { color: '#64748b' } },
        y: { beginAtZero: true, ticks: { precision: 0, color: '#64748b' }, grid: commonGrid }
      }
    }
  });

  new Chart(document.getElementById('chartMonthly'), {
    type: 'bar',
    data: {
      labels: monthlyLabels,
      datasets: [{
        label: 'Hadir',
        data: monthlyData,
        borderRadius: 8,
        backgroundColor: 'rgba(14,165,233,.78)',
        borderColor: '#0284c7',
        borderWidth: 1.2
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { ticks: { color: '#64748b', maxRotation: 0, autoSkip: true, maxTicksLimit: 16 }, grid: { display: false } },
        y: { beginAtZero: true, ticks: { precision: 0, color: '#64748b' }, grid: commonGrid }
      }
    }
  });

  const $materiModal = window.jQuery ? window.jQuery('#materiModal') : null;
  if ($materiModal && $materiModal.length) {
    $materiModal.appendTo('body');

    $materiModal.on('show.bs.modal', () => {
      document.querySelectorAll('.sidebar-overlay').forEach((el) => el.remove());
    });

    $materiModal.on('hidden.bs.modal', () => {
      document.body.classList.remove('modal-open');
      document.body.style.removeProperty('padding-right');
      document.querySelectorAll('.modal-backdrop').forEach((el) => el.remove());
    });
  }

  document.querySelectorAll('.materi-link').forEach((el) => {
    el.addEventListener('click', () => {
      const id = el.dataset.id;
      fetch(`<?= base_url('guru/materi/ajax') ?>/${id}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then((res) => {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
      })
      .then((res) => {
        if (res.error) {
          alert(res.message || 'Gagal memuat materi');
          return;
        }

        const d = res.data;
        document.getElementById('mJudul').innerText = d.judul;
        document.getElementById('mCatatan').innerText = d.catatan || '-';

        const btn = document.getElementById('btnDownload');
        const preview = document.getElementById('materiPreview');
        const ext = (d.file_ext || '').toLowerCase();
        const url = d.file_url || '';

        preview.innerHTML = '';
        preview.style.display = 'none';

        const imageExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        const isImage = imageExt.includes(ext);
        const isPdf = ext === 'pdf';

        if (d.file) {
          btn.href = `<?= base_url('guru/materi/download') ?>/${d.id}`;
          btn.style.display = 'block';

          if (url && isImage) {
            preview.innerHTML = `<img src=\"${url}\" alt=\"Preview materi\">`;
            preview.style.display = 'flex';
          } else if (url && isPdf) {
            preview.innerHTML = `<iframe src=\"${url}\" title=\"Preview PDF materi\"></iframe>`;
            preview.style.display = 'block';
          } else {
            preview.innerHTML = '<div class="materi-preview-fallback">Preview tidak tersedia untuk tipe file ini. Silakan gunakan tombol download.</div>';
            preview.style.display = 'flex';
          }
        } else {
          btn.style.display = 'none';
          preview.innerHTML = '<div class="materi-preview-fallback">Materi ini tidak memiliki lampiran file.</div>';
          preview.style.display = 'flex';
        }

        if ($materiModal && $materiModal.length) {
          $materiModal.modal({
            backdrop: true,
            keyboard: true,
            focus: true,
            show: true
          });
        }
      })
      .catch(() => {
        alert('Gagal memuat materi');
      });
    });
  });
});
</script>

@endsection
