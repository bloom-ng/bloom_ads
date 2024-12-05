<x-guest-layout isAuth="true">
    <section class="flex flex-col w-full items-center bg-[url('/images/lines.png')] bg-cover bg-center mb-10 lg:mb-16">
        <div
            class="bg-gradient-to-r from-[#FFE5C680] to-[#FFBB6780] p-10 md:p-16 lg:p-20 lg:w-[40%] rounded-3xl items-center text-center mt-10 mx-5 md:mx-0 md:mt-16 lg:mt-24">
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-black mb-5">Bloom Ads for <br>Partners</h1>
            <p class="text-2xl font-light mb-10">Please enter a valid & active email address</p>

            <!-- OAuth Buttons -->
            <div class="flex flex-col gap-4 mb-8">
                <a href="/auth/google"
                    class="flex items-center justify-center gap-2 p-2 border rounded-xl bg-white hover:bg-gray-50">
                    <img src="/images/google.svg" alt="google logo">
                    <span>Continue with Google</span>
                </a>
                <a href="/auth/facebook"
                    class="flex items-center justify-center gap-2 p-2 border rounded-xl bg-[#1877F2] hover:bg-[#1877F9] text-white">
                    <img src="/images/facebook.svg" alt="facebook logo">
                    <span>Continue with Facebook</span>
                </a>
            </div>

            <div class="relative flex items-center justify-center mb-8">
                <hr class="w-full border-gray-300">
                <span class="absolute px-3 bg-gradient-to-r from-[#FFE5C680] to-[#FFBB6780]">or</span>
            </div>

            <form action="{{ route('signup.register') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="user_type" value="partner">

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

                <button type="submit" class="w-full bg-[#FF8C00] text-white px-5 py-3 rounded-xl hover:bg-[#e67e00]">
                    Sign Up
                </button>
            </form>
        </div>
    </section>
</x-guest-layout>
