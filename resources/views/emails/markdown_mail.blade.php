<x-mail::message>
# Welcome {{ $user->name}}

Kindly click to verify

<x-mail::button :url="route('verification.verify', ['id' => $user->id])">
Verify
</x-mail::button>


Thanks,<br>
kyle
</x-mail::message>
