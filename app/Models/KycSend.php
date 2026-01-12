<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycSend extends Model
{
    use HasFactory;
    protected $fillable = ['email', 'name_client', 'lastname_client', 'shipping_status', 'kyc_status', 'status_firmante01', 'status_firmante02', 'employee_id', 'tracking_code', 'client_code'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
