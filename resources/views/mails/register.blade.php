<!DOCTYPE html>
<html>

<head>
    <title>Registration</title>
</head>

<body style="padding: 20px;">
    <div align="center">
        <h1>
            <b>
                Welcome {{ $user->full_name ? $user->full_name: $user->name}},
            </b>
        </h1>
    </div>

    <p>
        Hello <b>{{ $user->full_name ? $user->full_name: $user->name}}</b>,
        <br>
        Thank you for joining INFAKT.
        <br>
        We’d like to confirm that your account was created successfully. To access [customer portal] click the link below.
        <br>
        [Link/Button]
        <br>
        <br>
        If you experience any issues logging into your account, reach out to us at [email address].
        <br>
        Best,
        <br>
        The INFAKT team
    </p>

</body>

</html>