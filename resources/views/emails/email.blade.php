<!DOCTYPE html>
<html>
<head>
    <title>{{ $cus_subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f7f7f7;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        .footer {
            text-align: center;
            color: #888;
            margin-top: 20px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Contact Message</h2>
        <table>
            <tr>
                <th>Name</th>
                <td>{{ $cus_name }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $cus_email }}</td>
            </tr>
            <tr>
                <th>Phone</th>
                <td>{{ $cus_phone }}</td>
            </tr>
            <tr>
                <th>Subject</th>
                <td>{{ $cus_subject }}</td>
            </tr>
            <tr>
                <th>Message</th>
                <td>{{ $cus_message }}</td>
            </tr>
        </table>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Your Company. All rights reserved.</p>
        </div>
    </div>
</body>
</html>