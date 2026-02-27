<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class Payment extends Model
{
    use HasFactory;

    protected static ?bool $hasSnapTokenColumn = null;

    protected $fillable = [
        'order_id',
        'method',
        'amount',
        'transaction_id',
        'status',
        'external_id',
        'qr_string',
        'image_url',
        'payment_gateway_ref',
        'snap_token',
        'paid_at'
    ];

    protected static function booted(): void
    {
        static::saving(function (self $payment): void {
            if (!self::supportsSnapTokenColumn()) {
                unset($payment->attributes['snap_token']);
            }
        });
    }

    public static function supportsSnapTokenColumn(): bool
    {
        if (self::$hasSnapTokenColumn !== null) {
            return self::$hasSnapTokenColumn;
        }

        try {
            $table = (new self())->getTable();
            self::$hasSnapTokenColumn = Schema::hasTable($table)
                && Schema::hasColumn($table, 'snap_token');
        } catch (\Throwable $e) {
            self::$hasSnapTokenColumn = false;
        }

        return self::$hasSnapTokenColumn;
    }

    /**
     * Get the order that owns the Payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
