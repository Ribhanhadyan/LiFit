<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Absensi extends RestController
{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
        $this->db_kedua = $this->load->database('db2', true);
        $this->load->helper('url');
        $this->load->model('model');
    }

    public function users_get()
    {
        $id = $this->get('id');
        $absensi       = $this->model->getAll($id);
        if ($absensi) {
            $this->response([
                'metaData' => [
                    'code' => '200',
                    'message' => 'Absensi di temukan!'
                ],
                'absensi' => $absensi
            ], 200); // OK (200) being the HTTP response code
        } else {
            // Set the response and exit
            $this->response([
                'metaData' => [
                    'code' => "201",
                    'message' => 'Absensi tidak ditemukan!'
                ],
                'absensi' => null
            ], 404); // NOT_FOUND (404) being the HTTP response code
        }
    }

    public function index_post()
    {
        // $this->form_validation->set_rules('idPegawai', 'Pegawai', 'required');
        // $this->form_validation->set_rules('jenisTransaksi', 'Jenis Transaksi', 'required');
        // $this->form_validation->set_rules('asalDana', 'Asal Dana', 'required');
        // $this->form_validation->set_rules('kategoriTransaksi', 'Kategori Transaksi', 'required');
        // $this->form_validation->set_rules('jumlahTransaksi', 'Jumlah Transaksi', 'required');

        // if ($this->form_validation->run() == false) {
        //     $respon['result'] = false;
        //     $respon['pesan'] = "Catatan gagal di simpan!, semua isian wajib di isi!";
        //     echo json_encode($respon);
        // } else {
        //     $idPegawai = filter_input(INPUT_POST, 'idPegawai');
        //     $jenisTransaksi = filter_input(INPUT_POST, 'jenisTransaksi');
        //     $asalDana = filter_input(INPUT_POST, 'asalDana');
        //     $kategoriTransaksi = filter_input(INPUT_POST, 'kategoriTransaksi');
        //     $jumlahTransaksi = filter_input(INPUT_POST, 'jumlahTransaksi');
        //     $tanggal = date('Y-m-d H:i:s');
        //     $catatanTransaksi = filter_input(INPUT_POST, 'catatanTransaksi');
        //     $idTransaksi = filter_input(INPUT_POST, 'idTransaksi');
        //     $status = filter_input(INPUT_POST, 'status');

        //     $this->db->query("Add_CatatanKeuangan '$idTransaksi','$idPegawai','$jenisTransaksi','$asalDana','$kategoriTransaksi','$jumlahTransaksi','$tanggal','$catatanTransaksi', '$status',''");
        //     $respon['result'] = true;
        //     $respon['pesan'] = "Catatan berhasil di simpan!";
        //     echo json_encode($respon);
        // }
    }

     public function index_delete()
    {
        // $id = $this->delete('idpegawai');


        // $this->db_kedua->where('idpegawai', $id);
        // $this->db_kedua->delete('userToken');
        // $message = array("status" => "Data Berhasil Di Hapus");

        // $this->set_response($message, REST_Controller::HTTP_NO_CONTENT); // NO_CONTENT (204) being the HTTP response code
    }

    public function index_put()
    {
        // $id = $this->put("Prefix");
        // $Value = $this->put("Value");

        // $this->db_kedua->set('Value', $Value);
        // $this->db_kedua->where('Prefix', $id);
        // $this->db_kedua->update('SettingGlobal');

        // $message = array("status" => "Data Berhasil Di Ubah");
        // $this->set_response($message, REST_Controller::HTTP_CREATED); // NO_CONTENT (204) being the HTTP response code
    }

}
