@extends('layouts.application')

@section('content')
  <div class="page-header">
    <h1>Dinner Seating Planner</h1>
  </div>
  <h4 class="alert alert-info">
    You purchased <strong>{{ Auth::user()->dinnerSales()->sum('quantity') }} tickets</strong>.
    Please enter the guests' names below.
    @if ($group)
      They will be placed in <strong>Group #{{ $group->id }}</strong>
    @else
      They will be placed in a group.
    @endif
  </h4>
  {{ Form::open(['route' => 'dashboard.dinner.groups.store']) }}
    @if ($group)
      {{ Form::hidden('group_id', $group->id) }}
    @endif
    <div class="form-group">
      {{ Form::text('user_0', Auth::user()->name, array('class' => 'form-control', 'disabled' => true)) }}
    </div>
    @for ($i = 1; $i < Auth::user()->tickets_count; $i++)
      <div class="form-group {{ $errors->first("user_{$i}", 'has-error') }}">
        {{ Form::text("user_{$i}", ($i === 0 ? Auth::user()->name : null), array('class' => 'form-control')) }}
        {{ $errors->first("user_{$i}", '<span class="help-block">:message</span>') }}
      </div>
    @endfor
    {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
  {{ Form::close() }}
  <br />
  <p>If you experience any issues, please <a href='mailto:eesoc.webmaster@imperial.ac.uk'>email us</a></p>
@stop