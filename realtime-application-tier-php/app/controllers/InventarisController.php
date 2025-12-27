<?php

class InventarisController extends Controller {

    private $service;

    public function __construct() {
        $db = (new Database())->getConnection();
        $inventarisModel = new Inventaris($db);
        $this->service = new InventarisService($inventarisModel);
    }

    public function index() {
        try {
            $result = $this->service->getAll();
            $this->success($result, 'Data inventaris berhasil diambil');
        } catch (Exception $e) {
            $this->error('Gagal mengambil data inventaris: ' . $e->getMessage(), 500);
        }
    }

    public function show($id) {
        try {
            $result = $this->service->getById((int) $id);
            if ($result) {
                $this->success($result, 'Data inventaris ditemukan');
            } else {
                $this->error('Data inventaris tidak ditemukan', 404);
            }
        } catch (Exception $e) {
            $this->error('Gagal mengambil data inventaris: ' . $e->getMessage(), 500);
        }
    }

    public function create() {
        $input = $this->getJsonInput();
        if (!$input) {
            $this->error('Data JSON tidak valid', 400);
        }

        try {
            $created = $this->service->create($input);
            $this->success($created, 'Data inventaris berhasil ditambahkan', 201);
        } catch (Exception $e) {
            $this->error('Gagal menambahkan data inventaris: ' . $e->getMessage(), 500);
        }
    }

    public function update($id) {
        if (!$id || !is_numeric($id)) {
            $this->error('ID inventaris tidak valid', 400);
        }

        $input = $this->getJsonInput();
        if (!$input) {
            $this->error('Data JSON tidak valid', 400);
        }

        try {
            $this->service->update((int) $id, $input);
            $this->success(null, 'Data inventaris berhasil diperbarui');
        } catch (Exception $e) {
            $this->error('Gagal memperbarui data inventaris: ' . $e->getMessage(), 500);
        }
    }

    public function delete($id) {
        if (!$id || !is_numeric($id)) {
            $this->error('ID inventaris tidak valid', 400);
        }

        try {
            $this->service->delete((int) $id);
            $this->success(null, 'Data inventaris berhasil dihapus');
        } catch (Exception $e) {
            $this->error('Gagal menghapus data inventaris: ' . $e->getMessage(), 500);
        }
    }
}