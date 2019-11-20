<form id="SearchForm" action="{{route('searches.add')}}" method="POST" enctype="application/x-www-form-urlencoded" novalidate>
    <div data-response></div>
    @csrf

    <fieldset>
    <div class="form-inline first-block">
        <div class="input-group mb-3 mr-sm-3">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
            </div>
            <input type="text" class="form-control" id="inputCity" min="3" name="city" placeholder="New York"
                   required
                   data-avg-price-url="{{route('city.avgprice',['city'=>'%placeholder%'])}}"
                   data-city-search-url="{{route('city.search')}}"
            >
            <div class="invalid-feedback">City is required. At least 3 characters.</div>
            <input type="hidden" name="city_id" min="3">
        </div>
        <div class="input-group mb-3 mr-sm-3">
            <div class="input-group-prepend">
                <span class="input-group-text">Check-In Date</span>
            </div>
            <input type="date" class="form-control" id="inputCheckInDate" name="check_in_date" placeholder=""
                   required value="">
        </div>
        <div class="invalid-feedback">Must be tomorrow or later</div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">Duration</span>
            </div>
            <input type="number"
                   class="form-control"
                   placeholder=""
                   aria-label="Duration"
                   aria-describedby="inputNightsAddon"
                   name="nights"
                   id="inputNights"
                   required
                   value="2"
            >
            <div class="input-group-append">
                <span class="input-group-text" id="inputNightsAddon">nights</span>
            </div>
        </div>
        <div class="invalid-feedback">Should be 1 night or more</div>

    </div>
    <div class="form-inline second-block">
        <div class="input-group mb-3 mr-sm-3">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
            </div>
            <select class="form-control" name="number_of_adults" required>
                <option value="" selected disabled>Number of Adults:</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>
        <div class="input-group mb-3 mr-sm-3">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
            </div>
            <select class="form-control" name="has_children">
                <option value="" selected disabled>Number of Children:</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>
        <div class="input-group mb-3 mr-sm-3">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-birthday-cake"></i></span>
            </div>
            <input type="text" class="form-control" name="children"
                   placeholder="age, comma separated: 0,7,12">
        </div>
    </div>
    <hr>

    <div class="notify">
        <p>Neighborhoods (select up to 3): <button data-neighborhoods-clear type="button" class="btn btn-xs"><i class="fas fa-trash-alt"></i></button></p>
    </div>
    <div class="third-block mb-2">
            <select class="form-control" id="inputArea" name="neighborhood_ids[]" disabled multiple size="5">
                <option value>Within the city</option>
            </select>
            <small class="form-text text-muted">Within the city is default</small>
    </div>

    <div class="btn-group" role="group" aria-label="Basic example">
        <button class="btn btn-primary" data-role="next" type="button">Next</button>
{{--        <button class="btn btn-danger" type="reset">Cancel</button> --}}

    </div>
    </fieldset>
    <fieldset style="display:none">
        <div class="form-inline first-block">
            <div class="input-group mb-3 mr-sm-2">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-star"></i></span>
                </div>
                <select class="form-control" id="inputHotelClass" name="hotel_class">
                    <option value="" selected disabled>Hotel Class</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-comments"></i></span>
                </div>
                <select class="form-control" id="inputRating" name="rating">
                    <option value="" selected disabled>Review Rating:</option>
                    <option value="1">Review Rating - 1</option>
                    <option value="2">Review Rating - 2</option>
                    <option value="3">Review Rating - 3</option>
                    <option value="4">Review Rating - 4</option>
                    <option value="5">Review Rating - 5</option>
                    <option value="6">Review Rating - 6</option>
                    <option value="7">Review Rating - 7</option>
                    <option value="8">Review Rating - 8</option>
                    <option value="9">Review Rating - 9</option>
                    <option value="10">Review Rating - 10</option>
                </select>
            </div>
        </div>

        <div class="price-alert">
            <p>Note: The average rate of your desired hotels is <span data-city-avg-price></span></p>
        </div>

        <div class="notify">
            <p>Notify me when hotel rates drop below:</p>
        </div>
        <div class="form-inline second-block">
            <div class="input-group mb-3 mr-sm-2">
                <div class="input-group-prepend">
                    <input type="number" step="0.01" class="form-control" id="inputMaxBudget" placeholder="Amount"
                           name="max_budget" aria-describedby="inputMaxBudgetHelp"
                        {{-- disabled --}}
                    >
                </div>
                <select class="form-control" id="inputMaxBudgetCurrency" name="max_budget_currency"
                        aria-describedby="inputMaxBudgetCurrencyAddon"
                    {{-- disabled --}}
                >
                    <option value="USD" selected>USD</option>
                    <option value="EUR">EUR</option>
                    <option value="GBP">GBP</option>
                </select>
                <span>per night</span>
            </div>
        </div>
        <div class="form-inline third-block mb-3">

            <div class="radio mb-2 mr-sm-3">
                <input type="radio" id="customRadionFrequencyAnytime" name="frequency" value="default" class="custom-control-input" checked required>
                <label class="custom-control-label" for="customRadionFrequencyAnytime">Anytime</label>
            </div>
            <div class="radio mb-2 mr-sm-3">
                <input type="radio" id="customRadioFrequencyDaily" name="frequency" value="daily" class="custom-control-input">
                <label class="custom-control-label" for="customRadioFrequencyDaily">Once a day</label>
            </div>

        </div>

        <div class="btn-group" role="group" aria-label="Basic example">
            <button class="btn btn-primary" data-role="prev" type="button">Previous</button>
            <button class="btn btn-primary" type="submit">Submit</button>
        </div>

    </fieldset>
    <fieldset style="display: none">
        <div class="alert alert-success">
            Your search has been saved. We will send you the list of matching hotel offers to your email address shortly.
        </div>
    </fieldset>
</form>

