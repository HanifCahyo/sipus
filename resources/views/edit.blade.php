@extends('layout')

@section('content')
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand" href="#"></a>
        
        <ul class="navbar-nav ms-auto ml-md-0 d-flex">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="false" aria-expanded="false">
                    <i class="fas fa-user fa-fw"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="{{ url('logout') }}">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <div id="layoutSidenav">    
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <!-- Edit Form -->
                    <div class="row mt-4">
                        <div class="col-md-6 offset-md-3">
                            <h2>Edit User</h2>
                            <form method="POST" action="{{ route('updateUser', ['id' => $user->id]) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="{{ $user->username }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" value="{{ $user->password }}" required>
                                    <button type="button" class="btn btn-outline-secondary" id="showPasswordBtn">Show</button>
                                </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Update User</button>
                            </form>
                        </div>
                    </div>
                    <!-- End Edit Form -->
                </div>
            </main>
        </div>
    </div>
@endsection
