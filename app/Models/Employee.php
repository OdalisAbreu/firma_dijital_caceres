<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'email', 'department', 'position', 'major_employee', 'code_employee'];

    protected $casts = [
        'major_employee' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($employee) {
            // Si se está marcando como major_employee, desactivar los demás
            if ($employee->major_employee) {
                static::where('id', '!=', $employee->id)
                    ->where('major_employee', true)
                    ->update(['major_employee' => false]);
            }
        });
    }

    public function kycSends()
    {
        return $this->hasMany(KycSend::class);
    }
}
