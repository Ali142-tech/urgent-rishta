<p>Hello Team,</p>
<br/>
<p>You have received a new message from the Contact Us form on the website:</p>

<p>
    <strong>Name:</strong> {{ $data->sender }}<br/>
    <strong>Email:</strong> {{ $data->sender_email }}<br/>
    @if(!empty($data->sender_phone))
    <strong>Phone / WhatsApp:</strong> {{ $data->sender_phone }}<br/>
    @endif
    @if(!empty($data->sender_city))
    <strong>City:</strong> {{ $data->sender_city }}<br/>
    @endif
    <strong>Interested In:</strong> {{ $data->subject }}
</p>

<p><i>{{ $data->message }}</i></p>
<br/>
Thank You,
<br/>
Urgent Rishta Admin
