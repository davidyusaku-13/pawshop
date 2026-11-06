<?php

namespace App;

class ProductService
{
    public static function getAll(?int $categoryId = null, ?string $search = null, int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT p.*, k.name AS kategori_name 
                FROM produk p 
                LEFT JOIN kategori k ON p.category_id = k.id 
                WHERE 1=1";
        $params = [];

        if ($categoryId !== null && $categoryId > 0) {
            $sql .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }

        if (!empty($search)) {
            $sql .= " AND (p.nama_produk LIKE ? OR p.detail LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $sql .= " ORDER BY p.id ASC";

        if ($limit > 0) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }

        return Database::query($sql, $params);
    }

    public static function count(?int $categoryId = null, ?string $search = null): int
    {
        $sql = "SELECT COUNT(*) as count FROM produk WHERE 1=1";
        $params = [];

        if ($categoryId !== null && $categoryId > 0) {
            $sql .= " AND category_id = ?";
            $params[] = $categoryId;
        }

        if (!empty($search)) {
            $sql .= " AND (nama_produk LIKE ? OR detail LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $row = Database::fetchOne($sql, $params);
        return (int)($row['count'] ?? 0);
    }

    public static function getById(int $id): ?array
    {
        $sql = "SELECT p.*, k.name AS kategori 
                FROM produk p 
                LEFT JOIN kategori k ON p.category_id = k.id 
                WHERE p.id = ?";
        return Database::fetchOne($sql, [$id]);
    }

    public static function create(array $data, ?array $file = null, ?string &$error = null): bool
    {
        $gambar = 'x.jpg';
        if ($file && !empty($file['name'])) {
            $uploaded = FileUploader::uploadImage($file, null, $error);
            if (!$uploaded) {
                return false;
            }
            $gambar = $uploaded;
        }

        return Database::execute(
            "INSERT INTO produk (gambar, nama_produk, category_id, stok, harga, detail) VALUES (?, ?, ?, ?, ?, ?)",
            [
                $gambar,
                $data['nama_produk'],
                (int)$data['category_id'],
                (int)$data['stok'],
                (int)$data['harga'],
                $data['detail'] ?? ''
            ]
        );
    }

    public static function update(int $id, array $data, ?array $file = null, ?string &$error = null): bool
    {
        $current = self::getById($id);
        if (!$current) {
            $error = "Produk tidak ditemukan.";
            return false;
        }

        $gambar = $current['gambar'];
        if ($file && !empty($file['name'])) {
            $uploaded = FileUploader::uploadImage($file, $current['gambar'], $error);
            if (!$uploaded) {
                return false;
            }
            $gambar = $uploaded;
        }

        return Database::execute(
            "UPDATE produk SET gambar = ?, nama_produk = ?, category_id = ?, stok = ?, harga = ?, detail = ? WHERE id = ?",
            [
                $gambar,
                $data['nama_produk'],
                (int)$data['category_id'],
                (int)$data['stok'],
                (int)$data['harga'],
                $data['detail'] ?? '',
                $id
            ]
        );
    }

    public static function delete(int $id): bool
    {
        $current = self::getById($id);
        if ($current && !empty($current['gambar'])) {
            FileUploader::delete($current['gambar']);
        }
        return Database::execute("DELETE FROM produk WHERE id = ?", [$id]);
    }

    // CATEGORY MANAGEMENT
    public static function getAllCategories(): array
    {
        return Database::query("SELECT * FROM kategori ORDER BY id ASC");
    }

    public static function getCategoryById(int $id): ?array
    {
        return Database::fetchOne("SELECT * FROM kategori WHERE id = ?", [$id]);
    }

    public static function createCategory(string $name): bool
    {
        return Database::execute("INSERT INTO kategori (name) VALUES (?)", [trim($name)]);
    }

    public static function updateCategory(int $id, string $name): bool
    {
        return Database::execute("UPDATE kategori SET name = ? WHERE id = ?", [trim($name), $id]);
    }

    public static function deleteCategory(int $id): bool
    {
        return Database::execute("DELETE FROM kategori WHERE id = ?", [$id]);
    }
}
