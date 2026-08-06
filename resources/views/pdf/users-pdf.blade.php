<!DOCTYPE html>
<html>
<head>
    <title>User Data Export</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>User Data Export</h2>
    <table>
        <thead>
            <tr>
                <th>Data ID</th>
                <th>Gender</th>
                <th>Birthday</th>
                <th>Height</th>
                <th>City</th>
                <th>Religion</th>
                <th>Caste</th>
                <th>Mother Tongue</th>
                <th>Marital Status</th>
                <th>Education</th>
                <th>Profession</th>
                <th>Country of Residence</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <td>{{ $user->dataid }}</td>
                <td>{{ $user->gender }}</td>
                <td>{{ $user->birthday }}</td>
                <td>{{ $user->height }}</td>
                <td>{{ $user->city }}</td>
                <td>{{ $user->religion }}</td>
                <td>{{ $user->caste }}</td>
                <td>{{ $user->mother_tongue }}</td>
                <td>{{ $user->marital_status }}</td>
                <td>{{ $user->education }}</td>
                <td>{{ $user->profession }}</td>
                <td>{{ $user->con_of_residence }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
