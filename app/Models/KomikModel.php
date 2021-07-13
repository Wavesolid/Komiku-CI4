<?php

namespace App\Models;

use CodeIgniter\Model;

class KomikModel extends Model
{
    protected $table = 'komik';
    protected $useTimestamps = true;
    protected $allowedFields = ['judul', 'slug', 'penulis', 'penerbit', 'sampul', 'created_by'];

    public function getKomik($slug = false)
    {
        if ($slug == false) {
            return $this->findAll();
        }
        return $this->where(['slug' => $slug])->first();
    }

    public function getSpecificKomik($account = []) {
        $created = $account['email'];

        if($created == false) {
            return $this->findAll();
        }
        return $this->where(['created_by' => $created])->findAll();
    }
}
