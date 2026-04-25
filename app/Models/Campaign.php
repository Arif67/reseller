<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function product(){
        return $this->hasOne(Product::class, 'id','product_id')
            ->select('products.id', 'products.name', 'products.slug', 'products.old_price', 'products.new_price');
    }
    public function images(){
        return $this->hasMany(CampaignReview::class, 'campaign_id')
            ->select('campaign_reviews.id', 'campaign_reviews.image', 'campaign_reviews.campaign_id');
    }
}
