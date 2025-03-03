<x-guest-layout isAuth="true">
    <section class="flex flex-col w-full items-center bg-[url('/images/lines.png')] bg-cover bg-center mb-10 lg:mb-16">
        <div
            class="bg-gradient-to-r from-[#E6E6F366] to-[#6666B366] p-20 lg:w-[40%] rounded-3xl items-center text-center mt-24">
            <h1 class="text-5xl font-bold text-black mb-5">Billing for <br>Agencies</h1>
            <p class="text-2xl font-light mb-10">Please enter a valid & active email address</p>

            <!-- OAuth Buttons -->
            <!-- <div class="flex flex-col gap-4 mb-8">
                <a href="/auth/google?user_type=agency"
                    class="flex flex-row space-x-3 text-sm font-semibold text-center items-center justify-center text-white bg-[#181818] rounded-xl px-8 p-2">
                    <img src="/images/google.svg" alt="google logo">
                    <span>Continue with Google</span>
                </a>
                <a href="/auth/facebook?user_type=agency"
                    class="flex items-center justify-center gap-2 p-2 border rounded-xl bg-[#1877F2] hover:bg-[#1877F9] text-white">
                    <img src="/images/facebook.svg" alt="facebook logo">
                    <span>Continue with Facebook</span>
                </a>
            </div> -->

            <!-- <div class="relative flex items-center justify-center mb-8">
                <hr class="w-full border-gray-300">
                <span class="absolute px-3 bg-gradient-to-r from-[#E6E6F366] to-[#6666B366]">or</span>
            </div> -->

            <form action="{{ route('signup.register') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="user_type" value="agency">

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

                <div class="mb-3 relative">
                    <input type="password" name="password"
                        class="w-full py-3 px-5 border border-[#000000] rounded-xl bg-transparent text-black"
                        placeholder="Password" required id="signupPassword2">
                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600"
                        onclick="togglePasswordVisibility('signupPassword2', 'signupPasswordToggleIcon2')">
                        <span id="signupPasswordToggleIcon2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                    </button>
                </div>

                <div class="mb-3">
                    <input type="password" name="password_confirmation"
                        class="w-full py-3 px-5 border border-[#000000] rounded-xl bg-transparent text-black"
                        placeholder="Confirm Password" required>
                </div>

                <div class="mb-3 flex items-start space-x-2">
                    <input type="checkbox" name="terms_accepted" id="terms_accepted" class="mt-1" required>
                    <label for="terms_accepted" class="text-sm text-gray-700">
                        I agree to the <a href="#" class="text-[#000080] hover:underline">Terms and Conditions</a> and 
                        <a href="#" class="text-[#000080] hover:underline">Privacy Policy</a>
                    </label>
                </div>
                @error('terms_accepted')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

                <button type="submit" class="w-full bg-[#000080] text-white px-5 py-3 rounded-xl hover:bg-[#000050]">
                    Sign Up
                </button>
            </form>
            <p class="font-semibold mt-5">Already Collaborating With Us? <a href="/login"><span
                        class="text-[#000080]">Login</span> </a>
            </p>
        </div>
    </section>
</x-guest-layout>
