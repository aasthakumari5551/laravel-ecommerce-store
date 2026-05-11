<x-mail::message>
# Welcome, {{ $user->name }}! 👋

Your account has been created. Start exploring our catalog and
enjoy a seamless shopping experience.

<x-mail::button :url="url('/')" color="primary">
Start Shopping
</x-mail::button>

Thanks for joining us!
</x-mail::message>