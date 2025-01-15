<?php

namespace AJURMediaMaker;

class States
{
    public static function getUserState($user_id):mixed
    {
        $pdo = App::$pdo;
        $stmt = $pdo->prepare('SELECT state FROM user_states WHERE user_id = ?');
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }

    // Функция для сохранения состояния пользователя
    public static function setUserState($user_id, $state): void
    {
        $pdo = App::$pdo;
        $stmt = $pdo->prepare('REPLACE INTO user_states (user_id, state) VALUES (?, ?)');
        $stmt->execute([$user_id, $state]);
    }
}