@extends('layouts.application')

@section('sales_panel')
<div class="panel panel-info locker-sale-panel">
  <div class="panel-heading">Sales</div>
  <div class="panel-body">
    @if ($locker_sales->isEmpty())
      No orders yet.
    @else
      <table class="table table-hover table-condensed">
        <thead>
          <tr>
            <th>#</th>
            <th>Date</th>
            <th class="text-center">Unit Price</th>
            <th class="text-center">Quantity</th>
            <th class="text-center">Gross Price</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($locker_sales as $sale)
            <tr class="{{{ $sale->status_css }}}">
              <td>{{{ $sale->id }}}</td>
              <td>{{{ $sale->date }}}</td>
              <td class="text-center">{{{ $sale->unit_price }}}</td>
              <td class="text-center">{{{ $sale->quantity }}}</td>
              <td class="text-center">{{{ $sale->gross_price }}}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
  <div class="panel-footer">
    Please note that any orders made on ICU Shop may take up to 24 hours
    before it will appear on our system. If you have yet to see your order
    here, please <a href="#">contact us</a>.
  </div>
</div>
@stop

@section('unclaimed_lockers_alert')
  @if ($unclaimed_lockers_count > 0)
    <div class="alert alert-success">
      You currently have
      <strong>{{{ $unclaimed_lockers_count }}}</strong>
      unclaimed
      {{{ str_plural('locker', $unclaimed_lockers_count) }}}
    </div>
  @endif
@stop

@section('lockers_owned_panel')
  @if ( ! $lockers_owned->isEmpty())
    <div class="panel panel-default">
      <div class="panel-heading">Lockers I currently own</div>
      <div class="panel-body">
        <div class="row">
          @foreach ($lockers_owned as $locker)
            <div class="col-xs-3 col-sm-2">
              <h2>
                <a href="#{{{ $locker->anchor_id }}}">
                  {{{ $locker->name }}}
                </a>
              </h2>
              <h4>
                {{{ $locker->lockerCluster->lockerFloor->name }}}
                <small>{{{ $locker->lockerCluster->name }}}</small>
              </h4>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  @endif
@stop

@section('content')
  <div class="page-header">
    <h1>Locker Sale</h1>
  </div>
  <div class="row">
    <div class="col-lg-6">
      @yield('unclaimed_lockers_alert')
      @content('locker-sale-introduction')
      <a href="{{{ action('LockersController@getRent') }}}" class="btn btn-primary btn-block btn-lg" data-disable-with="Please wait&hellip;">
        <span class="glyphicon glyphicon-shopping-cart"></span>
        Rent a Locker
      </a>
    </div>
    <div class="col-lg-6">
      @yield('lockers_owned_panel')
      @yield('sales_panel')
    </div>
  </div>
  <hr>
  @foreach ($locker_floors as $locker_floor)
    <div class="page-header" id="{{ $locker_floor->anchor_name }}">
      <h2>{{{ $locker_floor->name }}}</h2>
    </div>
    @foreach ($locker_floor->lockerClusters()->sorted()->get() as $locker_cluster)
      <div class="panel panel-default locker-cluster" id="{{ $locker_cluster->anchor_name }}">
        <div class="panel-heading">{{{ $locker_cluster->name }}}</div>
        <div class="lockers">
          <table class="table table-bordered lockers-table">
            <?php
              $lockers = $locker_cluster->lockers;
              $maximum_columns = $lockers->totalColumns();
              $maximum_rows    = $lockers->totalRows();
            ?>
            @for ($row = 0; $row <= $maximum_rows; ++$row)
              <tr>
                @for ($column = 0; $column <= $maximum_columns; ++$column)
                  @if ($locker = $lockers->at($row, $column))
                    <?php $locker = $locker->getPresenter(); ?>
                    <td class="{{{ $locker->{$locker->isOwnedBy(Auth::user()) ? 'owner_css_class' : 'css_class'} }}}" id="{{{ $locker->anchor_id }}}">
                      <h4>{{{ $locker->name }}}</h4>
                      @if ($locker->isOwnedBy(Auth::user()))
                        <a href="#" class="btn btn-default btn-sm btn-block disabled">Owned</a>
                      @elseif ($locker->is_vacant && ! $locker->canBeClaimedBy(Auth::user()))
                        <a href="#" class="btn btn-success btn-sm btn-block disabled">Available</a>
                      @else
                        {{ $locker->status_action }}
                      @endif
                      @if (Auth::user()->is_admin)
                        @if ( ! $locker->is_taken)
                          @if ($locker->is_reserved)
                            <a href="{{{ action('LockersController@putCancelReservation', $locker->id) }}}" class="btn btn-warning btn-xs btn-block" data-method="put">Cancel</a>
                          @else
                            <a href="{{{ action('LockersController@putReserve', $locker->id) }}}" class="btn btn-warning btn-xs btn-block" data-method="put">Reserve</a>
                          @endif
                        @endif
                      @endif
                    </td>
                  @else
                    <td></td>
                  @endif
                @endfor
              </tr>
            @endfor
          </table>
        </div>
      </div>
    @endforeach
  @endforeach
@stop