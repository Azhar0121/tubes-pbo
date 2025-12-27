<?php

class Inventaris extends Model {

    public $id;
    public $nama_barang;
    public $kategori;
    public $kondisi;
    public $jumlah;

    public function __construct($db) {
        parent::__construct($db);
        $this->table = "inventaris";
    }

    //Ambil seluruh data inventaris
    public function getAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY id ASC";
        return $this->executeQuery($query);
    }

    //Ambil data inventaris berdasarkan ID
    public function getById() {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->executeQuery($query, [':id' => $this->id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->nama_barang = $row['nama_barang'];
            $this->kategori     = $row['kategori'];
            $this->kondisi      = $row['kondisi'];
            $this->jumlah       = $row['jumlah'];
            return $row;
        }

        return false;
    }

    //Tambah data inventaris
    public function create() {
        $query = "INSERT INTO {$this->table}
                  (nama_barang, kategori, kondisi, jumlah)
                  VALUES (:nama_barang, :kategori, :kondisi, :jumlah)";

        $params = [
            ':nama_barang' => $this->nama_barang,
            ':kategori'    => $this->kategori,
            ':kondisi'     => $this->kondisi,
            ':jumlah'      => $this->jumlah
        ];

        $stmt = $this->executeQuery($query, $params);

        if ($stmt) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }
    
    //Update data inventaris
    public function update() {
        $query = "UPDATE {$this->table}
                  SET nama_barang = :nama_barang,
                      kategori    = :kategori,
                      kondisi     = :kondisi,
                      jumlah      = :jumlah
                  WHERE id = :id";

        $params = [
            ':id'          => $this->id,
            ':nama_barang' => $this->nama_barang,
            ':kategori'    => $this->kategori,
            ':kondisi'     => $this->kondisi,
            ':jumlah'      => $this->jumlah
        ];

        $stmt = $this->executeQuery($query, $params);
        return $stmt->rowCount() > 0;
    }

    //Hapus data inventaris
    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->executeQuery($query, [':id' => $this->id]);
        return $stmt->rowCount() > 0;
    }
}