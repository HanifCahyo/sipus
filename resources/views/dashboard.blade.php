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
                    <!-- Tabel Data -->
                    <div class="table-responsive">
                        <table class="table table-striped mt-4">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Username (Enkripsi)</th>
                                    <th scope="col">Password (Enkripsi)</th>
                                    <th scope="col">Username (Plaintext)</th>
                                    <th scope="col">Password (Plaintext)</th>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->password }}</td>
                                        <td>{{ $user->encrypted_username }}</td>
                                        <td>{{ $user->encrypted_password }}</td>
                                        <td>
                                            <a href="{{ route('editUser', ['id' => $user->id]) }}" class="btn btn-primary btn-sm">Edit</a>
                                            <a href="{{ route('deleteUser', ['id' => $user->id]) }}" class="btn btn-danger btn-sm">Hapus</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Akhir Tabel Data -->
                </div>
            </main>
        </div>
    </div>
@endsection
