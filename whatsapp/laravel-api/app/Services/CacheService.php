<?php

namespace App\Services;

class CacheService
{
    private $db;

    public function __construct()
    {
        $dbPath = __DIR__ . '/../../storage/database/bot_cache.sqlite';
        if (!is_dir(dirname($dbPath))) {
            mkdir(dirname($dbPath), 0777, true);
        }
        $this->db = new \PDO("sqlite:$dbPath");
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->initialize();
    }

    private function initialize()
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS bot_cache (
                keyword TEXT PRIMARY KEY,
                category TEXT,
                results_json TEXT,
                expires_at DATETIME
            );
            CREATE TABLE IF NOT EXISTS conversation_history (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                chat_id TEXT,
                role TEXT,
                message TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");
    }

    public function get($keyword, $category)
    {
        $stmt = $this->db->prepare("
            SELECT results_json FROM bot_cache 
            WHERE keyword = :keyword AND category = :category AND expires_at > DATETIME('now')
        ");
        $stmt->execute(['keyword' => $keyword, 'category' => $category]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result ? json_decode($result['results_json'], true) : null;
    }

    public function set($keyword, $category, $data, $ttlSeconds = 3600)
    {
        $expiresAt = date('Y-m-d H:i:s', time() + $ttlSeconds);
        $stmt = $this->db->prepare("
            INSERT OR REPLACE INTO bot_cache (keyword, category, results_json, expires_at)
            VALUES (:keyword, :category, :results, :expires)
        ");
        return $stmt->execute([
            'keyword' => $keyword,
            'category' => $category,
            'results' => json_encode($data),
            'expires' => $expiresAt
        ]);
    }

    public function addHistory($chatId, $role, $message)
    {
        $stmt = $this->db->prepare("
            INSERT INTO conversation_history (chat_id, role, message)
            VALUES (:chat_id, :role, :message)
        ");
        $stmt->execute(['chat_id' => $chatId, 'role' => $role, 'message' => $message]);

        // Keep only last 10 messages per chat to prevent bloat
        $this->db->exec("
            DELETE FROM conversation_history 
            WHERE id IN (
                SELECT id FROM conversation_history 
                WHERE chat_id = '$chatId' 
                ORDER BY created_at DESC 
                LIMIT -1 OFFSET 10
            )
        ");
    }

    public function getHistory($chatId)
    {
        $stmt = $this->db->prepare("
            SELECT role, message FROM conversation_history 
            WHERE chat_id = :chat_id 
            ORDER BY created_at ASC
        ");
        $stmt->execute(['chat_id' => $chatId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function clear()
    {
        $this->db->exec("DELETE FROM bot_cache");
        $this->db->exec("DELETE FROM conversation_history");
    }
}
