<x-guest-layout :isAuth="true">
    <section class="flex flex-col w-full items-center bg-[url('/images/lines.png')] bg-cover bg-center mb-10 lg:mb-16">
        <div
            class="bg-gradient-to-r from-[#E6E6F366] to-[#6666B366] p-10 md:p-16 lg:p-20 lg:w-[40%] rounded-3xl items-center text-center mt-10 mx-5 md:mx-0 md:mt-16 lg:mt-24">
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-black mb-5">Bloom Ads for <br>Direct
                Advertisers</h1>

            <!-- OAuth Buttons -->
            <div class="flex flex-col gap-4 mb-8">
                <a href="/auth/google?user_type=direct_advertiser"
                    class="flex flex-row space-x-3 text-sm font-semibold text-center items-center justify-center text-white bg-[#181818] rounded-xl px-8 p-2">
                    <img src="/images/google.svg" alt="google logo">
                    <span>Continue with Google</span>
                </a>
                <a href="/auth/facebook?user_type=direct_advertiser"
                    class="flex items-center justify-center gap-2 p-2 border rounded-xl bg-[#1877F2] hover:bg-[#1877F9] text-white">
                    <img src="/images/facebook.svg" alt="facebook logo">
                    <span>Continue with Facebook</span>
                </a>
            </div>

            <div class="relative flex items-center justify-center mb-8">
                <hr class="w-full border-gray-300">
                <span class="absolute px-3 bg-gradient-to-r from-[#E6E6F366] to-[#6666B366]">or</span>
            </div>

            <form action="{{ route('signup.register') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="user_type" value="direct_advertiser">

                <div class="mb-3">
                    <input type="text" name="name"
                        class="w-full py-3 px-5 border border-[#000000] rounded-xl bg-transparent text-black"
                        placeholder="Name & Surname" required>
                </div>

                <div class="mb-3">
                    <input type="text" name="business_name"
                        class="w-full py-3 px-5 border border-[#000000] rounded-xl bg-transparent text-black"
                        placeholder="Your Business Name" required>
                </div>

                <div class="mb-3">
                    <input type="email" name="email"
                        class="w-full py-3 px-5 border border-[#000000] rounded-xl bg-transparent text-black"
                        placeholder="Valid Email Address" required>
                </div>

                <div class="flex flex-row mb-3 gap-3">
                    <div class="relative w-1/2">
                        <label for="country_code" class="sr-only">Country Code</label>
                        <select id="country_code" name="country_code"
                            class="w-full pr-10 px-3 py-3 border border-[#000000] rounded-xl bg-transparent text-black">
                            @foreach ($countryCodes as $countryCode)
                                <option value="{{ $countryCode['code'] }}">
                                    {{ $countryCode['code'] }} ({{ $countryCode['name'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full">
                        <label for="phone_number" class="sr-only">Phone Number</label>
                        <input type="tel" id="phone_number" name="phone_number"
                            class="w-full py-3 px-5 border border-[#000000] rounded-xl bg-transparent text-black"
                            placeholder="Phone Number" required>
                    </div>
                </div>

                <div class="mb-3">
                    <input type="url" name="weblink"
                        class="w-full py-3 px-5 border border-[#000000] rounded-xl bg-transparent text-black"
                        placeholder="Website Link" required>
                </div>

                <div class="mb-3">
                    <label for="country" class="sr-only">Country</label>
                    <select id="country" name="country"
                        class="w-full pr-10 px-5 py-3 border border-[#000000] rounded-xl bg-transparent text-black">
                        @foreach ($countries as $country)
                            <option value="{{ $country['name']['common'] }}" data-flag="{{ $country['flags']['png'] }}">
                                {{ $country['name']['common'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <input type="password" name="password"
                        class="w-full py-3 px-5 border border-[#000000] rounded-xl bg-transparent text-black"
                        placeholder="Password" required>
                </div>

                <button type="submit" class="w-full bg-[#000080] text-white px-5 py-3 rounded-xl hover:bg-[#000050]">
                    Sign Up
                </button>
            </form>

            <p class="mt-4">Already have an account? <a href="/login"
                    class="text-[#000080] hover:underline">Login</a></p>
        </div>
    </section>
</x-guest-layout>
