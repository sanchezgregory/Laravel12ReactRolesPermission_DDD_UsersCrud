<x-mail::message>
# Hello {{ $user->name }},

We have a special gift for you!

You have received a coupon for **{{ $coupon->discount_percentage }}% OFF**.

Use the code below at checkout:

<x-mail::panel>
{{ $coupon->code }}
</x-mail::panel>

This coupon is valid until {{ $coupon->expires_at->format('F j, Y') }}.
It can be used {{ $coupon->max_uses_per_user }} time(s).

<x-mail::button :url="config('app.url')">
Go to {{ config('app.name') }}
</x-mail::button>

Thanks,<br>
The {{ config('app.name') }} Team
</x-mail::message>
