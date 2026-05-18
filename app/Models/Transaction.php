<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory, HasUuids;

    public const DROP_OFF_PROCESS_STEPS = [
        'RECEIVED',
        'WASHED',
        'DRIED',
        'IRONED',
        'READY',
        'PICKED_UP',
    ];

    protected $fillable = [
        'outlet_id',
        'cashier_id',
        'member_id',
        'transaction_number',
        'transaction_type',
        'service_type',
        'weight',
        'estimated_finish',
        'discount_percent',
        'discount_amount',
        'tax_percent',
        'tax_amount',
        'status',
        'process_step',
        'subtotal',
        'member_discount',
        'total_amount',
        'payment_method',
        'payment_status',
        'trx_reference',
        'qris_qr_image',
        'qris_tutorial_pembayaran',
        'ref_id',
        'payment_fee',
        'payment_expires_at',
        'paid_at',
        'amount_received',
        'change_amount',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'member_discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'payment_fee' => 'decimal:2',
        'amount_received' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'estimated_finish' => 'datetime',
        'payment_expires_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function tenant()
    {
        return $this->hasOneThrough(
            Tenant::class,
            Outlet::class,
            'id',
            'id',
            'outlet_id',
            'tenant_id',
        );
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function selfServiceDetails()
    {
        return $this->hasMany(SelfServiceDetail::class);
    }

    public function servicePackages()
    {
        return $this->belongsToMany(ServicePackage::class, 'transaction_services')
            ->withPivot('price')
            ->withTimestamps();
    }

    public function addonOptions()
    {
        return $this->belongsToMany(AddonOption::class, 'transaction_addons')
            ->withPivot('price')
            ->withTimestamps();
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function isDropOff(): bool
    {
        return $this->transaction_type === 'DROP_OFF';
    }

    public function currentProcessStep(): ?string
    {
        if (! $this->isDropOff()) {
            return null;
        }

        if ($this->process_step) {
            return $this->process_step;
        }

        return match ($this->status) {
            'READY' => 'READY',
            'COMPLETED' => 'PICKED_UP',
            default => 'RECEIVED',
        };
    }

    public function nextProcessStep(): ?string
    {
        $currentStep = $this->currentProcessStep();

        if (! $currentStep) {
            return null;
        }

        $currentIndex = array_search($currentStep, self::DROP_OFF_PROCESS_STEPS, true);

        if ($currentIndex === false || ! isset(self::DROP_OFF_PROCESS_STEPS[$currentIndex + 1])) {
            return null;
        }

        return self::DROP_OFF_PROCESS_STEPS[$currentIndex + 1];
    }

    public function whatsappReadyMessage(): ?string
    {
        if (! $this->member || ! $this->member->no_hp) {
            return null;
        }

        $memberName = $this->member->nama ?? 'Pelanggan';
        $formattedTotal = number_format((float) $this->total_amount, 0, ',', '.');

        return "Halo {$memberName}, laundry Anda dengan nota {$this->transaction_number} di {$this->outlet->nama_outlet} sudah selesai dan siap diambil. Total tagihan Anda adalah Rp {$formattedTotal}. Terima kasih!";
    }

    public function whatsappReadyUrl(): ?string
    {
        $message = $this->whatsappReadyMessage();

        if (! $message) {
            return null;
        }

        $cleanPhone = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', (string) $this->member->no_hp));

        return 'https://wa.me/' . $cleanPhone . '?text=' . urlencode($message);
    }
}
