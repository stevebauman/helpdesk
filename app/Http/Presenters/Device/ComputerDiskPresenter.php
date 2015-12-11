<?php

namespace App\Http\Presenters\Device;

use App\Http\Presenters\Presenter;
use App\Models\Computer;
use App\Models\ComputerHardDisk;
use Orchestra\Html\Table\Grid as TableGrid;

class ComputerDiskPresenter extends Presenter
{
    /**
     * Displays the specified computers disks.
     *
     * @param Computer $computer
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function disks(Computer $computer)
    {
        return $this->table->of('computers.disks', function (TableGrid $table) use ($computer) {
            $table->with($computer->disks()->getQuery());

            $table->attributes('class', 'table table-hover');

            $table->column('name', function ($column) {
                $column->label = 'Name';
            });

            $table->column('size', function ($column) {
                $column->label = 'Size';
                $column->value = function (ComputerHardDisk $disk) {
                    return $disk->getSizeReadable();
                };
            });

            $table->column('used', function ($column) {
                $column->label = 'Used';
                $column->value = function (ComputerHardDisk $disk) {
                    return $disk->getPercentUsedProgressBar();
                };
            });
        });
    }
}
