@extends('layouts/adminlte')
@section('content')

<div class="container-fluid">

    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Data Murid</h3>
            <a href="<?= base_url('guru/murid/create') ?>" class="btn btn-primary btn-sm">
                + Tambah Murid
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-2">

                    <table class="table table-bordered table-sm mb-0">
                        <thead>
                            <tr>
                                <th style="width:60px">No</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($murid)): ?>
                                <?php foreach ($murid as $i => $m): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= esc($m['nama_depan'].' '.$m['nama_belakang']) ?></td>
                                    <td><?= kelasBadge($m['kelas_id']) ?></td>
                                </tr>
                                <?php endforeach ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        Belum ada data murid
                                    </td>
                                </tr>
                            <?php endif ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

</div>

@endsection
