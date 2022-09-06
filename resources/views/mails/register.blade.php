<!DOCTYPE html>
<html>

<head>
    <title>Registration</title>
</head>

<body style="padding: 20px;">
    <div align="center">
        <h1>
            <b>
                Welcome &nbsp; {{ $user->full_name ? $user->full_name: $user->name}},
            </b>
        </h1>
    </div>
    To,
    <br>
    <h1>Infakt,</h1>
    <br>
    340 JJ road, Warden house, Byculla,
    Mumbai 400008.

</body>

</html>