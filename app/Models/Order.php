<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'status',
        'total',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'total' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->order_number ??= static::generateOrderNumber();
        });
    }

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-'.now()->format('Ymd').'-'.strtoupper(Str::random(4));
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    /**
     * 依狀態機規則轉換狀態；取消時歸還品項庫存。回傳是否轉換成功。
     */
    public function transitionTo(OrderStatus $target): bool
    {
        if (! $this->status->canTransitionTo($target)) {
            return false;
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($target) {
            if ($target === OrderStatus::Cancelled) {
                foreach ($this->items as $item) {
                    Product::whereKey($item->product_id)->increment('stock', $item->quantity);
                }
            }

            $this->update(['status' => $target]);
        });

        return true;
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
