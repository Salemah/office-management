<!DOCTYPE html>
<html>
<head>
    <title>Wardan Tech Ltd</title>
</head>
<body>
    <h1>Wardan Tech Ltd</h1>
    <p> Added By : {{ $data['body'] }}</p>
    <p>Client : {{ $data['client'] }}</p>
    <p>Time : {{ $data['time'] }}</p>
    <p>Date : {{ $data['date'] }}</p>
    <p>Reminder Note  : {!! $data['details'] !!}</p>
</body>
</html>
