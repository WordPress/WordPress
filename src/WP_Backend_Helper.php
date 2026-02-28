<?php
declare(strict_types=1);

class WP_Backend_Helper
{
    public function sanitizePostTitle(string $title): string
    {
        $title = trim(strip_tags($title));
        if (strlen($title) > 200) {
            $title = substr($title, 0, 200);
        }
        return $title;
    }

    public function isValidPostTitle(string $title): bool
    {
        $title = trim(strip_tags($title));
        return strlen($title) >= 1 && strlen($title) <= 200;
    }

    public function generateSlug(string $title): string
    {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }

    public function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function isStrongPassword(string $password): bool
    {
        return strlen($password) >= 8;
    }

    public function generateExcerpt(string $content, int $wordCount = 55): string
    {
        $words = explode(' ', strip_tags($content));
        if (count($words) <= $wordCount) {
            return trim($content);
        }
        return implode(' ', array_slice($words, 0, $wordCount)) . '...';
    }

    public function isValidPostStatus(string $status): bool
    {
        return in_array($status, ['publish', 'draft', 'pending', 'private', 'trash'], true);
    }
}
