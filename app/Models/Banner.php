<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Banner extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'banner_id';

    protected $fillable = [
        'title', 'image_url', 'target_url', 'type',
        'category_id', 'is_active', 'display_order',
        'start_date', 'end_date'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    // Constants for banner types
    const TYPE_MAIN = 'main';
    const TYPE_PROMO = 'promo';
    const TYPE_CATEGORY = 'category';

    /**
     * Relationship with Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    /**
     * Scope for active banners
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for currently visible banners (within date range if set)
     */
    public function scopeVisible($query)
    {
        return $query->active()
            ->where(function($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', Carbon::now());
            })
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', Carbon::now());
            });
    }

    /**
     * Scope for banners by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for main banners
     */
    public function scopeMain($query)
    {
        return $this->scopeOfType($query, self::TYPE_MAIN);
    }

    /**
     * Scope for promo banners
     */
    public function scopePromo($query)
    {
        return $this->scopeOfType($query, self::TYPE_PROMO);
    }

    /**
     * Scope for category banners
     */
    public function scopeCategory($query)
    {
        return $this->scopeOfType($query, self::TYPE_CATEGORY);
    }

    /**
     * Scope for banners in proper display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('title');
    }

    /**
     * Check if banner is currently visible
     */
    public function isVisible(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();

        if ($this->start_date && $this->start_date->gt($now)) {
            return false;
        }

        if ($this->end_date && $this->end_date->lt($now)) {
            return false;
        }

        return true;
    }

    /**
     * Get all available banner types with labels
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_MAIN => 'Main Banner',
            self::TYPE_PROMO => 'Promo Banner',
            self::TYPE_CATEGORY => 'Category Banner',
        ];
    }

    /**
     * Get the label for the current banner type
     */
    public function getTypeLabel(): string
    {
        $types = self::getTypes();
        return $types[$this->type] ?? ucfirst($this->type);
    }

    /**
     * Get the full URL for the banner image
     */
    public function getImageUrl(): string
    {
        return asset('storage/' . $this->image_url);
    }

    /**
     * Get the target URL with proper protocol
     */
    public function getTargetUrl(): ?string
    {
        if (empty($this->target_url)) {
            return null;
        }

        if (!preg_match("~^(?:f|ht)tps?://~i", $this->target_url)) {
            return "https://" . $this->target_url;
        }

        return $this->target_url;
    }

    /**
     * Get formatted date range if both dates exist
     */
    public function getDateRange(): ?string
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->format('M d, Y') . ' - ' . $this->end_date->format('M d, Y');
        }

        return null;
    }

    /**
     * Get the status badge HTML
     */
    public function getStatusBadge(): string
    {
        if ($this->isVisible()) {
            return '<span class="badge badge-phoenix fs-10 bg-success">Active</span>';
        }

        return '<span class="badge badge-phoenix fs-10 bg-secondary">Inactive</span>';
    }
}
