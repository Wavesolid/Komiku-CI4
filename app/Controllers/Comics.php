<?php

namespace App\Controllers;

use App\Models\AccountModel;
use App\Models\idModel;
use App\Models\KomikModel;
use CodeIgniter\CodeIgniter;
use CodeIgniter\HTTP\Request;
use CodeIgniter\Session\Session;

class Comics extends BaseController
{
    // Kelas constructor
    protected $komikmodel;
    protected $idModel;
    protected $AccountModel;

    public function __construct()
    {
        $this->komikmodel = new KomikModel();
        $this->idModel = new idModel();
        $this->AccountModel = new AccountModel();
    }

    public function infoAccount() {
        
        if (logged_in()) {
            $user = user()->__get('email');
            $info = $this->AccountModel->getAccount($user);
            $name =  $info['username'];    
        }else {
            $name = 'kamu';
        }
        return $name;
    }

    public function index()
    {
        $user = user()->__get('email');
        $info = $this->AccountModel->getAccount($user);
        $detail = $this->komikmodel->getSpecificKomik($info);
        $data = [
            'title' => 'komik',
            'komik' => $detail,
            'nama' => $this->infoAccount(),
            'routes' => uri_string(true),
        ];
        return view('Comic/index', $data);
    }

    public function detail($slug)
    {
        $data = [
            'title' => 'Detail Komik',
            'komik' => $this->komikmodel->getKomik($slug),
            'nama' => $this->infoAccount(),
            'routes' => uri_string(true)
        ];
        if (empty($data['komik'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Judul komik ' . $slug . ' tidak ditemukan.');
        }
        return view('Comic/detail', $data);
    }

    public function create()
    {   
        $data = [
            'title' => 'Form Tambah Data',
            'validation' => \Config\Services::validation(),
            'nama' => $this->infoAccount(),
            'routes' => uri_string(true)
        ];
        return view('Comic/create', $data);
    }

    public function save()
    {
        if (!$this->validate([
            'judul' => [
                'rules' => 'required|is_unique[komik.judul]',
                'errors' => [
                    'required' => 'nama {field} harus diisi',
                    'is_unique' => 'nama {field} sudah ada'
                ]
            ],
            'sampul' => [
                'rules' => 'uploaded[sampul]|max_size[sampul,5024]|is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png]'
            ]
        ])) {
            return redirect()->to('/comics/create')->withInput();
        }
        
        $fileSampul = $this->request->getFile('sampul');
        $fileSampul->move('img');
        $namaSampul = $fileSampul->getName();
        $user = user()->__get('email');
        $slug = url_title($this->request->getVar('judul'), '-', true);
        
        $this->komikmodel->save([
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'created_by' => $user,
            'sampul' => $namaSampul
        ]);

        session()->setFlashData('berhasil', 'Data berhasil ditambahkan!');
        return redirect()->to('Comics');
    }

    public function delete($id)
    {
        $komik = $this->idModel->getId($id);
        $user = user()->__get('email');
        dd($komik);
        if ($komik['created_by'] == $user) {
            unlink('img/' . $komik['sampul']);
            $this->komikmodel->delete($id);
            session()->setFlashData('berhasil', 'Data berhasil dihapus!');
            return redirect()->to('Comics');
        } else {
            session()->setFlashData('gagal', 'Kamu tidak bisa menghapus data berikut!');
            return redirect()->to('Comics/' . $komik['slug']);
        }
    }

    public function edit($slug)
    {
        $komik = $this->komikmodel->getKomik($slug);
        $user = user()->__get('email');
        
        $data = [
            'title' => 'Form Edit Data',
            'validation' => \Config\Services::validation(),
            'komik' => $komik,
            'nama' => $this->infoAccount(),
            'routes' => uri_string(true)
        ];
        if($komik['created_by'] != $user) {
            session()->setFlashData('gagal', 'Kamu tidak bisa merubah data berikut!');
            return redirect()->to('Comics/' . $komik['slug']);
        }
        return view('/Comic/edit', $data);
    }

    public function update($id)
    {
        $komikLama = $this->komikmodel->getKomik($this->request->getVar('slug'));
        
        if ($komikLama['judul'] != $this->komikmodel->getKomik($this->request->getVar('judul'))) {
            $rule_judul = 'required';
        } else {
            $rule_judul = 'required|is_unique[komik.judul]';
        }
        $fileSampul = $this->request->getFile('sampul');

        if (!$this->validate([
            'judul' => [
                'rules' => $rule_judul,
                'errors' => [
                    'required' => 'nama {field} harus diisi',
                    'is_unique' => 'nama {field} sudah ada'
                ]
            ],
            'sampul' => [
                'rules' => 'max_size[sampul,5024]|is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png]',
                
            ]
        ])) {
            return redirect()->to('/Comics/edit/' . $this->request->getVar('slug'))->withInput();
        }

        if($fileSampul->getError() == 4) {
            $namaSampul = $this->request->getVar('sampulLama');
        }else {
            $namaSampul = $fileSampul->getRandomName();
            $fileSampul->move('img', $namaSampul );
            unlink('img/' . $this->request->getVar('sampulLama'));
        }
        $slug = url_title($this->request->getVar('judul'), '-', true);
        $user = user()->__get('email');

        if ($komikLama['created_by'] == $user) {
            $this->komikmodel->save([
                'id' => $id,
                'judul' => $this->request->getVar('judul'),
                'slug' => $slug,
                'penulis' => $this->request->getVar('penulis'),
                'penerbit' => $this->request->getVar('penerbit'),
                'sampul' => $namaSampul
            ]);
            session()->setFlashData('berhasil', 'Data berhasil diubah!');
            return redirect()->to('Comics');
        } else {
            session()->setFlashData('gagal', 'Data gagal diubah!');
            return redirect()->to('Comics/edit/' . $komikLama['slug']);
        }
    }

    public function search() {
        $data = [
            'title' => 'Cari Data',
            'validation' => \Config\Services::validation(),
            'routes' => uri_string(true),
            'nama' => $this->infoAccount()
        ];
        
        return view ('/comic/search', $data);
    }

    public function slugify() {
        
        if (!$this->validate([
            'judul' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'nama {field} harus diisi',
                ]
            ]
        ])) {
            return redirect()->to('/comics/search')->withInput();
        }
        $slug = url_title($this->request->getVar('judul'), '-', true);
        return redirect()->to('Comics/' . $slug);
    }
}
