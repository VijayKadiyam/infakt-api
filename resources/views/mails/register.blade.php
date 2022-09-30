<!DOCTYPE html>
<html>

<head>
    <title>Registration</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>

<body style="padding: 20px;">
    <?php
    $login_link = "http://infakt.aaibuzz.com/auth/login";
    ?>
    <div align="center" style="color:#0023f5">
        <h1>
            <b>
                Welcome {{ $user->full_name ? $user->full_name: $user->name}} !
            </b>
        </h1>
    </div>

    <p style="text-align: justify; font-family: Roboto;">
        Hello <b>{{ $user->full_name ? $user->full_name: $user->name}}</b>,
        <br>
        Thank you for joining INFAKT.
        <br>
        We're excited to have you get started. First, you need to confirm your account. Just press the button below to confirm your account.
        <br>
        Kindly use the default password as " <b>123456</b> " to login. Once logged in you will be prompted to reset your password. It's mandatory to reset your default password.
        <br>
        <br>
        <a href="{{$login_link}}" style="text-decoration: none;"><button class="button-10" role="button" style="display: flex;
        flex-direction: column;
        align-items: center;
        padding: 6px 14px;
        font-family: -apple-system, BlinkMacSystemFont, 'Roboto', sans-serif;
        border-radius: 6px;
        border: none;
        color: #fff;
        background: linear-gradient(180deg, #0023f5 0%, #367AF6 100%);
        background-origin: border-box;
        box-shadow: 0px 0.5px 1.5px rgba(54, 122, 246, 0.25), inset 0px 0.8px 0px -0.25px rgba(255, 255, 255, 0.2);
        user-select: none;
        -webkit-user-select: none;
        touch-action: manipulation;">INFAKT PORTAL</button></a>
        <br>
    </p>
    <p>You received this email because you just signed up for a new account at <a href="http://infakt.aaibuzz.com">Infakt</a></p>
    <br>
    If you experience any issues logging into your account, reach out to us at it@infakt.co.in.
    <br>
    Cheers,
    <br>
    <b>The INFAKT team</b>
    <br>
    <br>

    <div align="center">
        <a target="_blank" href="http://infakt.aaibuzz.com"><img src="http://infakt.aaibuzz.com/img/logo.73a58166.png" alt width="125"></a>
    </div>
</body>

</html>