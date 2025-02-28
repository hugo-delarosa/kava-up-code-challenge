<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $fillable = [
        'name',
        'description',
        'due_date',
    ];

    /**
     * Return the user that created the project.
     *
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return the tasks of the project.
     *
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

}
