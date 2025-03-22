@extends('layouts.admin-app')
@section('page-title')
    {{ $pageTitle }}
@endsection
@push('css-page')
    <!-- Include the Select2 CSS (usually in the <head> section) -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .custom-input {
            border: none;
            outline: none;
            background-color: transparent;
            box-shadow: none;
            padding: 5px;
            font-size: 16px;
        }
        .custom-input:focus {
            border: none;
            outline: none;
            box-shadow: none;
        }
    </style>
@endpush
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('home') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="">{{ __('ISO Systems') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#"> {{ $pageTitle }} </a>
        </li>
    </ul>
@endsection
@php
    
    $placeholder="------------------------------------";
    $placeholder2="-----------------------------------------------------------------------";
@endphp
@section('content')
    <div class="row">
        <div class="col-sm-12">

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="email-body">

                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center g-2">
                                        <div class="col">
                                            <h5>
                                                {{ $pageTitle }}
                                            </h5>
                                        </div>

                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-container">
                                        <h2 class="p-2 text-center">طلب نسخ وثائق إضافية</h2>
                                        <hr>
                                        <!-- General Information -->
                                        <div class="row">
                                            <div class="form-group col-6">
                                                <label class="fs-3 form-label" for="requesting_party">- الجهة الطالبة:</label>
                                                <input type="text" id="requesting_party" name="requesting_party"  placeholder="{{ $placeholder2}}" class="form-control " ><br>
                                            </div>  

                                            <div class="form-group col-4">
                                                <label class="fs-3 form-label mx-3" for="date">- التاريخ:</label>
                                                <input type="date" class="form-control" id="date" name="date"><br><br>
                                            </div>  
                                            
                                        </div>

                                        <!-- Dynamic Table -->
                                        <h4>- بيانات النسخ الإضافية المطلوبة من الوثائق :</h4>
                                        <table id="documentsTable" class="table table-border">
                                            <thead>
                                                <tr>
                                                    <th>م</th>
                                                    <th>اسم الوثيقة</th>
                                                    <th>الرقم الكودي</th>
                                                    <th>عدد النسخ</th>
                                                    <th>أسماء حائزي هذه النسخ</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Rows will be added dynamically here -->
                                            </tbody>
                                        </table>
                                        <button class="btn btn-add btn-success" onclick="addRow()">+</button><br><br>
                                        <hr>
                                        <!-- Additional Details -->
                                        <label class="fs-3 form-label" for="reason">أسباب الطلب:</label><br>
                                        <textarea id="reason" name="reason" rows="4" class="form-control" cols="50"></textarea><br>
                                        <div class="row">
                                            <div class="form-group col-4">
                                                <label class="fs-3 form-label" for="name">الاسم:</label>
                                                <input type="text" id="name" class="form-control" name="name" placeholder="{{ $placeholder}}"><br>
                                            </div>

                                            <div class="form-group col-4">
                                                <label class="fs-3 form-label" for="signature">التوقيع:</label>
                                                <input type="text" id="signature" class="form-control"  name="signature" placeholder="{{ $placeholder}}"><br>
                                            </div>
                                            
                                            <div class="form-group col-4">
                                                <label class="fs-3 form-label" for="job_title">الوظيفة:</label>
                                                <input type="text" id="job_title" class="form-control" name="job_title" placeholder="{{ $placeholder}}"><br>
                                            </div>

                                        </div>
                                        <hr>
                                        <label class="fs-3 form-label" for="reason"> :</label><br>
                                        <textarea id="reason" name="reason" rows="4" class="form-control" cols="50"></textarea><br>
                                        <div class="row">
                                            <div class="form-group col-6">
                                                <label class="fs-3 form-label" for="name">الاسم:</label>
                                                <input type="text" id="name" class="form-control" name="name" placeholder="{{ $placeholder}}"><br>
                                            </div>

                                            <div class="form-group col-6">
                                                <label class="fs-3 form-label" for="signature">التوقيع:</label>
                                                <input type="text" id="signature" class="form-control"  name="signature" placeholder="{{ $placeholder}}"><br>
                                            </div>                  
                                        </div>
                                        <!-- Submit Button -->
                                        <button class="btn" onclick="submitForm()">Submit</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection
<script>
    let rowCount = 1;

    // Function to add a new row to the table
    function addRow() {
        const tableBody = document.querySelector("#documentsTable tbody");
        const newRow = document.createElement("tr");

        newRow.innerHTML = `
            <td>${rowCount}</td>
            <td><input type="text" class="form-control"  name="document_name[]"></td>
            <td><input type="text" class="form-control" name="code_number[]"></td>
            <td><input type="number" class="form-control" name="copies_count[]"></td>
            <td><input type="text" class="form-control" name="holders_names[]"></td>
            <td><button class="btn btn-remove btn-danger" onclick="removeRow(this)">-</button></td>
        `;

        tableBody.appendChild(newRow);
        rowCount++;
    }

    // Function to remove a row from the table
    function removeRow(button) {
        const row = button.closest("tr");
        row.remove();
        updateRowNumbers();
    }

    // Function to update row numbers after deletion
    function updateRowNumbers() {
        const rows = document.querySelectorAll("#documentsTable tbody tr");
        rowCount = 1;
        rows.forEach(row => {
            row.querySelector("td:first-child").textContent = rowCount;
            rowCount++;
        });
    }

    // Function to collect form data and send it to the server
    function submitForm() {
        const formData = {
            date: document.getElementById("date").value,
            requesting_party: document.getElementById("requesting_party").value,
            reason: document.getElementById("reason").value,
            name: document.getElementById("name").value,
            signature: document.getElementById("signature").value,
            job_title: document.getElementById("job_title").value,
            documents: []
        };

        // Collect document data from the table
        const rows = document.querySelectorAll("#documentsTable tbody tr");
        rows.forEach(row => {
            const documentName = row.querySelector("input[name='document_name[]']").value;
            const codeNumber = row.querySelector("input[name='code_number[]']").value;
            const copiesCount = row.querySelector("input[name='copies_count[]']").value;
            const holdersNames = row.querySelector("input[name='holders_names[]']").value;

            formData.documents.push({
                document_name: documentName,
                code_number: codeNumber,
                copies_count: copiesCount,
                holders_names: holdersNames
            });
        });

        // Send data to the server (using fetch API)
        fetch("/store-document-request", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}" // Laravel CSRF token
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                alert("Form submitted successfully!");
                console.log(data);
            })
            .catch(error => {
                console.error("Error:", error);
            });
    }
</script>
