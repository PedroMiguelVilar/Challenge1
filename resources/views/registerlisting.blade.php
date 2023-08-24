@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/search.css') }}">


    <div class="wrapper">
        <div class="label">Search</div>
        <form action="{{ route('search') }}" method="GET" class="searchBar">
            <div class="input-group">
                <div class="search-input">
                    <input id="searchQueryInput" type="text" name="query" placeholder="Search" value=""
                        oninput="suggest()" />
                    <button id="searchQuerySubmit" type="submit" name="searchQuerySubmit">
                        <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                            <path fill="#666666"
                                d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z" />
                        </svg>
                    </button>
                </div>
                <div id="suggestionsDropdown"></div>
            </div>
        </form>
    </div>




    <nav>
        <h2>Users</h2>

        <div class="button-group">

            <div class="dropdown">
                <button>Menu</button>
                <div class="dropdown-content">

                    <form action="{{ route('export') }}" method="GET">
                        <input type="hidden" name="selected_users_csv" id="selected_users_csv">
                        <button class="dropdown-item" type="submit" name="action" onclick="exportSelected()">Export to
                            CSV</button>
                    </form>


                    <a class="dropdown-item" href="{{ route('export_all') }}">Export all to CSV</a>
                </div>
            </div>




            <form action="{{ route('users.bulkDelete') }}" method="POST" onsubmit="return confirmDelete();">
                @csrf
                @method('DELETE')
                <input type="hidden" name="selected_users_delete" id="selected_users_delete">
                <button class="primary-btn" type="submit" name="action" value="delete">Delete</button>
            </form>

        </div>
    </nav>

    <div>
        <label for="id-filter">ID:</label>
        <input type="text" id="id-filter">

        <label for="email-filter">Email:</label>
        <input type="text" id="email-filter" oninput="changeOrderBy()">

        <label for="cargo-filter">Cargo:</label>
        <select id="cargo-filter" onchange="filterTable()">
            <option value="">All</option>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>

        <label for="order-by">Order By:</label>
        <select id="order-by" onchange="changeOrderBy()">
            <option value="">None</option>
            <option value="name_asc">Name (Ascending)</option>
            <option value="name_desc">Name (Descending)</option>
        </select>


        <label for="results-per-page">Results per Page:</label>
        <select id="results-per-page" onchange="changeResultsPerPage()">
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
        </select>
    </div>



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
            @foreach ($users as $user)
                <tr onclick="toggleCheckbox(event, '{{ $user->id }}')">
                    <td class="first">
                        <input type="checkbox" name="selected_users[]" value="{{ $user->id }}" id="{{ $user->id }}"
                            onclick="toggleCheckbox(event, '{{ $user->id }}')">
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
                                    <a class="dropdown-item" href="{{ route('user', ['id' => $user->id]) }}"
                                        target="_blank">
                                        {{ __('More Info') }}
                                    </a>
                                </div>
                            </li>
                        </span>
                    </td>
                </tr>
            @endforeach

        </tbody>

    </table>


    <div class="pagination">
        {{ $users->appends(['resultsPerPage' => request('resultsPerPage')])->links() }}
    </div>

    @if (session('error'))
        <script>
            alert("{{ session('error') }}");
        </script>
    @endif



    <script>
        //Script to select multiple users

        var selectedusers = [];

        var lastSelectedCheckbox = null;

        function toggleCheckbox(event, userId) {
            const checkbox = document.getElementById(userId);

            if (event.shiftKey && lastSelectedCheckbox !== null) {
                const checkboxes = document.querySelectorAll('input[type="checkbox"]');
                const startIndex = Array.from(checkboxes).indexOf(lastSelectedCheckbox);
                const endIndex = Array.from(checkboxes).indexOf(checkbox);
                const [start, end] = [startIndex, endIndex].sort((a, b) => a - b);

                for (let i = start; i <= end; i++) {
                    const currentCheckbox = checkboxes[i];
                    currentCheckbox.checked = true;

                    const currenUserId = currentCheckbox.value;
                    if (!selectedusers.includes(currenUserId)) {
                        selectedusers.push(currenUserId);
                    }
                }
            } else {
                checkbox.checked = !checkbox.checked;

                if (checkbox.checked) {
                    if (!selectedusers.includes(userId)) {
                        selectedusers.push(userId);
                    }
                } else {
                    const index = selectedusers.indexOf(userId);
                    if (index !== -1) {
                        selectedusers.splice(index, 1);
                    }
                }
            }

            lastSelectedCheckbox = checkbox.checked ? checkbox : null;

            console.log(selectedusers);
        }



        function confirmDelete() {
            var confirmation = confirm("Do you want to delete the selected users?");

            if (confirmation) {
                updateForm('delete');
            }
        }

        function updateForm(action) {
            if (action === 'delete') {
                const inputField = document.getElementById('selected_users_delete');
                inputField.value = selectedusers.join(',');
            }
        }


        function exportSelected() {
            const inputField = document.getElementById('selected_users_csv');

            if (selectedusers.length === 0) {
                alert("No users selected to export.");
                inputField.value = '';
            } else {
                inputField.value = selectedusers.join(',');
            }
        }
    </script>



    <script>
        //Script to suggest a user name based on input
        function suggest() {
            const searchInput = document.querySelector('#searchQueryInput');
            const suggestionsDropdown = document.querySelector('#suggestionsDropdown');
            console.log(searchInput);
            const query = searchInput.value.trim();
            console.log(query);
            // Send an AJAX request to fetch suggested terms
            fetch(`/search/suggest?query=${query}`)
                .then(response => response.json())
                .then(suggestedTerms => {
                    // Clear previous suggestions
                    suggestionsDropdown.innerHTML = '';

                    if (query !== "" && suggestedTerms.length > 0) {
                        // Loop through the suggested terms and create dropdown items
                        suggestedTerms.forEach(term => {
                            const suggestionItem = document.createElement('div');
                            suggestionItem.classList.add('dropdown-item');
                            suggestionItem.textContent = term;

                            // Add click event listener to suggestion item
                            suggestionItem.addEventListener('click', () => {
                                // Set the clicked suggestion as the search query
                                searchInput.value = term;
                            });

                            suggestionsDropdown.appendChild(suggestionItem);
                        });

                        // Show the suggestions dropdown
                        suggestionsDropdown.style.display = 'block';
                    } else {
                        // Hide the suggestions dropdown when there are no suggested terms or the input value is empty
                        suggestionsDropdown.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Hide suggestions dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const searchInput = document.querySelector('#searchQueryInput');
            const suggestionsDropdown = document.querySelector('#suggestionsDropdown');

            if (event.target !== searchInput && event.target !== suggestionsDropdown) {
                suggestionsDropdown.style.display = 'none';
            }
        });
    </script>



    <script>
        //Script to filter
        var idFilterInput = document.getElementById("id-filter");
        idFilterInput.addEventListener("keyup", filterTable);

        var emailFilterInput = document.getElementById("email-filter");
        emailFilterInput.addEventListener("keyup", filterTable);

        function filterTable() {
            var idFilter = document.getElementById("id-filter").value.toLowerCase();
            var emailFilter = document.getElementById("email-filter").value.toLowerCase();
            var cargoFilter = document.getElementById("cargo-filter").value.toLowerCase();

            var rows = document.querySelectorAll("tbody tr");

            rows.forEach(function(row) {
                var id = row.querySelector(".second").textContent.toLowerCase();
                var emailData = row.querySelector(".third").textContent.toLowerCase();
                var cargo = row.querySelector(".fifth").textContent.toLowerCase();

                var displayRow = true;

                if (idFilter && id.indexOf(idFilter) === -1) {
                    displayRow = false;
                }

                if (emailFilter && emailData.indexOf(emailFilter) === -1) {
                    displayRow = false;
                }

                if (cargoFilter && cargo !== cargoFilter) {
                    displayRow = false;
                }

                if (displayRow) {
                    row.classList.remove("hidden");
                } else {
                    row.classList.add("hidden");
                }
            });
        }


        function changeResultsPerPage() {
            const resultsPerPage = document.getElementById("results-per-page").value;
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set("resultsPerPage", resultsPerPage);
            window.location.href = currentUrl.toString();
        }

        // Add this function to set the selected option based on the current URL parameter
        function updateResultsPerPageDropdown() {
            const resultsPerPage = new URLSearchParams(window.location.search).get('resultsPerPage');
            if (resultsPerPage) {
                document.getElementById("results-per-page").value = resultsPerPage;
            }
        }

        // Call the function when the page loads
        window.onload = updateResultsPerPageDropdown;
    </script>

    <script>
        //Script to order Names
        var orderBy = "";

        function changeOrderBy() {
            const orderByDropdown = document.getElementById("order-by");
            orderBy = orderByDropdown.value;
            applyOrder();
        }

        function applyOrder() {
            const rows = document.querySelectorAll("tbody tr");

            const idFilter = document.getElementById("id-filter").value.toLowerCase();
            const emailFilter = document.getElementById("email-filter").value.toLowerCase();
            const cargoFilter = document.getElementById("cargo-filter").value.toLowerCase();

            rows.forEach(function(row) {
                const id = row.querySelector(".second").textContent.toLowerCase();
                const emailData = row.querySelector(".third").textContent.toLowerCase();
                const cargo = row.querySelector(".fifth").textContent.toLowerCase();

                const displayRow =
                    (!idFilter || id.indexOf(idFilter) !== -1) &&
                    (!emailFilter || emailData.indexOf(emailFilter) !== -1) &&
                    (!cargoFilter || cargo === cargoFilter);

                if (!displayRow) {
                    row.classList.add("hidden");
                }
            });

            if (orderBy === "name_asc") {
                const sortedRows = Array.from(rows).filter(row => !row.classList.contains("hidden")).sort((a, b) => {
                    const nameA = a.querySelector(".fourth").textContent;
                    const nameB = b.querySelector(".fourth").textContent;
                    return nameA.localeCompare(nameB);
                });
                sortedRows.forEach(row => document.querySelector("tbody").appendChild(row));
            } else if (orderBy === "name_desc") {
                const sortedRows = Array.from(rows).filter(row => !row.classList.contains("hidden")).sort((a, b) => {
                    const nameA = a.querySelector(".fourth").textContent;
                    const nameB = b.querySelector(".fourth").textContent;
                    return nameB.localeCompare(nameA);
                });
                sortedRows.forEach(row => document.querySelector("tbody").appendChild(row));
            }
        }


        // Call this function when the page loads
        window.onload = function() {
            updateResultsPerPageDropdown();
            changeOrderBy(); // This will apply the default ordering
        };
    </script>
@endsection
