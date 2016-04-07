<ul class="nav navbar-left navbar-nav">

    <li class="{{ active()->route('issues.index') }}">

        <a href="{{ route('issues.index') }}">
            <i class="fa fa-exclamation-circle"></i> Open ({{ $open }})
        </a>

    </li>

    <li class="{{ active()->route('issues.closed') }}" >

        <a href="{{ route('issues.closed') }}">
            <i class="fa fa-check-circle"></i> Closed ({{ $closed }})
        </a>

    </li>

    <li class="dropdown {{ active()->input('label') }}">

        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-tags"></i>
            Labels
            <i class="fa fa-caret-down"></i>
        </a>

        <ul class="dropdown-menu dropdown-menu-labels">
            @if(count($labels) > 0)

                @foreach($labels as $label)

                    <li class="{{ active()->input('label', $label->name) }}">

                        <a href="{{ route(request()->route()->getName(), array_merge(request()->all(), ['label' => $label->name])) }}">
                            {!! $label->display_large !!}
                        </a>

                    </li>

                @endforeach

            @else

                @if(\App\Policies\LabelPolicy::create(auth()->user()))

                    <li>
                        <a href="{{ route('labels.create') }}">
                            <i class="fa fa-plus-square"></i> Create a Label
                        </a>
                    </li>

                @else

                    <li>
                        <a>
                            No Labels
                        </a>
                    </li>

                @endif

            @endif
        </ul>

    </li>

    <li class="dropdown {{ active()->input('resolution') }}">

        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-check"></i>
            Has Answer
            <i class="fa fa-caret-down"></i>
        </a>

        <ul class="dropdown-menu dropdown-menu-labels">
            <li class="{{ active()->input('resolution', 'yes') }}">
                <a href="{{ route(request()->route()->getName(), array_merge(request()->all(), ['resolution' => 'yes'])) }}">
                    Yes
                </a>
            </li>
        </ul>

    </li>

    @if(count(request()->all()) > 2)

        <li class="dropdown">

            <a href="{{ route(request()->route()->getName()) }}">
                <i class="fa fa-times"></i>
                Clear Filters
            </a>

        </li>

    @endif

    <li>
        <a href="{{ route('issues.create') }}">
            <i class="fa fa-plus-square"></i> New Ticket
        </a>
    </li>

</ul>

@include('components.search')
