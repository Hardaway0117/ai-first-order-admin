<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::Processing => __('Processing'),
            self::Shipped => __('Shipped'),
            self::Completed => __('Completed'),
            self::Cancelled => __('Cancelled'),
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, match ($this) {
            self::Pending => [self::Processing, self::Cancelled],
            self::Processing => [self::Shipped, self::Cancelled],
            self::Shipped => [self::Completed],
            self::Completed, self::Cancelled => [],
        }, true);
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'text-bg-warning',
            self::Processing => 'text-bg-info',
            self::Shipped => 'text-bg-primary',
            self::Completed => 'text-bg-success',
            self::Cancelled => 'text-bg-secondary',
        };
    }

    /** 圖表用色，與狀態徽章同一套 Bootstrap 色系 */
    public function chartColor(): string
    {
        return match ($this) {
            self::Pending => '#ffc107',
            self::Processing => '#0dcaf0',
            self::Shipped => '#0d6efd',
            self::Completed => '#198754',
            self::Cancelled => '#6c757d',
        };
    }
}
