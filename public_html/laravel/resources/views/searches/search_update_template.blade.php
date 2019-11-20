<template id="search_update_template">
    <div class="row">
        <div class="col-7">

            <div class="card border-warning m-3">
                <div class="card-header bg-warning text-dark">
                    Update Search
                </div>
                <div class="card-body">
                    <form data-search-update-form action="" method="POST" enctype="application/x-www-form-urlencoded">
                        <div data-response></div>
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group row">
                                    <label class="col-6 col-form-label">City</label>
                                    <div class="col-6">
                                        <input type="text" class="form-control form-control-sm" name="city"
                                               data-city-search-url="{{route('city.search')}}"
                                               required
                                        >
                                        <input type="hidden" class="form-control form-control-sm" name="city_id" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-6 col-form-label">Area <button data-neighborhoods-clear type="button" class="btn btn-xs"><i class="fas fa-trash-alt"></i></button></label>
                                    <div class="col-6">
                                        <select class="form-control form-control-sm" name="neighborhood_ids[]" disabled multiple size="5">
                                        </select>
                                        <small class="form-text text-muted">Within the city is default</small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-6 col-form-label">Check-in Date</label>
                                    <div class="col-6">
                                        <input type="date" class="form-control form-control-sm" name="check_in_date"
                                               required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-6 col-form-label">Nights</label>
                                    <div class="col-6">
                                        <input type="number" class="form-control form-control-sm" name="nights"
                                               required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-6 col-form-label">Number Of Adults</label>
                                    <div class="col-6">
                                        <input type="number" class="form-control form-control-sm" name="number_of_adults"
                                               required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-6 col-form-label">Children</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control form-control-sm" name="children"
                                               placeholder="age, comma separated: 0,7,12">
                                    </div>
                                </div>

                            </div>
                            <div class="col-6">
                                <div class="form-group row">
                                    <label class="col-6 col-form-label">Hotel Class</label>
                                    <div class="col-6">
                                        <input type="number" step="0.5" class="form-control form-control-sm"
                                               placeholder="1-5 stars" name="hotel_class">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-6 col-form-label">Rating</label>
                                    <div class="col-6">
                                        <input type="number" step="0.1" class="form-control form-control-sm"
                                               name="rating" placeholder="1-5">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-6 col-form-label">Maximum Budget</label>
                                    <div class="col-3">
                                        <input type="number" step="0.01" class="form-control form-control-sm"
                                               name="max_budget">
                                    </div>
                                    <div class="col-3">
                                        <select class="form-control form-control-sm" name="max_budget_currency">
                                            <option value="USD" selected>USD</option>
                                            <option value="CAD">CAD</option>
                                            <option value="EUR">EUR</option>
                                            <option value="GBP">GBP</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form group row">
                                    <label class="col-6 col-form-label">Frequency</label>
                                    <div class="col-6">
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
                                                                    <label class="col-6 col-form-label">Maximum Budget Discount</label>
                                                                    <div class="col-6">
                                                                        <div class="input-group input-group-sm mb-3">
                                                                            <select class="form-control" name="max_budget_discount">
                                                                                <option value="">Choose Discount</option>
                                                                                <option value="30">30%</option>
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
                                                                                <span class="input-group-text" >OFF regular price</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                --}}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="btn btn-group">
                                <button class="btn btn-success" type="submit">Save</button>
                                <button class="btn btn-warning" type="reset">Reset</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

</template>
