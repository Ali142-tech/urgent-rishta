Hello Team,

You have received a new message from the Contact Us form on the website:

Name: {{ $data->sender }}
Email: {{ $data->sender_email }}
@if(!empty($data->sender_phone))
Phone / WhatsApp: {{ $data->sender_phone }}
@endif
@if(!empty($data->sender_city))
City: {{ $data->sender_city }}
@endif
Interested In: {{ $data->subject }}

{{ $data->message }}

Thank You,
Urgent Rishta Admin
