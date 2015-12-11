<?php

namespace App\Models;

use Orchestra\Support\Facades\HTML;

class ComputerHardDisk extends Model
{
    /**
     * The computer hard disk table.
     *
     * @var string
     */
    protected $table = 'computer_hard_disks';

    /**
     * The fillable computer hard disk attributes.
     *
     * @var array
     */
    protected $fillable = [
        'computer_id',
        'name',
    ];

    /**
     * The belongsTo computer relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function computer()
    {
        return $this->belongsTo(Computer::class, 'computer_id');
    }

    /**
     * The hasMany computer hard disk records relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function records()
    {
        return $this->hasMany(ComputerHardDiskRecord::class, 'disk_id');
    }

    /**
     * Returns the hard disks size in a human readable format.
     *
     * @param int $precision
     *
     * @return string
     */
    public function getSizeReadable($precision = 2)
    {
        $size = $this->size;

        for ($i = 0; ($size / 1024) > 0.9; $i++, $size /= 1024) {
        }

        return round($size, $precision).['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'][$i];
    }

    /**
     * Returns the current hard disks free space.
     *
     * @return int
     */
    public function getFreeSpace()
    {
        $record = $this->records()->latest()->first();

        if ($record instanceof ComputerHardDiskRecord) {
            return $record->free;
        }

        return 0;
    }

    /**
     * Returns the percent of space free on the current hard disk.
     *
     * @return string
     */
    public function getPercentUsed()
    {
        $used = $this->size - $this->getFreeSpace();

        return sprintf('%.2f', ($used / $this->size) * 100);
    }

    /**
     * Returns the percent used progress
     * bar for the current hard disk.
     *
     * @return string
     */
    public function getPercentUsedProgressBar()
    {
        $free = $this->getPercentUsed();

        $bar = HTML::create('div', "$free%", ['class' => 'progress-bar', 'role' => 'progressbar', 'style' => "width: $free%;"]);

        return HTML::raw("<div class='progress'>$bar</div>");
    }
}
