<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategories extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'expense_categories';

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'expense_cat_id');
    }
}
