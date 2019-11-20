@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Search #{{$search->id}} Unsubscribe</div>
                    <div class="card-body">
                        <div class="card-text">
                            Hello, {{$search->user->name}}
                        </div>
                        <div class="card-text">
                            You have been unsubscribed from hotel search # {{$search->id}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
