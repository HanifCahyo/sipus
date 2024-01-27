@extends('layout')

@section('content')
    <!-- Your existing navigation code here -->

    <div id="layoutSidenav">    
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <!-- Your existing table code here -->

                    <!-- Form to add a new user -->
                    <div class="mt-4">
                        <h2>Add a New User</h2>
                        <form action="{{route('storeUser')}}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                                @if ($errors->has('name'))
                                          <span class="error"> * {{ $errors->first('name') }}</span>
                                          @endif
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" name="email" required>
                                @if ($errors->has('email'))
                                          <span class="error">* {{ $errors->first('email') }}</span>
                                        @endif
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                                @if ($errors->has('username'))
                                          <span class="error"> * {{ $errors->first('username') }}</span>
                                          @endif
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                @if ($errors->has('password'))
                                          <span class="error">* {{ $errors->first('password') }}</span>
                                        @endif
                            </div>
                            <!-- Add more fields as needed -->

                            <button type="submit" class="btn btn-primary">Add User</button>
                        </form>
                    </div>
                    <!-- End of the form -->
                </div>
            </main>
        </div>
    </div>
@endsection
