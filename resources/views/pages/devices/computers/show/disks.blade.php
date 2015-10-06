@extends('pages.devices.computers.show')

@section('show.panel.title')

    Hard Disks

    <span class="pull-right btn-group">
        <a
                class="btn btn-xs btn-warning"
                data-post="POST"
                data-title="Synchronize hard disks?"
                data-message="Are you sure you want to synchronize this computers hard disks?"
                href="{{ route('devices.computers.delete', [$computer->getKey()]) }}"
                >
            <i class="fa fa-refresh"></i>
            Synchronize
        </a>
    </span>

@stop

@section('show.panel.body')



@stop
