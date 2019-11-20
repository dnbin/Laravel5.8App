@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">View Search Results #{{$search->id}} |
                        Entries: {{$search->entries->count()}}</div>

                    <div class="card-body">
                        <div data-response></div>
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-sm-3 col-xs-12">
                                <div class="card text-white bg-info mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Search Parameters</h5>
                                        <p class="card-text">
                                        <ul>
                                            <li>City: {{$search->city->name}}</li>
                                            @if(!empty($search->neighborhoods))
                                                <li>Neighborhoods: @foreach($search->neighborhoods as $neighborhood) {{$neighborhood->name}} @endforeach</li>
                                            @endif
                                            <li>Check-in Date: {{$search->check_in_date->toDateString()}}</li>
                                            <li>Nights: {{$search->nights}}</li>
                                            @if(!empty($search->hotel_class))
                                                <li>Hotel Class: {{$search->hotel_class}}</li>
                                            @endif
                                            @if(!empty($search->rating))
                                                <li>Rating: {{$search->rating}}</li>
                                            @endif
                                            @if(!empty($search->max_budget))
                                                <li>Max Budget: {{$search->max_budget}} {{$search->max_budget_currency}}</li>
                                            @endif
                                        </ul>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-9 col-xs-12">
                                @if($search->entries->isNotEmpty())
                                    @foreach($search->entries as $entry)

                                        <div class="card text-dark bg-light mb-3">
                                            <div class="card-header">
                                                Hotel Class: {{ $entry->star_rating }} @if(!empty($entry->review_score)) | Review Score: {{ $entry->review_score }} @endif | Price per Night: {{(int)$entry->price}} {{$entry->currency}} | Total Price: {{($entry->price*$search->nights)}} {{$entry->currency}} | <a
                                                        href="{{$entry->linkFormatting($search,$entry->feed)}}" target="_blank">{{$entry->title}}</a>
                                                @if(!empty($entry->saving_percentage) && $entry->saving_percentage>=$search->max_budget_discount)
                                                    <span class="bg-warning text-dark">| Saving %: {{$entry->saving_percentage}} {{$entry->currency}}</span>
                                                @else
                                                    @role('admin')
                                                        <small>No saving %</small>
                                                    @endrole
                                                @endif

                                                @role('admin')
                                                @if(!empty($entry->regular_room_rate_amount))
                                                    <span class="bg-warning text-dark">| Regular Room Rate: {{$entry->regular_room_rate_amount}} {{$entry->regular_room_rate_currency}}</span>
                                                @endif

                                                @endrole


                                                <span class="text-right">Feed: {{$entry->feed->name}}</span>
                                            </div>

                                            <div class="row no-gutters">
                                                <div class="col-sm-4 col-xs-4 col-md-2 col-lg-1">
                                                    <img src="{{$entry->image_link}}" class="card-img" alt="...">
                                                </div>
                                                <div class="col-sm-8 col-xs-8 col-md-10 col-lg-11">
                                                    <div class="card-body">
                                                        <h5 class="card-title">{{$entry->country}}, @if(!empty($entry->province_state)){{$entry->province_state}}, @endif{{$entry->city}}, {{$entry->zip_code}}, {{$entry->street_address}}</h5>
                                                        <p class="card-text">
                                                            {{$entry->description}}
                                                        </p>
                                                        @if(!empty($entry->custom_label_0))
                                                        <p class="card-text">
                                                            {{$entry->custom_label_0}}
                                                        </p>
                                                        @endif
                                                        @if(!empty($entry->custom_label_1))
                                                        <p class="card-text">
                                                            {{$entry->custom_label_1}}
                                                        </p>
                                                        @endif
                                                        <p class="card-text"><small class="text-muted">Last updated {{$entry->last_updated_at}}</small></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    @endforeach
                                    @else
                                    <div class="alert alert-warning">No offers found. Please check later.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{$search->unsubscribe_url}}">Unsubscribe</a> <small>Link valid 24h, till {{\Carbon\Carbon::now()->addDay()->toDateTimeString()}}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
