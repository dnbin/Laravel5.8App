<form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="form-group row">
        <div class="col-md-12">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                                                <span class="input-group-text" id="name-addon"><i
                                                        class="fas fa-user"></i></span>
                </div>
                <input id="name" type="text"
                       class="form-control @error('name') is-invalid @enderror"
                       name="name"
                       value="{{ old('name') }}"
                       required
                       autocomplete="name"
                       autofocus
                       aria-label="Name"
                       aria-describedby="name-addon"
                       placeholder="Name"
                >
            </div>
            @error('name')
            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                        </span>
            @enderror
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-12">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                                                <span class="input-group-text" id="email_addon"><i
                                                        class="fas fa-user"></i></span>
                </div>
                <input id="email" type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       autocomplete="email"
                       placeholder="Email"
                       aria-label="Email"
                       aria-describedby="email-addon">
            </div>
            @error('email')
            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
            @enderror
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-12">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                                                <span class="input-group-text" id="password_addon"><i
                                                        class="fas fa-lock"></i></span>
                </div>
                <input id="password" type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       name="password" required autocomplete="new-password"
                       placeholder="Password"
                       aria-label="Password"
                       aria-describedby="password_addon"
                >
            </div>
            @error('password')
            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
            @enderror
        </div>
    </div>

    <div class="checkbox mb-2 mr-sm-3">
        <input type="checkbox" id="city">
        <label class="label" for="city">I agree to XXXX Conditions of Use and Privacy
            Notice</label>
    </div>
    <button type="submit" class="btn btn-primary">Create Account</button>
</form>

