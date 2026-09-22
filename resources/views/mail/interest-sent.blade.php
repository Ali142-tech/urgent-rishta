<p>Hello,</p>
<p>You have just received a rishta interest from <a href="https://urgentrishta.co/member/profile/{{ $sender->dataid }}">{{ $sender->getFullName() }}</a>. Click <a href="https://urgentrishta.co/member/profile/listing/interests">here</a> to respond.</p>
@if(!empty($showUpgradeNote))
<p>This interest came from one of our Royal members. Please upgrade your package to Royal to get the most out of a match like this.</p>
@endif
<p>Thanks,<br>Urgent Rishta Admin</p>
