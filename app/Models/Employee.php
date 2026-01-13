<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'email', 'department', 'position', 'major_employee', 'code_employee', 'user_id'];

    protected $casts = [
        'major_employee' => 'boolean',
        'user_id' => 'integer',
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
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
