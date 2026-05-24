<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Users associated with this role.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Permissions belonging to this role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }
}
