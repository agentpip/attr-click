<x-layout>
    <section class="mx-auto max-w-md">
        <flux:heading size="xl">Sign in</flux:heading>
        <flux:subheading class="mt-2">We’ll email a one-use link to your verified address.</flux:subheading>

        <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
            @csrf
            <flux:field>
                <flux:label>Email address</flux:label>
                <flux:input name="email" type="email" autocomplete="email" :value="old('email')" required autofocus />
                <flux:error name="email" />
            </flux:field>

            <flux:button type="submit" variant="primary" class="w-full">Email me a sign-in link</flux:button>
        </form>
    </section>
</x-layout>
