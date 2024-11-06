<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <button id="push-btn">Click here</button>
    <script>
        const btn = document.getElementById('push-btn');
        btn.addEventListener('click', () => {
            Notification.requestPermissions().then(perm => {
                alert(perm);
            });
        });
    </script>
</body>

</html>