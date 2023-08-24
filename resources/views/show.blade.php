@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

<table>

    <thead>
        <tr>
            <th class="first"></th>
            <th class="second">ID</th>
            <th class="third">Email</th>
            <th class="fourth">Name</th>
            <th class="fifth">Role</th>
            <th class="sixth"></th>
        </tr>
    </thead>

    <tbody class="noselect">
                <td class="first">
                </td>
                <td class="second">{{ $user->id }}</td>
                <td class="third">{{ $user->email }}</td>
                <td class="fourth">{{ $user->name }}</td>
                <td class="fifth">{{ $user->role }}</td>
                <td class="sixth">
                    <span class="dropdown-wrapper">
                        <li class="nav-item dropdown custom-dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                ...
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('users.edit', ['id' => $user->id]) }}">
                                    {{ __('Edit User') }}
                                </a>    
                            </div>
                        </li>
                    </span>
                </td>

    </tbody>

</table>

@endsection