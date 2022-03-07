<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome to sweetshop {{ $user->name }}</title>
</head>
<body>
    <h3>Dear {{ $user->name }}</h3>
    <p>you have successfully registered in sweetshop with following email {{ $user->email }}</p>
</body>
</html>
