<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashflow extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'cashflow'; // Pastikan ini 'cashflow' (singular) sesuai error Anda

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'tipe',
        'nominal', // <-- INI YANG HILANG SEBELUMNYA
        'description',
        'cover',
    ];

    /**
     * Mendapatkan user yang memiliki catatan cashflow ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}