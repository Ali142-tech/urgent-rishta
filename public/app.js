window._ = require('lodash');
window.$ = window.jQuery = require('jquery');
require('bootstrap-sass');

var notifications = [];
const NOTIFICATION_TYPES = {
    follow: 'App\\Notifications\\UserFollowed',
    interest_sent: 'App\\Notifications\\InterestSent',
    interest_accepted: 'App\\Notifications\\InterestAccepted',
    interest_declined: 'App\\Notifications\\InterestDeclined',
};
function refreshNotifications() {
    $.get('/member/profile/notifications/refresh', function (data) {
        addNotifications(data, "#notifications");
    });
}
function addNotifications(newNotifications, target) {
    notifications = _.concat(notifications, newNotifications);
    // show only last 5 notifications
    notifications.slice(0, 5);
    showNotifications(notifications, target);
}
function showNotifications(notifications, target) {
    if(notifications.length) {
        $('.noti_counter').show();
        $('.noti_counter').html(notifications.length);
        var htmlElements = notifications.map(function (notification) {
            return makeNotification(notification);
        });
        $(target + 'Menu').html(htmlElements.join(''));
        $(target).addClass('has-notifications')
    } else {
        $('.noti_counter').hide();
        $('.noti_counter').html("");
        $(target + 'Menu').html('<li class="dropdown-header">No notifications</li>');
        $(target).removeClass('has-notifications');
    }
}
// Make a single notification string
function makeNotification(notification) {
    return '<li><a href="' + routeNotification(notification) +
        '" target="_blank" onclick="javascript:refreshNotifications();">' +
        makeNotificationText(notification) + '</a></li>';
}

// get the notification route based on it's type
function routeNotification(notification) {
    var url = '?read=' + notification.id;
    var data;
    if (notification.data)
        data = notification.data;
    else data = notification;
    if (notification.type === NOTIFICATION_TYPES.follow) {
        url = '/member/profile/' + data.followerid + '/' + url;
    } else if (notification.type === NOTIFICATION_TYPES.interest_sent) {
        url = '/member/profile/' + data.senderid + '/' + url;
    } else if (notification.type === NOTIFICATION_TYPES.interest_accepted) {
        url = '/member/profile/' + data.receiverid + '/' + url;
    } else if (notification.type === NOTIFICATION_TYPES.interest_declined) {
        url = '/member/profile/' + data.receiverid + '/' + url;
    }
    return Laravel.root + url;
}

// get the notification text based on it's type
function makeNotificationText(notification) {
    var text = '';
    var data;
    if (notification.data)
        data = notification.data;
    else data = notification;
    if (notification.type === NOTIFICATION_TYPES.follow) {
        text += '<strong>' + data.follower + '</strong> is following you now';
    } else if (notification.type === NOTIFICATION_TYPES.interest_sent) {
        text += '<strong>' + data.sender + '</strong> is interested in you';
    } else if (notification.type === NOTIFICATION_TYPES.interest_accepted) {
        text += '<strong>' + data.receiver + '</strong> has accepted your interest';
    } else if (notification.type === NOTIFICATION_TYPES.interest_declined) {
        text += '<strong>' + data.receiver + '</strong> has declined your interest';
    }
    return text;
}

window.Pusher = require('pusher-js');
import Echo from "laravel-echo";

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'd09d09fa3cc86a06d161',
    cluster: 'ap1',
    forceTLS: true,
    auth: {
        headers: {
            Authorization: 'Bearer ' + Laravel.token,
            Accept: 'application/json',
        },
    },
});

$(document).ready(function() {
    if(Laravel.userId) {
        refreshNotifications();

        window.Echo.private(`App.User.${Laravel.userId}`)
            .notification((notification) => {
                addNotifications([notification], '#notifications');
                showAlert('success', makeNotificationText(notification), 7000);
        });
    }
});
