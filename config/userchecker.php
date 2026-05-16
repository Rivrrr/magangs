<?php

class UserModel {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function findUser($email) {
        $stmt = $this->conn->prepare("
            SELECT *
            FROM mahasiswa
            WHERE email = ?
        ");
        $stmt->execute([$email]);
        $mahasiswa = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($mahasiswa) {
            return [
                'id' => $mahasiswa['id_mahasiswa'],
                'nama' => $mahasiswa['nama'],
                'email' => $mahasiswa['email'],
                'password' => $mahasiswa['password'],
                'role' => 'mahasiswa'
            ];
        }

        $stmt = $this->conn->prepare("
            SELECT
                d.id_dosen AS id,
                d.nama,
                d.email,
                d.password,
                CASE
                    WHEN k.id_kps IS NOT NULL THEN 'kps'
                    ELSE 'dosen'
                END AS role
            FROM dosen d
            LEFT JOIN kps k ON d.id_dosen = k.id_dosen
            WHERE d.email = ?
        ");
        $stmt->execute([$email]);
        $dosen = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dosen) {
            return [
                'id' => $dosen['id'],
                'nama' => $dosen['nama'],
                'email' => $dosen['email'],
                'password' => $dosen['password'],
                'role' => $dosen['role']
            ];
        }

        $stmt = $this->conn->prepare("
            SELECT *
            FROM admin
            WHERE email = ?
        ");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            return [
                'id' => $admin['id_admin'],
                'nama' => $admin['nama'],
                'email' => $admin['email'],
                'password' => $admin['password'],
                'role' => 'admin'
            ];
        }

        return null;
    }
}