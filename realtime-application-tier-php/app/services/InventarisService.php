<?php

class InventarisService {

    private $inventaris; 

    public function __construct(Inventaris $inventaris) {
        $this->inventaris = $inventaris;
    }

    public function getAll() {
        $stmt = $this->inventaris->getAll();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id) {
        $this->inventaris->id = $id;
        return $this->inventaris->getById();
    }

    public function create(array $input) {
        $this->validateRequired($input, ['nama_barang', 'kategori', 'kondisi', 'jumlah']);
        $input = $this->sanitize($input);

        $this->inventaris->nama_barang = $input['nama_barang'];
        $this->inventaris->kategori    = $input['kategori'];
        $this->inventaris->kondisi     = $input['kondisi'];
        $this->inventaris->jumlah      = (int) $input['jumlah'];

        if ($this->inventaris->create()) {
            $createdData = [
                'id'           => $this->inventaris->id,
                'nama_barang'  => $this->inventaris->nama_barang,
                'kategori'     => $this->inventaris->kategori,
                'kondisi'      => $this->inventaris->kondisi,
                'jumlah'       => $this->inventaris->jumlah
            ];

            $this->notifyRealTime(
                'inventaris_updated',
                ['action' => 'create', 'data' => $createdData]
            );

            return $createdData;
        }

        throw new Exception('Gagal menambahkan data inventaris');
    }

    public function update(int $id, array $input) {
        $this->validateRequired($input, ['nama_barang', 'kategori', 'kondisi', 'jumlah']);
        $input = $this->sanitize($input);

        $this->inventaris->id          = $id;
        $this->inventaris->nama_barang = $input['nama_barang'];
        $this->inventaris->kategori    = $input['kategori'];
        $this->inventaris->kondisi     = $input['kondisi'];
        $this->inventaris->jumlah      = (int) $input['jumlah'];

        if (!$this->inventaris->update()) {
            throw new Exception('Gagal memperbarui data inventaris atau data tidak ditemukan');
        }

        $this->notifyRealTime(
            'inventaris_updated',
            ['action' => 'update', 'id' => $id]
        );
    }

    public function delete(int $id) {
        $this->inventaris->id = $id;

        if (!$this->inventaris->delete()) {
            throw new Exception('Gagal menghapus data inventaris atau data tidak ditemukan');
        }

        $this->notifyRealTime(
            'inventaris_updated',
            ['action' => 'delete', 'id' => $id]
        );
    }

    private function validateRequired(array $input, array $requiredFields): void {
        $missing = [];

        foreach ($requiredFields as $field) {
            if (!isset($input[$field]) || trim($input[$field]) === '') {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            throw new Exception('Field wajib: ' . implode(', ', $missing));
        }
    }

    private function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }

        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    private function notifyRealTime(string $event, array $data): void {
        $payload = json_encode([
            'event' => $event,
            'data'  => $data
        ]);

        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  =>
                    "Content-Type: application/json\r\n" .
                    "Content-Length: " . strlen($payload) . "\r\n",
                'content' => $payload,
                'timeout' => 3,
                'ignore_errors' => true // CRUD tidak boleh gagal karena notify
            ]
        ];

        $context = stream_context_create($options);

        @file_get_contents(
            'http://localhost:3000/notify',
            false,
            $context
        );
    }
}