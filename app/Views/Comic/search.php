<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>

<div class="container">
    <div class="row">
        <div class="col">
            <form action="/Comics/slugify" method="post">
                <div class="input-group input-group-lg searchcss">
                    <?= csrf_field()  ?>
                    <input type="text" class="form-control <?= ($validation->hasError('judul')) ? 'is-invalid' : ' ' ?> " name="judul" aria-label="Sizing example input" placeholder="Masukan Judul Buku" aria-describedby="inputGroup-sizing-lg" autofocus>
                    <button class="btn btn-outline-success" type="submit">Cari</button>
                    <div class="invalid-feedback">
                        <?= $validation->getError('judul'); ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection('content'); ?>