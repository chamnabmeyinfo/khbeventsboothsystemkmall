<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'category';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'name',
        'parent_id',
        'limit',
        'status',
        'create_time',
        'update_time',
    ];

    /**
     * Get parent category
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get child categories
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get booths in this category
     */
    public function booths()
    {
        return $this->hasMany(Booth::class, 'category_id');
    }

    /**
     * Get booths in this sub-category
     */
    public function subCategoryBooths()
    {
        return $this->hasMany(Booth::class, 'sub_category_id');
    }
}
