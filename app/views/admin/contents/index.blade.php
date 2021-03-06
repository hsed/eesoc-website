@extends('layouts.admin')

@section('content')
  <div class="row">
    <div class="col-lg-12">
      <div class="page-header">
        <a href="{{ URL::route('admin.contents.create') }}" class="pull-right btn btn-primary btn-lg">
          <span class="glyphicon glyphicon-plus"></span>
          New Content Block
        </a>
        <h1>Content Blocks</h1>
      </div>
      <div class="list-group category-list">
        @foreach($contents as $content)
          <a href="{{ URL::route('admin.contents.edit', $content->id) }}" class="list-group-item">
            <h3>
              {{{ $content->name }}}
              <small>{{{ $content->template_code }}}</small>
            </h3>
          </a>
        @endforeach
      </div>
    </div>
  </div>
@stop
