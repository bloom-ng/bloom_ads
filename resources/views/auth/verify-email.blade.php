<x-guest-layout isAuth="true">
    <section class="flex flex-col w-full items-center bg-[url('/images/lines.png')] bg-cover bg-center mb-10 lg:mb-16">
        <div
            class="bg-gradient-to-r from-[#FFE5C680] to-[#FFBB6780] p-10 md:p-16 lg:p-20 lg:w-[40%] rounded-3xl items-center text-center mt-10 mx-5 md:mx-0 md:mt-16 lg:mt-24">
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-black mb-5">Verify Your Email</h1>
            <p class="text-xl mb-8">Thanks for signing up! Before getting started, could you verify your email address by
                clicking on the link we just emailed to you?</p>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-8 text-green-600">
                    A new verification link has been sent to your email address.
                </div>
            @endif

            <div class="flex flex-col space-y-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit"
                        class="w-full bg-[#FF8C00] text-white px-5 py-3 rounded-xl hover:bg-[#e67e00]">
                        Resend Verification Email
                    </button>
                </form>

                {{-- <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full bg-gray-200 text-gray-700 px-5 py-3 rounded-xl hover:bg-gray-300">
                        Log Out
                    </button>
                </form> --}}
            </div>
        </div>
    </section>
</x-guest-layout>
