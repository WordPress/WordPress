<?php

declare(strict_types=1);

namespace Sentry\Serializer\Traits;

use Sentry\Breadcrumb;

/**
 * @internal
 */
trait BreadcrumbSeralizerTrait
{
    /**
     * @return array<string, mixed>
     *
     * @psalm-return array{
     *     type: string,
     *     category: string,
     *     level: string,
     *     timestamp: float,
     *     message?: string,
     *     data?: object
     * }
     */
    protected static function serializeBreadcrumb(Breadcrumb $breadcrumb): array
    {
        $result = [
            'type' => $breadcrumb->getType(),
            'category' => $breadcrumb->getCategory(),
            'level' => $breadcrumb->getLevel(),
            'timestamp' => $breadcrumb->getTimestamp(),
        ];

        if ($breadcrumb->getMessage() !== null) {
            $result['message'] = $breadcrumb->getMessage();
        }

        if (!empty($breadcrumb->getMetadata())) {
            $result['data'] = (object) $breadcrumb->getMetadata();
        }

        return $result;
    }
}
