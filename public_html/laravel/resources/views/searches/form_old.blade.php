<form id="SearchForm" action="{{route('searches.add')}}" method="POST" enctype="application/x-www-form-urlencoded" novalidate>
    <div data-response></div>
    @csrf
    <fieldset>
        <div class="form-group row">
            <label for="inputCity" class="col-sm-3 col-form-label">City</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="inputCity" min="3" name="city" placeholder="New York"
                       required
                       data-avg-price-url="{{route('city.avgprice',['city'=>'%placeholder%'])}}"
                       data-city-search-url="{{route('city.search')}}"
                >
                <div class="invalid-feedback">City is required. At least 3 characters.</div>
                <input type="hidden" name="city_id" min="3">
            </div>
        </div>
        <div class="form-group row">
            <label for="inputArea" class="col-sm-3 col-form-label">Area <button data-neighborhoods-clear type="button" class="btn btn-xs"><i class="fas fa-trash-alt"></i></button></label>
            <div class="col-sm-9">
                <select class="form-control" id="inputArea" name="neighborhood_ids[]" disabled multiple size="5">
                    <option value>Within the city</option>
                </select>
                <small class="form-text text-muted">Within the city is default</small>
            </div>
        </div>
        <div class="form-group row">
            <label for="inputCheckInDate" class="col-sm-3 col-form-label">Check-in Date</label>
            <div class="col-sm-4">
                <input type="date" class="form-control" id="inputCheckInDate" name="check_in_date" placeholder=""
                       required value="">
                <div class="invalid-feedback">Must be tomorrow or later</div>
            </div>
            <div class="col-sm-5">
                <div class="input-group mb-3">
                    <label for="inputNights" class="col-auto col-form-label">Duration</label>
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
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Adults</label>
            <div class="col-sm-4">
                <input type="number" class="form-control" name="number_of_adults"
                       required value="2">
            </div>
            <div class="col-sm-5">
                <div class="input-group mb-3">
                    <label class="col-auto col-form-label">Children</label>
                    <input type="number" class="form-control" name="has_children">
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Children Age</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" name="children"
                       placeholder="age, comma separated: 0,7,12">
            </div>
        </div>

        <div class="form-group row">

            <label for="inputHotelClass" class="col-sm-3 col-form-label">Hotel Class</label>
            <div class="col-sm-3">
                <select class="form-control" id="inputHotelClass" name="hotel_class">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3" selected>3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>

                {{--
                <input type="number" step="0.5" class="form-control" id="inputHotelClass" placeholder="1-5 stars"
                       name="hotel_class">
                       --}}
            </div>
            <div class="col-sm-6">
                {{--
                            <label for="inputRating" class="col-auto col-form-label">Review Rating</label>
            <div class="col-sm-1">
                <input type="number" step="0.1" min="1" max="10" class="form-control" id="inputRating" name="rating"
                       placeholder="1.0-10.0">
            </div>

                --}}
                <div class="input-group mb-3">
                <label for="inputRating" class="col-auto col-form-label">Review Rating</label>
                <select class="form-control" id="inputRating" name="rating">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7" selected>7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                </select>
                </div>
            </div>

        </div>
        <div class="progress mb-3">
            <div class="progress-bar bg-warning text-dark" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0"
                 aria-valuemax="100">50%
            </div>
        </div>
        <div class="form-group row justify-content-center">
                <button class="btn btn-success m-3" data-role="next" type="button">Next</button>
                <button class="btn btn-danger m-3" type="reset">Cancel</button>
        </div>
    </fieldset>
    <fieldset style="display:none">
        <h4>Send me offers of matched hotels when room rates are below:</h4>
        <div class="form-group row">
            <div class="col-xs-12 col-md-8 offset-md-2">
            <div class="input-group mb-3">
                {{--
                <div class="input-group-prepend">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" id="customBudgetTypeAmount" name="budget_type_amount" class="custom-control-input">
                            <label class="custom-control-label" for="customBudgetTypeAmount"></label>
                        </div>
                </div>
                --}}
                <input type="number" step="0.01" class="form-control" id="inputMaxBudget" placeholder=""
                       name="max_budget" aria-describedby="inputMaxBudgetHelp"
                       {{-- disabled --}}
                >
                <select class="form-control col-sm-3" id="inputMaxBudgetCurrency" name="max_budget_currency"
                        aria-describedby="inputMaxBudgetCurrencyAddon"
                        {{-- disabled --}}
                >
                    <option value="USD" selected>USD</option>
                    <option value="EUR">EUR</option>
                    <option value="GBP">GBP</option>
                </select>
                <div class="input-group-append">
                    <span class="input-group-text" id="inputMaxBudgetCurrencyAddon">per night</span>
                </div>
            </div>
            <small id="inputMaxBudgetHelp" class="form-text text-muted text-left">
                The average rate of your desired hotels is <span data-city-avg-price></span>
            </small>
            </div>
            </div>
        <div class="form group row mb-3">
            <div class="col-xs-12 col-md-8 offset-md-2">
            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="customRadionFrequencyAnytime" name="frequency" value="default" class="custom-control-input" checked required>
                <label class="custom-control-label" for="customRadionFrequencyAnytime">Anytime</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="customRadioFrequencyDaily" name="frequency" value="daily" class="custom-control-input">
                <label class="custom-control-label" for="customRadioFrequencyDaily">Once a day</label>
            </div>
            </div>
        </div>
        {{--
        <div class="form-group row">
            <div class="col-xs-12 col-md-8 offset-md-2">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" id="customBudgetTypeDiscount" name="budget_type_discount" class="custom-control-input">
                        <label class="custom-control-label" for="customBudgetTypeDiscount"></label>
                    </div>
                </div>
                <select disabled class="form-control col-sm-3" id="inputBudgetTypeDiscount" name="max_budget_discount"
                        aria-describedby="inputBudgetTypeDiscountAddon"
                >
                    <option value="30" selected>30%</option>
                    <option value="35">35%</option>
                    <option value="40">40%</option>
                    <option value="45">45%</option>
                    <option value="50">50%</option>
                    <option value="55">55%</option>
                    <option value="60">60%</option>
                    <option value="65">65%</option>
                    <option value="70">70%</option>
                    <option value="75">75%</option>
                    <option value="80">80%</option>
                    <option value="85">85%</option>
                    <option value="90">90%</option>
                </select>
                <div class="input-group-append">
                    <span class="input-group-text" id="inputBudgetTypeDiscountAddon">OFF regular price</span>
                </div>
            </div>
            </div>
        </div>
        --}}
        <div class="progress mb-3">
            <div class="progress-bar bg-success text-light" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0"
                 aria-valuemax="100">100%
            </div>
        </div>

        <div class="form-group row justify-content-center">
                <button class="btn btn-warning m-3" data-role="prev" type="button">Previous</button>
                <button class="btn btn-success m-3" type="submit">Submit</button>
                <button class="btn btn-danger m-3" type="reset">Cancel</button>
        </div>
    </fieldset>
    <fieldset style="display: none">
        <div class="alert alert-success">
        Your search has been saved. We will send you the list of matching hotel offers to your email address shortly.
        </div>
    </fieldset>
</form>

