<?php 
require_once "include/db.php";
require_once 'vendor/autoload.php';

Pushpad\Pushpad::$auth_token = 'HKrcikYp0417ok985Da3ZqTCPv8VpuMPWUEAFOjA';
Pushpad\Pushpad::$project_id = 8429; # set it here or pass it as a param to methods later

$notification = new Pushpad\Notification(array(
    # required, the main content of the notification
    'body' => "Hello world!",
    # optional, the title of the notification (defaults to your project name)
    'title' => "Website Name",
    # optional, open this link on notification click (defaults to your project website)
    'target_url' => "https://panel.hankasys.com",
    # optional, the icon of the notification (defaults to the project icon)
    'icon_url' => "https://panel.hankasys.com/dosyalar/logo/logo-icon-33439.svg",
    # optional, the small icon displayed in the status bar (defaults to the project badge)
    'badge_url' => "https://panel.hankasys.com/dosyalar/logo/logo-icon-33439.svg",
    # optional, an image to display in the notification content
    # see https://pushpad.xyz/docs/sending_images
    'image_url' => "https://panel.hankasys.com/dosyalar/logo/logo-icon-33439.svg",
    # optional, drop the notification after this number of seconds if a device is offline
    'ttl' => 604800,
    # optional, prevent Chrome on desktop from automatically closing the notification after a few seconds
    'require_interaction' => true,
    # optional, enable this option if you want a mute notification without any sound
    'silent' => false,
    # optional, enable this option only for time-sensitive alerts (e.g. incoming phone call)
    'urgent' => false,
    # optional, a string that is passed as an argument to action button callbacks
    'custom_data' => "123",
    # optional, add some action buttons to the notification
    # see https://pushpad.xyz/docs/action_buttons
    /*
    'actions' => array(
        array(
            'title' => "My Button 1",
            'target_url' => "https://example.com/button-link", # optional
            'icon' => "https://example.com/assets/button-icon.png", # optional
            'action' => "myActionName" # optional
        )
    ),
    */
    # optional, bookmark the notification in the Pushpad dashboard (e.g. to highlight manual notifications)
    'starred' => true,
    # optional, use this option only if you need to create scheduled notifications (max 5 days)
    # see https://pushpad.xyz/docs/schedule_notifications
    //'send_at' => strtotime('2016-07-25 10:09'), # use a function like strtotime or time that returns a Unix timestamp

    # optional, add the notification to custom categories for stats aggregation
    # see https://pushpad.xyz/docs/monitoring
    //'custom_metrics' => array('examples', 'another_metric') # up to 3 metrics per notification
));


# deliver to a user
//$notification->deliver_to('tekin.polat.dpu@gmail.com');

# deliver to a group of users
#$notification->deliver_to($user_ids);

# deliver to some users only if they have a given preference
# e.g. only $users who have a interested in "events" will be reached
#$notification->deliver_to($users, ["tags" => ["events"]]);

# deliver to segments
# e.g. any subscriber that has the tag "segment1" OR "segment2"
#$notification->broadcast(["tags" => ["segment1", "segment2"]]);

# you can use boolean expressions 
# they must be in the disjunctive normal form (without parenthesis)
#$notification->broadcast(["tags" => ["zip_code:28865 && !optout:local_events || friend_of:Organizer123"]]);
#$notification->deliver_to($users, ["tags" => ["tag1 && tag2", "tag3"]]); # equal to "tag1 && tag2 || tag3"

# deliver to everyone
$notification->broadcast(); 
