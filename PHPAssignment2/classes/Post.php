<?php
require_once 'Database.php';

class Post
{
    private $conn;
    private $uploadDir;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
        $this->uploadDir = './uploads/posts/';

        if (!is_dir($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0755, true) && !is_dir($this->uploadDir)) {
                die("Error: Unable to create upload directory.");
            }
        }
    }


    private function getNextId()
    {
        $stmt = $this->conn->query("SELECT IFNULL(MAX(id), 0) + 1 AS nextId FROM posts");
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['nextId'];
    }


    private function handleUpload(array $file = null): ?string
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExts)) {
            die("Invalid file type. Allowed types: " . implode(', ', $allowedExts));
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            die("File size exceeds 5MB.");
        }

        $filename = uniqid('post_', true) . "." . $ext;
        $target = $this->uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            die("Failed to move uploaded file.");
        }

        return $filename;
    }


    public function createPost($userId, string $title, string $content, array $imageFile = null): bool
    {
        $nextId = $this->getNextId();
        $imageFilename = $this->handleUpload($imageFile);

        $sql = "INSERT INTO posts (id, user_id, title, content, image) 
                VALUES (:id, :user_id, :title, :content, :image)";
        $stmt = $this->conn->prepare($sql);

        // Use execute with array - letting PDO handle binding types to avoid errors
        return $stmt->execute([
            ':id' => $nextId,
            ':user_id' => $userId,
            ':title' => $title,
            ':content' => $content,
            ':image' => $imageFilename
        ]);
    }

    public function update(int $id, string $title, string $content, array $imageFile = null): bool
    {
        $post = $this->getPost($id);
        if (!$post) {
            return false;
        }

        $imageFilename = $post['image'];
        if ($imageFile && $imageFile['error'] === UPLOAD_ERR_OK) {
            if ($imageFilename && file_exists($this->uploadDir . $imageFilename)) {
                unlink($this->uploadDir . $imageFilename);
            }
            $imageFilename = $this->handleUpload($imageFile);
        }

        $sql = "UPDATE posts
                SET title = :title, content = :content, image = :image, updated_at = NOW()
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':title' => $title,
            ':content' => $content,
            ':image' => $imageFilename,
            ':id' => $id
        ]);
    }

    public function getAllPosts()
    {
        $stmt = $this->conn->prepare("SELECT posts.*, users.name AS author FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPost($id)
    {
        $sql = "SELECT * FROM posts WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function deletePost($id)
    {
        $post = $this->getPost($id);
        if (!$post) {
            return false;
        }
        if ($post['image'] && file_exists($this->uploadDir . $post['image'])) {
            unlink($this->uploadDir . $post['image']);
        }
        $sql = "DELETE FROM posts WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function getPostsByUser(int $userId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM posts WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
