<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>
<div class="container">
    <div class="row">
        <div class="col">
            <h2 class="mt-2">Detail Komik</h2>
            <?php if (session()->getFlashData('gagal')) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= session()->getFlashData('gagal'); ?>
                </div>
            <?php endif; ?>
            <div class="card mb-3" style="max-width: 540px;">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="/img/<?= $komik['sampul']; ?>" alt="..." width="150">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title"><?= $komik['judul']; ?></h5>
                            <p class="card-text"><b><?= $komik['penulis']; ?></b></p>
                            <p class="card-text"><small class="text-muted">Created at: <?= $komik['created_at']; ?></small></p>
                            <a href="/Comics/edit/<?= $komik['slug']; ?>" class="btn btn-warning">Edit</a>
                            <form action="/Comics/<?= $komik['id']; ?>" method="post" class="d-inline">
                                <input type="hidden" name="_method" value="DELETE">
                                <?= csrf_field(); ?>
                                <button type="submit" class="btn btn-danger" onclick="return confirm('apakah anda yakin?')">Delete</button>
                            </form>
                            <br>
                            <a href="/Comics"> Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('content'); ?>