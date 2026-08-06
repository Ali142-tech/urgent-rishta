<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
            text-align: left;
        }
    </style>
</head>
<body>
    <h2>Profile Details</h2>
    <table>
        <tr>
            <th>Member ID</th>
            <td>{{ $member->dataid }}</td>
        </tr>
        <tr>
            <th>Age</th>
            <td>{{ date_diff(date_create($member->birthday), date_create('now'))->y }}</td>
        </tr>
        <tr>
            <th>Height</th>
            <td>{{ $member->height }}</td>
        </tr>
        <tr>
            <th>Religion</th>
            <td>{{ $member->lbl_religion }}</td>
        </tr>
        <tr>
            <th>Caste / Sect</th>
            <td>{{ $member->lbl_caste }}</td>
        </tr>
        <tr>
            <th>Mother Tongue</th>
            <td>{{ $member->lbl_mother_tongue }}</td>
        </tr>
        <tr>
            <th>Marital Status</th>
            <td>{{ $member->lbl_marital_status }}</td>
        </tr>
        <tr>
            <th>Education</th>
            <td>{{ $member->lbl_education }}</td>
        </tr>
        <tr>
            <th>Profession</th>
            <td>{{ $member->profession }}</td>
        </tr>
        <tr>
            <th>City</th>
            <td>{{ $member->lbl_city }}</td>
        </tr>
        <tr>
            <th>Location</th>
            <td>{{ $member->lbl_city }} {{ $member->lbl_con_of_residence }}</td>
        </tr>
    </table>
</body>
</html>
