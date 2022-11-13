<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Check URL Application</title>
    <style>
        .container {
            width: 500px;
            padding: 7rem 0rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Check URL Application</h3>
        <div>
            Upload a csv file to download a result
            <br>
            Upload file:
        </div>
        <br>
        <div>
            <form id="myForm" method="post" action="{{ route('store') }}" enctype="multipart/form-data">
                @csrf
                <div class="input-group mb-3">
                    <input type="file" name="csv_file" class="form-control csv" id="uploadCaptureInputFile" required>
                    <span class="input-group-text" id="basic-addon2">Upload</span>
                </div>
                @if (session()->get('error'))
                    <p class="error" style="color: brown">{{ session()->get('error') }}</p>
                @endif
                @if (session()->get('csv'))
                    <p class="error" style="color: brown">{{ session()->get('csv') }}</p>
                @endif
                <input type="submit" name="submit" value="Submit">
                <input type="reset" name="reset" onclick="reset" value="Reset">
            </form>
        </div>
        <br>
       
    </div>

</body>
</html>
