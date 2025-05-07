<!DOCTYPE html>
<html>

<head>
    <title>CS 195 Portal Login Code</title>
    <style>
        .red {
            color: red;
        }

        .gray {
            color: #4f4f4f;
        }

        .bold {
            font-weight: bold;
        }

        .italic {
            font-style: italic;
        }
    </style>
</head>

<body>
    <h1>Login to Portalis</h1>

    <p>Your login PIN is: <strong class="bold">{{ $data['pin'] }}</strong></p>

    <p class="red">Do not share this PIN with anyone else.</p>

    <p class="gray italic">This is an automated email. Please do not reply.</p>
</body>

</html>