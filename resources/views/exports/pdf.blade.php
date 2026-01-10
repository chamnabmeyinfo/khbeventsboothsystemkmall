<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export - {{ ucfirst($type) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4e73df;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>KHB Booth System - {{ ucfirst($type) }} Export</h1>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
    </div>

    @if($type == 'booths')
    <table>
        <thead>
            <tr>
                <th>Booth Number</th>
                <th>Status</th>
                <th>Client</th>
                <th>Category</th>
                <th>Price</th>
                <th>User</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $booth)
            <tr>
                <td>{{ $booth->booth_number }}</td>
                <td>{{ $booth->getStatusLabel() }}</td>
                <td>{{ $booth->client ? $booth->client->company : 'N/A' }}</td>
                <td>{{ $booth->category ? $booth->category->name : 'N/A' }}</td>
                <td>${{ number_format($booth->price, 2) }}</td>
                <td>{{ $booth->user ? $booth->user->username : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @elseif($type == 'clients')
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Company</th>
                <th>Name</th>
                <th>Position</th>
                <th>Phone</th>
                <th>Booths Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $client)
            <tr>
                <td>{{ $client->id }}</td>
                <td>{{ $client->company ?? 'N/A' }}</td>
                <td>{{ $client->name }}</td>
                <td>{{ $client->position ?? 'N/A' }}</td>
                <td>{{ $client->phone_number ?? 'N/A' }}</td>
                <td>{{ $client->booths->count() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @elseif($type == 'bookings')
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Client</th>
                <th>Booths Count</th>
                <th>User</th>
                <th>Type</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $book)
            <tr>
                <td>{{ $book->id }}</td>
                <td>{{ $book->date_book->format('Y-m-d H:i:s') }}</td>
                <td>{{ $book->client ? $book->client->company : 'N/A' }}</td>
                <td>{{ count(json_decode($book->boothid, true) ?? []) }}</td>
                <td>{{ $book->user ? $book->user->username : 'N/A' }}</td>
                <td>{{ $book->type }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>
