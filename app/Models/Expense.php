<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    protected $fillable = [
        'reference',
        'expense_categories_id',
        'amount',
        'status',
        'description',
        'store_id',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_categories_id');
    }
      // Expense belongs to a store
      public function store()
      {
          return $this->belongsTo(Store::class, 'store_id');
      }
}
