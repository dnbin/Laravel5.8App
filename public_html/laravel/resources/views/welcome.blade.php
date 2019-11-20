@extends('layouts.app')
@section('content')
    <main class="account travel-plan">
        <div class="container">
            <h2>Enter your travel plan:</h2>
            <h5>Save on hotels without searching</h5>
            <div class="row justify-content-center">
                <div class="col-md-12 box">

                    <div class="row justify-content-center">
                        <div class="col-md-11">
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif
                            @include('searches.form')
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

@endsection
