<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'category_id',
        'amount',
        'expense_date',
        'description',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
