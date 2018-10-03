<?php

namespace ElectronicInvoicing;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    protected $fillable = [
        'voucher_id',
        'product_id',
        'quantity',
        'unit_price',
        'discount'
    ];

    public function voucher()
    {
        return $this->belongsTo('ElectronicInvoicing\Voucher');
    }

    public function product()
    {
        return $this->belongsTo('ElectronicInvoicing\Product');
    }

    public function aditionalDetails()
    {
        return $this->hasMany('ElectronicInvoicing\AditionalDetail');
    }

    public function taxDetails()
    {
        return $this->hasMany('ElectronicInvoicing\TaxDetail');
    }
}
