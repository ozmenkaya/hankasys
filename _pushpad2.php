<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <button id="notifications-button">Subscribe</button>
    <script src="https://pushpad.xyz/service-worker.js"></script>
    <script>
        
        (function(p,u,s,h,x){p.pushpad=p.pushpad||function(){(p.pushpad.q=p.pushpad.q||[]).push(arguments)};h=u.getElementsByTagName('head')[0];x=u.createElement('script');x.async=1;x.src=s;h.appendChild(x);})(window,document,'https://pushpad.xyz/pushpad.js');

        pushpad('init', 8429);


        document.querySelector('#notifications-button').addEventListener('click', function() {
            console.log("dd")
            pushpad('subscribe', function (isSubscribed) {
                console.log(isSubscribed)
                if (isSubscribed) {
                    alert("Thanks! You have successfully subscribed to notifications.");
                } else {
                    alert("You have blocked the notifications from browser preferences.");
                }
            });
        });
    </script>
</body>
</html>