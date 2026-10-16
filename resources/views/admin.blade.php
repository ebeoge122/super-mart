<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Super Mart') }} Admin</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
<h1>Upload New Products here</h1>

<div class="login-form">
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert alert-danger">{{ $errors->first() }}</div>
@endif
        <h5 style="text-align:center">Upload product images and excel files here</h5>
    <form method="POST" action="{{ route('admin.upload') }}" enctype="multipart/form-data" class="formm">
        @csrf
            
            <label for="excel">Upload Excel Sheet:</label>
            <input type="file" name="excel" id="excel" value="{{ old('excel') }}" class="input-text" required accept=".xlsx, .xls, .csv" >

            <label for="images">Upload Product Images (optional, you may select multiple):</label>
            <input type="file" name="images[]" id="images" value="{{ old('images') }}" class="input-text" accept="image/*" multiple>

            <button type="submit" class="input-button">Upload Products</button>
        </form>

        <h4 class="fh2" style="text-align: center;">
            Maximum file size is 5mb </h4>

        <p style="text-align: center;">
            <a href="{{ asset('templates/sample_excel_supermart.xlsx') }}" download>Download the Excel template</a>
        </p>
    </div>


</body>
</html>
